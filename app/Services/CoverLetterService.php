<?php

namespace App\Services;

class CoverLetterService
{
    /**
     * Interpolate template variables with actual values
     *
     * @param  string  $template  Template with {{variable}} placeholders
     * @param  array<string, string>  $variables  Key-value pairs for replacement
     * @return string Interpolated text
     */
    public function interpolate(string $template, array $variables): string
    {
        $result = $template;

        foreach ($variables as $key => $value) {
            $placeholder = '{{'.$key.'}}';
            $result = str_replace($placeholder, $value, $result);
        }

        return $result;
    }
}
