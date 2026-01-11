<?php

namespace App\Services;

use App\Models\Cv;
use App\Models\CvExperience;
use App\Models\ExperienceSnippet;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SnippetGenerationService
{
    /**
     * Generate snippets from all experiences in a CV
     */
    public function generateFromCv(Cv $cv, bool $autoSave = false): array
    {
        $snippets = [];
        $errors = [];

        foreach ($cv->experiences as $experience) {
            try {
                $generated = $this->generateFromExperience($experience);

                if ($autoSave) {
                    foreach ($generated as $snippetData) {
                        $snippet = ExperienceSnippet::create($snippetData);
                        $snippets[] = $snippet;
                    }
                } else {
                    $snippets = array_merge($snippets, $generated);
                }
            } catch (\Exception $e) {
                Log::error('Failed to generate snippets from experience', [
                    'experience_id' => $experience->id,
                    'error' => $e->getMessage(),
                ]);

                $errors[] = [
                    'experience' => $experience->job_title . ' at ' . $experience->company_name,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'snippets' => $snippets,
            'errors' => $errors,
            'total_generated' => count($snippets),
        ];
    }

    /**
     * Generate snippets from a single experience
     */
    public function generateFromExperience(CvExperience $experience): array
    {
        $highlights = $experience->highlights ?? [];

        if (empty($highlights)) {
            return [];
        }

        // Prepare context for AI
        $context = [
            'job_title' => $experience->job_title,
            'company_name' => $experience->company_name,
            'highlights' => $highlights,
        ];

        // Call AI to categorize and tag
        $analyzed = $this->analyzeHighlights($context);

        $snippets = [];
        foreach ($analyzed as $snippet) {
            $snippets[] = [
                'title' => $snippet['title'] ?? $this->generateTitle($snippet['content']),
                'content' => $snippet['content'],
                'category' => $snippet['category'] ?? 'other',
                'tags' => $snippet['tags'] ?? [],
                'role_type' => $snippet['role_type'] ?? $this->inferRoleType($experience->job_title),
                'industry' => $snippet['industry'] ?? null,
                'usage_count' => 0,
            ];
        }

        return $snippets;
    }

    /**
     * Use AI to analyze and categorize highlights
     */
    private function analyzeHighlights(array $context): array
    {
        $prompt = $this->buildAnalysisPrompt($context);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.openai.api_key'),
            ])
                ->timeout(60)
                ->retry(3, 100)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => config('services.openai.model'),
                    'messages' => [
                        ['role' => 'system', 'content' => $prompt['system']],
                        ['role' => 'user', 'content' => $prompt['user']],
                    ],
                    'temperature' => 0.3,
                    'response_format' => ['type' => 'json_object'],
                ]);

            if (!$response->successful()) {
                throw new \Exception('OpenAI API request failed');
            }

            $data = $response->json();
            $result = json_decode($data['choices'][0]['message']['content'], true);

            return $result['snippets'] ?? [];
        } catch (\Exception $e) {
            Log::error('AI snippet analysis failed', [
                'error' => $e->getMessage(),
                'context' => $context,
            ]);

            // Fallback: create basic snippets without AI categorization
            return $this->createFallbackSnippets($context);
        }
    }

    /**
     * Build the AI analysis prompt
     */
    private function buildAnalysisPrompt(array $context): array
    {
        $system = <<<'PROMPT'
You are an expert CV analyzer. Extract achievement bullets from experience highlights and categorize them.

For each highlight, return:
- title: Short descriptive title (5-10 words)
- content: The full achievement bullet (keep original text, improve if needed)
- category: One of [leadership, technical, project_management, collaboration, problem_solving, innovation, optimization, mentorship, communication, other]
- tags: Array of relevant skills/technologies mentioned (3-8 tags)
- role_type: One of [engineer, senior_engineer, lead_engineer, architect, manager, product_manager, designer, data_scientist, devops, qa, other]
- industry: One of [tech, finance, healthcare, ecommerce, saas, consulting, education, other] or null

Return JSON: {"snippets": [{"title": "...", "content": "...", "category": "...", "tags": [...], "role_type": "...", "industry": "..."}]}

IMPORTANT: Each snippet should be a complete, standalone achievement that can be reused in other CVs.
PROMPT;

        $user = sprintf(
            "Job Title: %s\nCompany: %s\n\nHighlights:\n%s",
            $context['job_title'],
            $context['company_name'],
            implode("\n", array_map(fn($h, $i) => ($i + 1) . '. ' . $h, $context['highlights'], array_keys($context['highlights'])))
        );

        return [
            'system' => $system,
            'user' => $user,
        ];
    }

    /**
     * Create fallback snippets without AI categorization
     */
    private function createFallbackSnippets(array $context): array
    {
        $snippets = [];

        foreach ($context['highlights'] as $highlight) {
            $snippets[] = [
                'title' => $this->generateTitle($highlight),
                'content' => $highlight,
                'category' => 'other',
                'tags' => $this->extractSimpleTags($highlight),
                'role_type' => $this->inferRoleType($context['job_title']),
                'industry' => null,
            ];
        }

        return $snippets;
    }

    /**
     * Generate a simple title from content
     */
    private function generateTitle(string $content): string
    {
        // Take first 50 characters or until first period
        $title = substr($content, 0, 50);

        if (strpos($content, '.') !== false && strpos($content, '.') < 50) {
            $title = substr($content, 0, strpos($content, '.'));
        }

        return trim($title) . (strlen($content) > 50 ? '...' : '');
    }

    /**
     * Extract simple tags from text (fallback method)
     */
    private function extractSimpleTags(string $text): array
    {
        // Common tech terms and skills
        $keywords = [
            'React', 'Vue', 'Angular', 'TypeScript', 'JavaScript', 'Python', 'Java',
            'AWS', 'Azure', 'GCP', 'Docker', 'Kubernetes', 'CI/CD',
            'SQL', 'NoSQL', 'MongoDB', 'PostgreSQL', 'Redis',
            'API', 'REST', 'GraphQL', 'microservices', 'serverless',
            'leadership', 'team', 'project', 'agile', 'scrum',
            'performance', 'optimization', 'scalability', 'reliability',
        ];

        $found = [];
        foreach ($keywords as $keyword) {
            if (stripos($text, $keyword) !== false) {
                $found[] = $keyword;
            }
        }

        return array_slice($found, 0, 5); // Max 5 tags
    }

    /**
     * Infer role type from job title
     */
    private function inferRoleType(string $jobTitle): string
    {
        $title = strtolower($jobTitle);

        $roleMap = [
            'engineer' => ['engineer', 'developer', 'programmer'],
            'senior_engineer' => ['senior engineer', 'senior developer', 'sr engineer'],
            'lead_engineer' => ['lead engineer', 'lead developer', 'tech lead', 'technical lead'],
            'architect' => ['architect', 'principal engineer', 'staff engineer'],
            'manager' => ['manager', 'engineering manager', 'team lead'],
            'product_manager' => ['product manager', 'product owner', 'pm'],
            'designer' => ['designer', 'ux', 'ui', 'product designer'],
            'data_scientist' => ['data scientist', 'machine learning', 'ml engineer'],
            'devops' => ['devops', 'sre', 'platform engineer'],
            'qa' => ['qa', 'test engineer', 'quality assurance'],
        ];

        foreach ($roleMap as $roleType => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($title, $keyword)) {
                    return $roleType;
                }
            }
        }

        return 'other';
    }

    /**
     * Bulk generate snippets from multiple CVs
     */
    public function bulkGenerate(array $cvIds, bool $autoSave = false): array
    {
        $allSnippets = [];
        $allErrors = [];
        $totalGenerated = 0;

        foreach ($cvIds as $cvId) {
            $cv = Cv::find($cvId);

            if (!$cv) {
                continue;
            }

            $result = $this->generateFromCv($cv, $autoSave);

            $allSnippets = array_merge($allSnippets, $result['snippets']);
            $allErrors = array_merge($allErrors, $result['errors']);
            $totalGenerated += $result['total_generated'];
        }

        return [
            'snippets' => $allSnippets,
            'errors' => $allErrors,
            'total_generated' => $totalGenerated,
            'cvs_processed' => count($cvIds),
        ];
    }

    /**
     * Estimate cost for generating snippets from a CV
     */
    public function estimateCost(Cv $cv): int
    {
        $totalHighlights = 0;

        foreach ($cv->experiences as $experience) {
            $totalHighlights += count($experience->highlights ?? []);
        }

        // Rough estimate: ~500 tokens per experience analysis
        $estimatedTokens = $totalHighlights * 500;

        // Input: $0.004/1k tokens, Output: $0.03/1k tokens
        $inputCost = ($estimatedTokens * 0.4 / 1000);
        $outputCost = ($estimatedTokens * 0.6 / 1000) * 3;

        return (int) ceil($inputCost + $outputCost);
    }
}
