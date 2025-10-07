<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $cv->title }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6366f1', // indigo-500
                        secondary: '#8b5cf6', // violet-500
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
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-size: 9pt;
            line-height: 1.4;
            font-weight: 400;
        }
        .sidebar {
            background: #0f172a;
        }
        .skill-badge {
            background: #f1f5f9;
            color: #334155;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 8pt;
            font-weight: 500;
        }
        .section-title {
            color: #0f172a;
            font-weight: 600;
            font-size: 11pt;
            margin-bottom: 0.75rem;
            padding-bottom: 0.25rem;
            border-bottom: 2px solid #f1f5f9;
        }
        ul li::marker {
            color: #2563eb;
        }
        .icon {
            width: 12px;
            height: 12px;
            display: inline-block;
            vertical-align: middle;
            margin-right: 6px;
            color: #94a3b8;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside class="sidebar w-64 flex-shrink-0 p-8 text-white self-stretch">
            @if($cv->headerInfo)
                <div class="mb-8">
                    <h1 class="text-2xl font-bold mb-1">{{ $cv->headerInfo->full_name }}</h1>
                    <h2 class="opacity-90 font-medium" style="font-size: 9.5pt;">
                        {{ $cv->headerInfo->job_title }}
                        @if(isset($profileName))
                            <div class="text-xs mt-1 opacity-75">({{ str_replace('-', ' ', ucwords($profileName, '-')) }} Focus)</div>
                        @endif
                    </h2>
                </div>

                <div class="space-y-4" style="font-size: 7.5pt;">
                    {{-- Contact Info --}}
                    <div>
                        <h3 class="font-semibold mb-2 text-blue-600" style="font-size: 8pt;">Contact</h3>
                        <div class="space-y-2 opacity-90">
                            @if($cv->headerInfo->email)
                                <div class="break-all flex items-start">
                                    <svg class="icon flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <a href="mailto:{{ $cv->headerInfo->email }}" class="text-white hover:underline">{{ $cv->headerInfo->email }}</a>
                                </div>
                            @endif
                            @if($cv->headerInfo->phone)
                                <div class="flex items-center">
                                    <svg class="icon flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    <a href="tel:{{ $cv->headerInfo->phone }}" class="text-white hover:underline">{{ $cv->headerInfo->phone }}</a>
                                </div>
                            @endif
                            @if($cv->headerInfo->location)
                                <div class="flex items-center">
                                    <svg class="icon flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    {{ $cv->headerInfo->location }}
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Links --}}
                    @if($cv->headerInfo->linkedin_url || $cv->headerInfo->github_url || $cv->headerInfo->website_url)
                        <div>
                            <h3 class="font-semibold mb-2 text-blue-600" style="font-size: 8pt;">Links</h3>
                            <div class="space-y-2 opacity-90">
                                @if($cv->headerInfo->linkedin_url)
                                    <div class="break-all"><a href="{{ $cv->headerInfo->linkedin_url }}" class="text-white hover:underline">LinkedIn</a></div>
                                @endif
                                @if($cv->headerInfo->github_url)
                                    <div class="break-all"><a href="{{ $cv->headerInfo->github_url }}" class="text-white hover:underline">GitHub</a></div>
                                @endif
                                @if($cv->headerInfo->website_url)
                                    <div class="break-all"><a href="{{ $cv->headerInfo->website_url }}" class="text-white hover:underline">{{ parse_url($cv->headerInfo->website_url, PHP_URL_HOST) }}</a></div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Skills in Sidebar --}}
                    @foreach($sections as $section)
                        @if($section->section_type === 'skills' && $section->skillCategories->count() > 0)
                            <div class="border-t border-white/20 mt-6 pt-4">
                                <h3 class="font-semibold mb-2 text-blue-600" style="font-size: 8pt;">{{ $section->title }}</h3>
                                <div class="space-y-5">
                                    @foreach($section->skillCategories->sortBy('display_order') as $category)
                                        <div>
                                            <div class="font-medium mb-1.5 text-blue-600">{{ $category->category_name }}</div>
                                            <div class="opacity-90 leading-relaxed">
                                                @foreach($category->skills as $index => $skill)
                                                    @if($index > 0)<span class="text-blue-600"> • </span>@endif{{ $skill }}
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </aside>

        {{-- Main Content --}}
        <main class="flex-1 p-8 bg-white">
            @foreach($sections as $section)
                {{-- Summary Section --}}
                @if($section->section_type === 'summary' && $section->summary)
                    <section class="mb-8">
                        <h3 class="section-title">Professional Summary</h3>
                        <p class="text-gray-700 leading-relaxed text-justify whitespace-pre-line">{{ $section->summary->content }}</p>
                    </section>
                @endif

                {{-- Custom Section --}}
                @if($section->section_type === 'custom' && $section->customSection)
                    <section class="mb-8">
                        <h3 class="section-title">{{ $section->title }}</h3>
                        @php
                            $content = $section->customSection->content;
                            $hasBullets = str_contains($content, '•');
                        @endphp

                        @if($hasBullets)
                            @php
                                // Split on bullet points
                                $items = preg_split('/\s*•\s*/', $content);
                                $items = array_filter(array_map('trim', $items));
                            @endphp
                            <ul class="list-disc list-outside ml-5 text-gray-700 space-y-1 leading-relaxed">
                                @foreach($items as $item)
                                    @if($item)
                                        <li>{{ $item }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        @else
                            <div class="text-gray-700 leading-relaxed space-y-2">
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
                        @endif
                    </section>
                @endif

                {{-- Experience Section --}}
                @if($section->section_type === 'experience' && $section->experiences->count() > 0)
                    <section class="mb-8">
                        <h3 class="section-title">Experience</h3>
                        <div class="space-y-6">
                            @foreach($section->experiences->sortBy('display_order') as $experience)
                                <div class="relative">
                                    <div class="flex justify-between items-start mb-1">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900" style="font-size: 9.5pt;">{{ $experience->job_title }}</h4>
                                            <div class="text-blue-600 font-medium">
                                                @if($experience->company_url)
                                                    <a href="{{ $experience->company_url }}" class="text-blue-600 hover:underline">{{ $experience->company_name }}</a>
                                                @else
                                                    {{ $experience->company_name }}
                                                @endif
                                            </div>
                                            @if($experience->location)
                                                <div class="text-xs text-gray-500 mt-0.5">{{ $experience->location }}</div>
                                            @endif
                                        </div>
                                        <div class="text-slate-400 font-normal whitespace-nowrap ml-4" style="font-size: 7pt;">
                                            {{ $experience->start_date->format('M Y') }} –
                                            @if($experience->is_current)
                                                Present
                                            @elseif($experience->end_date)
                                                {{ $experience->end_date->format('M Y') }}
                                            @endif
                                        </div>
                                    </div>
                                    <ul class="list-disc list-outside ml-5 text-gray-700 space-y-1 leading-relaxed mt-2">
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
                    <section class="mb-8">
                        <h3 class="section-title">Projects</h3>
                        <div class="space-y-5">
                            @foreach($section->projects->sortBy('display_order') as $project)
                                <div class="border-l-2 border-slate-200 pl-4">
                                    <h4 class="font-semibold text-gray-900">
                                        @if($project->project_url)
                                            <a href="{{ $project->project_url }}" class="text-gray-900 hover:text-primary hover:underline">{{ $project->project_name }}</a>
                                        @else
                                            {{ $project->project_name }}
                                        @endif
                                    </h4>
                                    <p class="text-gray-700 leading-relaxed mt-1">{{ $project->description }}</p>
                                    @if($project->technologies)
                                        <div class="flex flex-wrap gap-1.5 mt-2">
                                            @foreach(array_map('trim', explode(',', $project->technologies)) as $tech)
                                                <span class="skill-badge">{{ $tech }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Education Section --}}
                @if($section->section_type === 'education' && $section->education->count() > 0)
                    <section class="mb-8">
                        <h3 class="section-title">Education</h3>
                        <div class="space-y-4">
                            @foreach($section->education->sortBy('display_order') as $edu)
                                <div class="flex justify-between items-baseline">
                                    <div class="flex-1">
                                        <div class="font-semibold text-gray-900">{{ $edu->degree }}</div>
                                        <div class="text-blue-600">{{ $edu->institution }}</div>
                                        @if($edu->description)
                                            <p class="text-gray-600 text-sm mt-1">{{ $edu->description }}</p>
                                        @endif
                                    </div>
                                    <span class="text-slate-400 font-normal whitespace-nowrap ml-4" style="font-size: 7pt;">
                                        {{ $edu->start_year }}–{{ $edu->end_year ?? 'Present' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- References Section --}}
                @if($section->section_type === 'references' && $section->reference)
                    <section class="mb-8">
                        <h3 class="section-title">References</h3>
                        <p class="text-gray-700 italic">{{ $section->reference->content }}</p>
                    </section>
                @endif
            @endforeach
        </main>
    </div>
</body>
</html>
