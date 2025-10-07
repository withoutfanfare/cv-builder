<?php

namespace App\Services;

class KeywordScoringService
{
    /**
     * Calculate prominence-weighted keyword scores
     *
     * @param  string  $jobDescription  The job description text
     * @param  string  $customWeights  Custom weights (future use)
     * @return array<string, float> Keyword scores
     */
    public function calculateProminenceScore(string $jobDescription, string $customWeights = ''): array
    {
        $lines = explode("\n", $jobDescription);

        // Parse sections
        $title = '';
        $intro = '';
        $body = '';

        // First non-empty line is title
        foreach ($lines as $index => $line) {
            $trimmed = trim($line);
            if ($trimmed !== '') {
                $title = $trimmed;
                unset($lines[$index]);
                break;
            }
        }

        // Next paragraph (until double newline or end) is intro
        $introLines = [];
        $foundIntro = false;
        $emptyLineCount = 0;

        foreach ($lines as $index => $line) {
            $trimmed = trim($line);

            if (! $foundIntro && $trimmed !== '') {
                $foundIntro = true;
            }

            if ($foundIntro) {
                if ($trimmed === '') {
                    $emptyLineCount++;
                    if ($emptyLineCount >= 1) {
                        // End of intro paragraph
                        unset($lines[$index]);
                        break;
                    }
                } else {
                    $introLines[] = $trimmed;
                    $emptyLineCount = 0;
                }
                unset($lines[$index]);
            }
        }

        $intro = implode(' ', $introLines);
        $body = implode(' ', array_map('trim', $lines));

        // Extract keywords and apply weighting
        $scores = [];
        $keywordMap = []; // Track original case of keywords

        // Weight: 3x for title
        foreach ($this->extractKeywords($title) as $keyword) {
            $normalizedKey = $this->normalizeKeyword($keyword);
            $scores[$normalizedKey] = ($scores[$normalizedKey] ?? 0) + 3.0;
            if (! isset($keywordMap[$normalizedKey])) {
                $keywordMap[$normalizedKey] = $keyword;
            }
        }

        // Weight: 2x for intro
        foreach ($this->extractKeywords($intro) as $keyword) {
            $normalizedKey = $this->normalizeKeyword($keyword);
            $scores[$normalizedKey] = ($scores[$normalizedKey] ?? 0) + 2.0;
            if (! isset($keywordMap[$normalizedKey])) {
                $keywordMap[$normalizedKey] = $keyword;
            }
        }

        // Weight: 1x for body
        foreach ($this->extractKeywords($body) as $keyword) {
            $normalizedKey = $this->normalizeKeyword($keyword);
            $scores[$normalizedKey] = ($scores[$normalizedKey] ?? 0) + 1.0;
            if (! isset($keywordMap[$normalizedKey])) {
                $keywordMap[$normalizedKey] = $keyword;
            }
        }

        // Return scores with original case from first occurrence
        $result = [];
        foreach ($scores as $normalizedKey => $score) {
            $originalCase = $keywordMap[$normalizedKey];
            $result[$originalCase] = $score;
        }

        return $result;
    }

    /**
     * Extract meaningful keywords from text
     *
     * @return array<string>
     */
    private function extractKeywords(string $text): array
    {
        // Remove punctuation except hyphens in compound words
        $text = preg_replace('/[^\w\s\-]/', ' ', $text);

        // Split into words
        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        // Filter out common words and short words
        $stopWords = [
            'the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for',
            'of', 'with', 'by', 'from', 'as', 'is', 'was', 'are', 'were', 'be',
            'been', 'being', 'have', 'has', 'had', 'do', 'does', 'did', 'will',
            'would', 'could', 'should', 'may', 'might', 'must', 'can', 'this',
            'that', 'these', 'those', 'you', 'your', 'we', 'our', 'it', 'its',
        ];

        $keywords = [];
        foreach ($words as $word) {
            $word = trim($word);
            $lowerWord = strtolower($word);

            if (strlen($word) >= 2 && ! in_array($lowerWord, $stopWords)) {
                // Preserve original case for first occurrence, but group case-insensitively
                $keywords[] = $word;
            }
        }

        return $keywords;
    }

    /**
     * Normalize keyword for case-insensitive matching
     */
    private function normalizeKeyword(string $keyword): string
    {
        return strtolower($keyword);
    }
}
