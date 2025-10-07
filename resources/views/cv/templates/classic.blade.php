<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $cv->title }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Text:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
        @page {
            size: A4;
            margin: 0;
        }
        body {
            font-family: 'Crimson Text', Georgia, 'Times New Roman', serif;
            font-size: 10pt;
            line-height: 1.6;
            font-weight: 400;
            color: #1a1a1a;
        }
        h1 {
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        h2, h3, h4 {
            font-weight: 600;
        }
        .section-divider {
            border-top: 1px solid #333;
            margin: 1.5rem 0 1rem 0;
        }
        .contact-info {
            font-size: 9pt;
        }
    </style>
</head>
<body class="bg-white">
    <div class="w-full min-h-screen px-16 py-12">
        {{-- Header Section --}}
        @if($cv->headerInfo)
            <header class="text-center mb-8 pb-4 border-b-2 border-black">
                <h1 class="text-3xl mb-2">{{ $cv->headerInfo->full_name }}</h1>
                <h2 class="text-base mb-3 italic">
                    {{ $cv->headerInfo->job_title }}
                    @if(isset($profileName))
                        <span class="text-sm">({{ str_replace('-', ' ', ucwords($profileName, '-')) }} Focus)</span>
                    @endif
                </h2>

                <div class="contact-info flex justify-center flex-wrap gap-x-4 gap-y-1">
                    @if($cv->headerInfo->email)
                        <span>{{ $cv->headerInfo->email }}</span>
                    @endif
                    @if($cv->headerInfo->phone)
                        <span>{{ $cv->headerInfo->phone }}</span>
                    @endif
                    @if($cv->headerInfo->location)
                        <span>{{ $cv->headerInfo->location }}</span>
                    @endif
                </div>

                @if($cv->headerInfo->linkedin_url || $cv->headerInfo->github_url || $cv->headerInfo->website_url)
                    <div class="contact-info flex justify-center flex-wrap gap-x-4 mt-1">
                        @if($cv->headerInfo->linkedin_url)
                            <span>LinkedIn: {{ basename($cv->headerInfo->linkedin_url) }}</span>
                        @endif
                        @if($cv->headerInfo->github_url)
                            <span>GitHub: {{ basename($cv->headerInfo->github_url) }}</span>
                        @endif
                        @if($cv->headerInfo->website_url)
                            <span>{{ parse_url($cv->headerInfo->website_url, PHP_URL_HOST) }}</span>
                        @endif
                    </div>
                @endif
            </header>
        @endif

        {{-- Main Content --}}
        <div>
            @foreach($sections as $section)
                {{-- Summary Section --}}
                @if($section->section_type === 'summary' && $section->summary)
                    <section class="mb-6">
                        <h3 class="text-sm font-semibold uppercase tracking-wide mb-2">Professional Summary</h3>
                        <div class="section-divider"></div>
                        <p class="text-justify leading-relaxed whitespace-pre-line">{{ $section->summary->content }}</p>
                    </section>
                @endif

                {{-- Custom Section --}}
                @if($section->section_type === 'custom' && $section->customSection)
                    <section class="mb-6">
                        <h3 class="text-sm font-semibold uppercase tracking-wide mb-2">{{ $section->title }}</h3>
                        <div class="section-divider"></div>
                        <div class="leading-relaxed space-y-2">
                            @foreach(explode("\n\n", $section->customSection->content) as $paragraph)
                                @if(preg_match('/^\*\*(.*?)\*\*\s*-\s*(.*)$/', trim($paragraph), $matches))
                                    <div>
                                        <span class="font-semibold">{{ $matches[1] }}</span>
                                        <span> – {{ $matches[2] }}</span>
                                    </div>
                                @else
                                    <p>{{ $paragraph }}</p>
                                @endif
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Skills Section --}}
                @if($section->section_type === 'skills' && $section->skillCategories->count() > 0)
                    <section class="mb-6">
                        <h3 class="text-sm font-semibold uppercase tracking-wide mb-2">{{ $section->title }}</h3>
                        <div class="section-divider"></div>
                        <div class="space-y-1.5">
                            @foreach($section->skillCategories->sortBy('display_order') as $category)
                                <div class="flex gap-3">
                                    <div class="font-semibold w-44 flex-shrink-0">{{ $category->category_name }}:</div>
                                    <div class="flex-1">{{ implode(', ', $category->skills) }}</div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Experience Section --}}
                @if($section->section_type === 'experience' && $section->experiences->count() > 0)
                    <section class="mb-6">
                        <h3 class="text-sm font-semibold uppercase tracking-wide mb-2">Experience</h3>
                        <div class="section-divider"></div>
                        <div class="space-y-4">
                            @foreach($section->experiences->sortBy('display_order') as $experience)
                                <div>
                                    <div class="flex justify-between items-baseline mb-1">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-base">{{ $experience->job_title }}</h4>
                                            <div class="italic">{{ $experience->company_name }}@if($experience->location), {{ $experience->location }}@endif</div>
                                        </div>
                                        <span class="text-sm font-semibold whitespace-nowrap ml-4">
                                            {{ $experience->start_date->format('M Y') }} –
                                            @if($experience->is_current)
                                                Present
                                            @elseif($experience->end_date)
                                                {{ $experience->end_date->format('M Y') }}
                                            @endif
                                        </span>
                                    </div>
                                    <ul class="list-disc list-outside ml-5 space-y-1 leading-relaxed mt-2">
                                        @foreach($experience->highlights as $highlight)
                                            <li>{{ $highlight }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Projects Section --}}
                @if($section->section_type === 'projects' && $section->projects->count() > 0)
                    <section class="mb-6">
                        <h3 class="text-sm font-semibold uppercase tracking-wide mb-2">Projects</h3>
                        <div class="section-divider"></div>
                        <div class="space-y-3">
                            @foreach($section->projects->sortBy('display_order') as $project)
                                <div>
                                    <h4 class="font-semibold">{{ $project->project_name }}</h4>
                                    <p class="leading-relaxed mt-1">{{ $project->description }}</p>
                                    @if($project->technologies)
                                        <p class="text-sm italic mt-1">Technologies: {{ $project->technologies }}</p>
                                    @endif
                                    @if($project->project_url)
                                        <p class="text-sm mt-1">{{ $project->project_url }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Education Section --}}
                @if($section->section_type === 'education' && $section->education->count() > 0)
                    <section class="mb-6">
                        <h3 class="text-sm font-semibold uppercase tracking-wide mb-2">Education</h3>
                        <div class="section-divider"></div>
                        <div class="space-y-2">
                            @foreach($section->education->sortBy('display_order') as $edu)
                                <div class="flex justify-between items-baseline">
                                    <div class="flex-1">
                                        <span class="font-semibold">{{ $edu->degree }}</span>
                                        <span class="italic"> – {{ $edu->institution }}</span>
                                        @if($edu->description)
                                            <p class="mt-0.5">{{ $edu->description }}</p>
                                        @endif
                                    </div>
                                    <span class="text-sm font-semibold whitespace-nowrap ml-4">
                                        {{ $edu->start_year }}–{{ $edu->end_year ?? 'Present' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- References Section --}}
                @if($section->section_type === 'references' && $section->reference)
                    <section class="mb-6">
                        <h3 class="text-sm font-semibold uppercase tracking-wide mb-2">References</h3>
                        <div class="section-divider"></div>
                        <p class="italic">{{ $section->reference->content }}</p>
                    </section>
                @endif
            @endforeach
        </div>
    </div>
</body>
</html>
