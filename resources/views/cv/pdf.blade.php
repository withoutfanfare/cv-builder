<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $cv->title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        h2 {
            color: #34495e;
            margin-top: 30px;
        }
        h3 {
            color: #7f8c8d;
        }
        .section {
            margin-bottom: 30px;
        }
        .skill-category {
            margin-bottom: 15px;
        }
        .skills {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }
        .skill {
            background: #ecf0f1;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 14px;
        }
        .experience, .project, .education {
            margin-bottom: 20px;
        }
        .date-range {
            color: #7f8c8d;
            font-style: italic;
        }
    </style>
</head>
<body>
    <h1>{{ $cv->title }}</h1>

    @if($cv->headerInfo)
        <div class="section">
            <p><strong>{{ $cv->headerInfo->name }}</strong></p>
            @if($cv->headerInfo->email)
                <p>Email: {{ $cv->headerInfo->email }}</p>
            @endif
            @if($cv->headerInfo->phone)
                <p>Phone: {{ $cv->headerInfo->phone }}</p>
            @endif
            @if($cv->headerInfo->location)
                <p>Location: {{ $cv->headerInfo->location }}</p>
            @endif
        </div>
    @endif

    @foreach($cv->sections->sortBy('display_order') as $section)
        <div class="section">
            <h2>{{ $section->title }}</h2>

            @if($section->section_type === 'summary' && $section->summary)
                <p>{{ $section->summary->content }}</p>
            @endif

            @if($section->section_type === 'skills')
                @foreach($section->skillCategories as $skillCategory)
                    <div class="skill-category">
                        <h3>{{ $skillCategory->category_name }}</h3>
                        <div class="skills">
                            @foreach($skillCategory->skills as $skill)
                                <span class="skill">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif

            @if($section->section_type === 'experience')
                @foreach($section->experiences as $experience)
                    <div class="experience">
                        <h3>{{ $experience->job_title }} - {{ $experience->company_name }}</h3>
                        <p class="date-range">{{ $experience->start_date }} - {{ $experience->end_date ?? 'Present' }}</p>
                        @if($experience->highlights)
                            <ul>
                                @foreach($experience->highlights as $highlight)
                                    <li>{{ $highlight }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endforeach
            @endif

            @if($section->section_type === 'projects')
                @foreach($section->projects as $project)
                    <div class="project">
                        <h3>{{ $project->project_name }}</h3>
                        <p>{{ $project->description }}</p>
                        @if($project->technologies)
                            <p><strong>Technologies:</strong> {{ implode(', ', $project->technologies) }}</p>
                        @endif
                    </div>
                @endforeach
            @endif

            @if($section->section_type === 'education')
                @foreach($section->education as $edu)
                    <div class="education">
                        <h3>{{ $edu->degree }} - {{ $edu->institution }}</h3>
                        <p class="date-range">{{ $edu->start_date }} - {{ $edu->end_date ?? 'Present' }}</p>
                        @if($edu->grade)
                            <p>Grade: {{ $edu->grade }}</p>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    @endforeach
</body>
</html>
