<?php

namespace App\Services;

use App\Exceptions\IncompleteCvException;
use App\Exceptions\InvalidResponseException;
use App\Exceptions\MissingJobDescriptionException;
use App\Exceptions\OpenAiApiException;
use App\Models\Cv;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Http;

class CvReviewService
{
    public function analyzeForJob(Cv $cv, JobApplication $jobApplication): array
    {
        if (empty($jobApplication->job_description) || strlen($jobApplication->job_description) < 50) {
            throw new MissingJobDescriptionException('Job description is required for CV analysis');
        }

        $hasExperiences = is_array($cv->getExperiencesList()) && count($cv->getExperiencesList()) > 0;
        $hasSkills = is_array($cv->getSkillsList()) && count($cv->getSkillsList()) > 0;

        if (! $hasExperiences && ! $hasSkills) {
            throw new IncompleteCvException('CV must have at least one experience or skill for analysis');
        }

        $jobRequirements = $this->extractJobRequirements($jobApplication->job_description);

        $cvData = [
            'skills' => $cv->getSkillsList(),
            'experiences' => $cv->getExperiencesList(),
            'education' => $cv->getEducationList(),
            'highlights' => $cv->getHighlightsList(),
        ];

        $messages = $this->buildAnalysisPrompt($cvData, $jobRequirements, $jobApplication->job_description);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.config('services.openai.api_key'),
            ])
                ->timeout(60)
                ->retry(3, 100)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => config('services.openai.model'),
                    'messages' => $messages,
                    'temperature' => 0.3,
                    'response_format' => ['type' => 'json_object'],
                ]);

            if (! $response->successful()) {
                throw new OpenAiApiException('Failed to complete CV analysis');
            }

            $data = $response->json();
            $reviewData = json_decode($data['choices'][0]['message']['content'], true);

            $reviewData['analysis_metadata'] = [
                'generated_at' => now()->toIso8601String(),
                'model_used' => config('services.openai.model'),
                'tokens_used' => $data['usage']['total_tokens'] ?? 0,
                'prompt_version' => '1.0',
            ];

            if (! $this->validateReviewData($reviewData)) {
                throw new InvalidResponseException('Invalid response from analysis service');
            }

            return $reviewData;
        } catch (\Exception $e) {
            if ($e instanceof OpenAiApiException || $e instanceof InvalidResponseException) {
                throw $e;
            }
            throw new OpenAiApiException('Failed to complete CV analysis: '.$e->getMessage(), 0, $e);
        }
    }

    public function extractJobRequirements(string $jobDescription): array
    {
        if (empty($jobDescription)) {
            throw new \InvalidArgumentException('Job description cannot be empty');
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.config('services.openai.api_key'),
            ])
                ->timeout(30)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => config('services.openai.model'),
                    'messages' => [
                        ['role' => 'system', 'content' => 'Extract job requirements as JSON: {skills, competencies, keywords, experience_level, role_focus}'],
                        ['role' => 'user', 'content' => $jobDescription],
                    ],
                    'temperature' => 0.2,
                    'response_format' => ['type' => 'json_object'],
                ]);

            return json_decode($response->json()['choices'][0]['message']['content'], true);
        } catch (\Exception $e) {
            return ['skills' => [], 'competencies' => [], 'keywords' => [], 'experience_level' => 'mid', 'role_focus' => []];
        }
    }

    public function calculateMatchScore(array $cvData, array $jobRequirements): int
    {
        $score = 0;
        $score += $this->calculateSkillsMatch($cvData['skills'] ?? [], $jobRequirements['skills'] ?? []) * 0.4;
        $score += $this->calculateExperienceRelevance($cvData['experiences'] ?? [], $jobRequirements) * 0.3;
        $score += $this->calculateKeywordMatch($cvData, $jobRequirements['keywords'] ?? []) * 0.2;
        $score += $this->calculateEvidenceQuality($cvData) * 0.1;

        return max(0, min(100, (int) round($score)));
    }

    public function estimateTokenCount(Cv $cv, JobApplication $jobApplication): int
    {
        $cvWords = str_word_count(json_encode($cv->toArray()));
        $jobWords = str_word_count($jobApplication->job_description ?? '');

        return (int) (($cvWords + $jobWords) * 1.3 + 500 + 2000);
    }

    public function estimateCostCents(int $estimatedTokens): int
    {
        $inputCost = ($estimatedTokens * 0.4 / 1000) * 1;
        $outputCost = ($estimatedTokens * 0.6 / 1000) * 3;

        return (int) ceil($inputCost + $outputCost);
    }

    private function buildAnalysisPrompt(array $cvData, array $jobRequirements, string $jobDescription): array
    {
        $systemPrompt = 'You are a CV optimization consultant. Analyze the CV against the job description and return JSON with: schema_version (1.0), match_score (0-100), skill_gaps, section_recommendations, bullet_improvements, language_suggestions, skill_evidence, action_checklist. Use priority levels: high/medium/low.

IMPORTANT for language_suggestions: Return SPECIFIC text replacements, not general advice. Each suggestion must have:
- "original": the exact text from the CV that needs improvement
- "improvement": the specific replacement text
- "priority": high/medium/low
- "reason": why this change improves alignment with the job

Example: {"original": "Worked on projects", "improvement": "Led cross-functional projects", "priority": "high", "reason": "Uses stronger action verb and shows leadership"}';

        return [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => "Job:\n{$jobDescription}\n\nCV:\n".json_encode($cvData)],
        ];
    }

    private function validateReviewData(array $data): bool
    {
        $required = ['schema_version', 'match_score', 'skill_gaps', 'section_recommendations', 'bullet_improvements', 'language_suggestions', 'skill_evidence', 'action_checklist'];
        foreach ($required as $key) {
            if (! array_key_exists($key, $data)) {
                return false;
            }
        }

        return is_int($data['match_score']) && $data['match_score'] >= 0 && $data['match_score'] <= 100;
    }

    private function calculateSkillsMatch(array $cvSkills, array $requiredSkills): int
    {
        if (empty($requiredSkills)) {
            return 70;
        }
        $matches = 0;
        foreach ($requiredSkills as $required) {
            foreach ($cvSkills as $cvSkill) {
                if (stripos($cvSkill, $required) !== false || stripos($required, $cvSkill) !== false) {
                    $matches++;
                    break;
                }
            }
        }

        return (int) (($matches / count($requiredSkills)) * 100);
    }

    private function calculateExperienceRelevance(array $experiences, array $jobRequirements): int
    {
        if (empty($experiences)) {
            return 30;
        }
        $score = 60;
        foreach ($experiences as $exp) {
            foreach ($jobRequirements['role_focus'] ?? [] as $focus) {
                if (stripos($exp['title'] ?? '', $focus) !== false) {
                    $score += 10;
                    break;
                }
            }
        }

        return min(100, $score);
    }

    private function calculateKeywordMatch(array $cvData, array $keywords): int
    {
        if (empty($keywords)) {
            return 70;
        }
        $cvText = json_encode($cvData);
        $matches = array_filter($keywords, fn ($k) => stripos($cvText, $k) !== false);

        return (int) ((count($matches) / count($keywords)) * 100);
    }

    private function calculateEvidenceQuality(array $cvData): int
    {
        $score = 50;
        if (! empty($cvData['experiences'])) {
            $score += 15;
        }
        if (! empty($cvData['education'])) {
            $score += 10;
        }
        if (! empty($cvData['highlights'])) {
            $score += 15;
        }

        return min(100, $score);
    }
}
