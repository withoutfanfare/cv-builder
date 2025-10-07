<?php

namespace App\Services;

class KeywordCoverageService
{
    private const STOPWORDS = [
        // Articles, conjunctions, prepositions
        'the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for',
        'of', 'with', 'by', 'from', 'as', 'is', 'was', 'are', 'be', 'been',
        'being', 'have', 'has', 'had', 'do', 'does', 'did', 'will', 'would',
        'should', 'could', 'may', 'might', 'must', 'can', 'this', 'that',
        'these', 'those', 'i', 'you', 'he', 'she', 'it', 'we', 'they',
        // Generic job posting words
        'experience', 'experienced', 'years', 'year', 'month', 'months', 'day', 'days',
        'strong', 'good', 'excellent', 'great', 'proven', 'solid', 'deep', 'extensive',
        'ability', 'skills', 'skill', 'knowledge', 'understanding', 'proficiency', 'proficient',
        'required', 'requiring', 'requires', 'requirement', 'requirements', 'preferred', 'preferably',
        'looking', 'seeking', 'need', 'needs', 'needed', 'must', 'should', 'ideal', 'ideally',
        'working', 'work', 'worked', 'works', 'role', 'position', 'job', 'responsibilities',
        'candidate', 'candidates', 'applicant', 'applicants', 'team', 'teams', 'company', 'organization',
        'our', 'your', 'their', 'his', 'her', 'its', 'us', 'them',
        'using', 'use', 'used', 'uses', 'including', 'include', 'includes', 'such',
        'well', 'very', 'highly', 'able', 'demonstrated', 'demonstrate', 'apply', 'application',
        'develop', 'developer', 'development', 'engineer', 'engineering', 'senior', 'junior', 'lead',
        'all', 'any', 'some', 'each', 'every', 'other', 'another', 'more', 'most', 'less', 'least',
        'both', 'either', 'neither', 'not', 'no', 'yes', 'who', 'what', 'where', 'when', 'why', 'how',
        'about', 'above', 'across', 'after', 'against', 'along', 'among', 'around', 'before', 'behind',
        'below', 'beneath', 'beside', 'between', 'beyond', 'down', 'during', 'except', 'inside', 'into',
        'like', 'near', 'off', 'onto', 'out', 'outside', 'over', 'through', 'throughout', 'till', 'toward',
        'under', 'until', 'up', 'upon', 'within', 'without',
    ];

    /**
     * Tokenize text by removing stopwords and short tokens
     */
    public function tokenize(string $text): array
    {
        // Lowercase and remove punctuation
        $cleaned = strtolower(preg_replace('/[^\w\s]/', '', $text));

        // Split on whitespace
        $tokens = preg_split('/\s+/', $cleaned, -1, PREG_SPLIT_NO_EMPTY);

        // Remove stopwords and short tokens (< 3 characters)
        return array_filter($tokens, fn ($token) => ! in_array($token, self::STOPWORDS) && strlen($token) > 2
        );
    }

    /**
     * Calculate keyword coverage between job description and CV content
     */
    public function calculateCoverage(string $jobDescription, string $cvContent): array
    {
        // Tokenize and get unique keywords
        $jobKeywords = array_unique($this->tokenize($jobDescription));
        $cvKeywords = array_unique($this->tokenize($cvContent));

        // Calculate matches and missing
        $matched = array_intersect($jobKeywords, $cvKeywords);
        $missing = array_diff($jobKeywords, $cvKeywords);

        // Calculate coverage percentage
        $totalJobKeywords = count($jobKeywords);
        $coverage = $totalJobKeywords > 0
            ? (count($matched) / $totalJobKeywords) * 100
            : 0;

        return [
            'coverage_percentage' => round($coverage, 2),
            'missing_keywords' => array_values(array_slice($missing, 0, 20)), // Top 20 only
            'matched_count' => count($matched),
            'total_job_keywords' => $totalJobKeywords,
        ];
    }
}
