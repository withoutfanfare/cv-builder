<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidCvReviewSchema implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_array($value)) {
            $fail('The :attribute must be a valid JSON object.');

            return;
        }

        // Required top-level fields
        $requiredFields = [
            'schema_version',
            'match_score',
            'analysis_metadata',
            'skill_gaps',
            'section_recommendations',
            'bullet_improvements',
            'language_suggestions',
            'skill_evidence',
            'action_checklist',
        ];

        foreach ($requiredFields as $field) {
            if (! array_key_exists($field, $value)) {
                $fail("The :attribute is missing required field: {$field}");

                return;
            }
        }

        // Validate match_score is integer 0-100
        if (! is_int($value['match_score']) || $value['match_score'] < 0 || $value['match_score'] > 100) {
            $fail('The :attribute match_score must be an integer between 0 and 100.');

            return;
        }

        // Validate analysis_metadata structure
        if (! is_array($value['analysis_metadata'])) {
            $fail('The :attribute analysis_metadata must be an object.');

            return;
        }

        $requiredMetadata = ['generated_at', 'model_used', 'tokens_used', 'prompt_version'];
        foreach ($requiredMetadata as $field) {
            if (! array_key_exists($field, $value['analysis_metadata'])) {
                $fail("The :attribute analysis_metadata is missing required field: {$field}");

                return;
            }
        }

        // Validate arrays
        $arrayFields = ['skill_gaps', 'section_recommendations', 'bullet_improvements', 'language_suggestions', 'skill_evidence', 'action_checklist'];
        foreach ($arrayFields as $field) {
            if (! is_array($value[$field])) {
                $fail("The :attribute {$field} must be an array.");

                return;
            }
        }

        // Validate enum values in skill_gaps
        foreach ($value['skill_gaps'] as $index => $gap) {
            if (! is_array($gap)) {
                $fail("The :attribute skill_gaps[{$index}] must be an object.");

                return;
            }

            if (! isset($gap['priority']) || ! in_array($gap['priority'], ['high', 'medium', 'low'])) {
                $fail("The :attribute skill_gaps[{$index}].priority must be 'high', 'medium', or 'low'.");

                return;
            }
        }

        // Validate enum values in section_recommendations
        foreach ($value['section_recommendations'] as $index => $rec) {
            if (! is_array($rec)) {
                $fail("The :attribute section_recommendations[{$index}] must be an object.");

                return;
            }

            if (! isset($rec['impact']) || ! in_array($rec['impact'], ['high', 'medium', 'low'])) {
                $fail("The :attribute section_recommendations[{$index}].impact must be 'high', 'medium', or 'low'.");

                return;
            }
        }

        // Validate enum values in bullet_improvements
        foreach ($value['bullet_improvements'] as $index => $bullet) {
            if (! is_array($bullet)) {
                $fail("The :attribute bullet_improvements[{$index}] must be an object.");

                return;
            }

            if (! isset($bullet['priority']) || ! in_array($bullet['priority'], ['emphasize', 'keep', 'de-emphasize', 'remove'])) {
                $fail("The :attribute bullet_improvements[{$index}].priority must be 'emphasize', 'keep', 'de-emphasize', or 'remove'.");

                return;
            }
        }
    }
}
