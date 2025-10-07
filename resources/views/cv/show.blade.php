<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $cv->title }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        accent: '#0369a1', // sky-700
                    }
                }
            }
        }
    </script>
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
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica', 'Arial', sans-serif;
            font-size: 9pt;
            line-height: 1.5;
            font-weight: 300;
        }
        h1, h2, h3, h4 {
            font-weight: 600;
        }
        .section-header {
            position: relative;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }
        .section-header::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 3rem;
            height: 2px;
            background: #0369a1;
        }
        .icon {
            width: 0.875rem;
            height: 0.875rem;
            display: inline-block;
            vertical-align: middle;
        }
    </style>
</head>
<body class="bg-white text-gray-900 antialiased">
    <div class="w-full min-h-screen">
        {{-- Header Section --}}
        @if($cv->headerInfo)
            <header class="relative px-12 pt-12 pb-8 bg-gradient-to-br from-slate-50 to-white border-b border-slate-200">
                <div class="absolute top-0 right-0 w-64 h-64 bg-accent/5 rounded-full -mr-32 -mt-32"></div>
                <div class="relative">
                    <h1 class="text-4xl font-bold mb-1 tracking-tight text-gray-900">{{ $cv->headerInfo->full_name }}</h1>
                    <h2 class="text-lg font-medium text-accent mb-4">
                        {{ $cv->headerInfo->job_title }}
                        @if(isset($profileName))
                            <span class="text-xs text-gray-500 font-normal ml-2">({{ str_replace('-', ' ', ucwords($profileName, '-')) }} Focus)</span>
                        @endif
                    </h2>

                    <div class="flex flex-wrap gap-x-5 gap-y-1.5 text-xs text-gray-600">
                        @if($cv->headerInfo->email)
                            <a href="mailto:{{ $cv->headerInfo->email }}" class="flex items-center gap-1.5 hover:text-accent transition-colors">
                                <svg class="icon text-accent" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                </svg>
                                {{ $cv->headerInfo->email }}
                            </a>
                        @endif
                        @if($cv->headerInfo->phone)
                            <a href="tel:{{ $cv->headerInfo->phone }}" class="flex items-center gap-1.5 hover:text-accent transition-colors">
                                <svg class="icon text-accent" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                </svg>
                                {{ $cv->headerInfo->phone }}
                            </a>
                        @endif
                        @if($cv->headerInfo->location)
                            <span class="flex items-center gap-1.5">
                                <svg class="icon text-accent" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                </svg>
                                {{ $cv->headerInfo->location }}
                            </span>
                        @endif
                        @if($cv->headerInfo->linkedin_url)
                            <a href="{{ $cv->headerInfo->linkedin_url }}" class="flex items-center gap-1.5 hover:text-accent transition-colors">
                                <svg class="icon text-accent" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                </svg>
                                {{ basename($cv->headerInfo->linkedin_url) }}
                            </a>
                        @endif
                        @if($cv->headerInfo->github_url)
                            <a href="{{ $cv->headerInfo->github_url }}" class="flex items-center gap-1.5 hover:text-accent transition-colors">
                                <svg class="icon text-accent" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                                </svg>
                                {{ basename($cv->headerInfo->github_url) }}
                            </a>
                        @endif
                        @if($cv->headerInfo->website_url)
                            <a href="{{ $cv->headerInfo->website_url }}" class="flex items-center gap-1.5 hover:text-accent transition-colors">
                                <svg class="icon text-accent" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" />
                                </svg>
                                {{ parse_url($cv->headerInfo->website_url, PHP_URL_HOST) }}
                            </a>
                        @endif
                    </div>
                </div>
            </header>
        @endif

        {{-- Main Content --}}
        <div class="px-12 py-6">
            @foreach($sections as $section)
                {{-- Summary Section --}}
                @if($section->section_type === 'summary' && $section->summary)
                    <section class="mb-6">
                        <h3 class="section-header text-sm font-bold text-gray-900 uppercase tracking-wide">Professional Summary</h3>
                        <p class="text-gray-700 leading-relaxed text-justify whitespace-pre-line">{{ $section->summary->content }}</p>
                    </section>
                @endif

                {{-- Custom Section --}}
                @if($section->section_type === 'custom' && $section->customSection)
                    <section class="mb-6">
                        <h3 class="section-header text-sm font-bold text-gray-900 uppercase tracking-wide">{{ $section->title }}</h3>
                        <div class="text-gray-700 leading-relaxed space-y-1.5">
                            @foreach(explode("\n\n", $section->customSection->content) as $paragraph)
                                @if(preg_match('/^\*\*(.*?)\*\*\s*-\s*(.*)$/', trim($paragraph), $matches))
                                    <div>
                                        <span class="font-semibold text-gray-900">{{ $matches[1] }}</span>
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
                        <h3 class="section-header text-sm font-bold text-gray-900 uppercase tracking-wide">{{ $section->title }}</h3>
                        <div class="space-y-2">
                            @foreach($section->skillCategories->sortBy('display_order') as $category)
                                <div class="flex gap-3">
                                    <div class="font-medium text-accent w-44 flex-shrink-0">{{ $category->category_name }}</div>
                                    <div class="text-gray-700 flex-1">{{ implode(', ', $category->skills) }}</div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Experience Section --}}
                @if($section->section_type === 'experience' && $section->experiences->count() > 0)
                    <section class="mb-6">
                        <h3 class="section-header text-sm font-bold text-gray-900 uppercase tracking-wide">Experience</h3>
                        <div class="space-y-4">
                            @foreach($section->experiences->sortBy('display_order') as $experience)
                                <div class="relative pl-4 border-l-2 border-accent/30">
                                    <div class="flex justify-between items-baseline mb-1">
                                        <div>
                                            <h4 class="font-semibold text-gray-900">{{ $experience->job_title }}</h4>
                                            <div class="font-medium text-accent">{{ $experience->company_name }}@if($experience->location) · {{ $experience->location }}@endif</div>
                                            @if($experience->company_url)
                                                <a href="{{ $experience->company_url }}" class="flex items-center gap-1 mt-0.5 text-[7pt] text-gray-500 hover:text-accent transition-colors">
                                                    <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                                                    </svg>
                                                    <span>{{ preg_replace('#^https?://(www\.)?#', '', rtrim($experience->company_url, '/')) }}</span>
                                                </a>
                                            @endif
                                        </div>
                                        <span class="text-[8pt] text-gray-500 font-medium whitespace-nowrap ml-4">
                                            {{ $experience->start_date->format('M Y') }} –
                                            @if($experience->is_current)
                                                Present
                                            @elseif($experience->end_date)
                                                {{ $experience->end_date->format('M Y') }}
                                            @endif
                                        </span>
                                    </div>
                                    <ul class="list-disc list-outside ml-4 text-gray-700 space-y-0.5 leading-relaxed mt-2">
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
                        <h3 class="section-header text-sm font-bold text-gray-900 uppercase tracking-wide">Side Projects</h3>
                        <div class="space-y-3">
                            @foreach($section->projects->sortBy('display_order') as $project)
                                <div class="bg-slate-50/50 p-3 rounded-sm border-l-2 border-accent">
                                    <h4 class="font-semibold text-gray-900 mb-1">{{ $project->project_name }}</h4>
                                    @if($project->project_url)
                                        <a href="{{ $project->project_url }}" class="flex items-center gap-1 mb-1 text-[7pt] text-gray-500 hover:text-accent transition-colors">
                                            <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                                            </svg>
                                            <span>{{ preg_replace('#^https?://(www\.)?#', '', rtrim($project->project_url, '/')) }}</span>
                                        </a>
                                    @endif
                                    <p class="text-gray-700 leading-relaxed mb-1">{{ $project->description }}</p>
                                    @if($project->technologies)
                                        <p class="text-xs text-accent font-medium">{{ $project->technologies }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Education Section --}}
                @if($section->section_type === 'education' && $section->education->count() > 0)
                    <section class="mb-6">
                        <h3 class="section-header text-sm font-bold text-gray-900 uppercase tracking-wide">Education</h3>
                        <div class="space-y-2">
                            @foreach($section->education->sortBy('display_order') as $edu)
                                <div class="flex justify-between items-baseline">
                                    <div>
                                        <span class="font-semibold text-gray-900">{{ $edu->degree }}</span>
                                        <span class="text-accent"> · {{ $edu->institution }}</span>
                                        @if($edu->description)
                                            <p class="text-gray-600 mt-0.5">{{ $edu->description }}</p>
                                        @endif
                                    </div>
                                    <span class="text-xs text-gray-500 font-medium whitespace-nowrap ml-4">
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
                        <h3 class="section-header text-sm font-bold text-gray-900 uppercase tracking-wide">References</h3>
                        <p class="text-gray-700 italic">{{ $section->reference->content }}</p>
                    </section>
                @endif
            @endforeach
        </div>
    </div>
</body>
</html>
