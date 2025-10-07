<?php

use App\Services\CoverLetterService;

test('interpolates template variables correctly', function () {
    $service = app(CoverLetterService::class);

    $template = 'Dear {{company_name}}, I am applying for {{role_title}}.';
    $variables = [
        'company_name' => 'Acme Corp',
        'role_title' => 'Senior Developer',
    ];

    $result = $service->interpolate($template, $variables);

    expect($result)->toBe('Dear Acme Corp, I am applying for Senior Developer.');
});

test('handles missing variables by keeping placeholder', function () {
    $service = app(CoverLetterService::class);

    $template = 'Dear {{company_name}}, I love {{unknown_variable}}.';
    $variables = [
        'company_name' => 'Acme Corp',
    ];

    $result = $service->interpolate($template, $variables);

    expect($result)->toBe('Dear Acme Corp, I love {{unknown_variable}}.');
});

test('handles empty template', function () {
    $service = app(CoverLetterService::class);

    $result = $service->interpolate('', ['key' => 'value']);

    expect($result)->toBe('');
});

test('handles empty variables array', function () {
    $service = app(CoverLetterService::class);

    $template = 'Dear {{company_name}}';
    $result = $service->interpolate($template, []);

    expect($result)->toBe('Dear {{company_name}}');
});

test('handles multiple occurrences of same variable', function () {
    $service = app(CoverLetterService::class);

    $template = '{{name}} is great. I love {{name}}. {{name}} rocks!';
    $variables = ['name' => 'Laravel'];

    $result = $service->interpolate($template, $variables);

    expect($result)->toBe('Laravel is great. I love Laravel. Laravel rocks!');
});

test('handles nested braces with double mustache', function () {
    $service = app(CoverLetterService::class);

    $template = 'Test {{var}} and {not a variable}';
    $variables = [
        'var' => 'value',
    ];

    $result = $service->interpolate($template, $variables);

    // Only {{var}} should be replaced, single braces left alone
    expect($result)->toBe('Test value and {not a variable}');
});

test('preserves whitespace and newlines', function () {
    $service = app(CoverLetterService::class);

    $template = "Dear {{company}},\n\n  I am {{name}}.\n\nBest regards";
    $variables = [
        'company' => 'Acme',
        'name' => 'John',
    ];

    $result = $service->interpolate($template, $variables);

    expect($result)->toBe("Dear Acme,\n\n  I am John.\n\nBest regards");
});

test('handles special characters in variable values', function () {
    $service = app(CoverLetterService::class);

    $template = 'Dear {{company}}';
    $variables = [
        'company' => 'Acme & Co. (UK) Ltd.',
    ];

    $result = $service->interpolate($template, $variables);

    expect($result)->toBe('Dear Acme & Co. (UK) Ltd.');
});

test('handles complex template with all common variables', function () {
    $service = app(CoverLetterService::class);

    $template = "Dear Hiring Manager at {{company_name}},\n\n".
        "I am excited to apply for the {{role_title}} position. {{value_prop}}.\n\n".
        "Recently, {{recent_win}}. I believe this experience makes me an ideal candidate.\n\n".
        'Best regards';

    $variables = [
        'company_name' => 'Acme Corp',
        'role_title' => 'Senior Laravel Developer',
        'value_prop' => 'I bring 8 years of PHP development experience',
        'recent_win' => 'I led a team that reduced API response time by 60%',
    ];

    $result = $service->interpolate($template, $variables);

    expect($result)->toContain('Acme Corp')
        ->toContain('Senior Laravel Developer')
        ->toContain('I bring 8 years')
        ->toContain('reduced API response time by 60%')
        ->not->toContain('{{');
});
