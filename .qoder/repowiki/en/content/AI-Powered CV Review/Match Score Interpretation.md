# Match Score Interpretation

<cite>
**Referenced Files in This Document**   
- [CvReviewService.php](file://app/Services/CvReviewService.php)
- [ai-review-results.blade.php](file://resources/views/filament/forms/ai-review-results.blade.php)
- [ai-review-summary.blade.php](file://resources/views/filament/forms/ai-review-summary.blade.php)
- [IncompleteCvException.php](file://app/Exceptions/IncompleteCvException.php)
- [MissingJobDescriptionException.php](file://app/Exceptions/MissingJobDescriptionException.php)
- [CvReviewServiceTest.php](file://tests/Feature/CvReviewServiceTest.php)
</cite>

## Table of Contents
1. [Match Score Calculation Methodology](#match-score-calculation-methodology)
2. [Job Requirements Extraction](#job-requirements-extraction)
3. [Score Interpretation and Ranges](#score-interpretation-and-ranges)
4. [User Interface Presentation](#user-interface-presentation)
5. [Edge Cases and Exceptions](#edge-cases-and-exceptions)
6. [Score Improvement Strategies](#score-improvement-strategies)
7. [Reliability and Limitations](#reliability-and-limitations)

## Match Score Calculation Methodology

The match score is calculated by the `CvReviewService` class through a weighted combination of four key alignment factors. The `calculateMatchScore` method combines these components using the following formula:

- **Skills Alignment (40%)**: Measures how well the CV's listed skills match the required skills from the job description. The system performs case-insensitive string matching between CV skills and required skills, calculating the percentage of required skills that are present.
- **Experience Relevance (30%)**: Evaluates the relevance of the candidate's work experience to the job's role focus areas. A base score of 60 is given, with an additional 10 points awarded if any experience title matches a role focus area from the job description.
- **Keyword Coverage (20%)**: Assesses the presence of important keywords from the job description within the CV content. The system calculates the percentage of job keywords that appear anywhere in the CV data.
- **Evidence Quality (10%)**: Evaluates the completeness of supporting evidence in the CV, with points awarded for having experiences (15 points), education (10 points), and highlights (15 points) sections.

The final score is calculated as a weighted sum of these components, then rounded and constrained to the 0-100 range.

**Section sources**
- [CvReviewService.php](file://app/Services/CvReviewService.php#L147-L177)

## Job Requirements Extraction

The `extractJobRequirements` method in `CvReviewService` is responsible for parsing unstructured job descriptions into structured requirements. This method sends the job description to the OpenAI API with a system prompt that instructs the model to extract specific components in JSON format.

The method extracts the following structured data from job descriptions:
- **Skills**: Technical and soft skills explicitly mentioned in the job posting
- **Competencies**: Broader professional capabilities and attributes
- **Keywords**: Important terms and phrases that should appear in a competitive CV
- **Experience Level**: The required seniority level (e.g., "mid", "senior")
- **Role Focus**: Key areas of responsibility or specialization emphasized in the role

The extracted requirements are then used as the benchmark against which the CV is evaluated. This structured approach allows for systematic comparison between the candidate's qualifications and the job requirements.

**Section sources**
- [CvReviewService.php](file://app/Services/CvReviewService.php#L99-L129)
- [CvReviewServiceTest.php](file://tests/Feature/CvReviewServiceTest.php#L83-L114)

## Score Interpretation and Ranges

The match score is presented on a 0-100 scale, with different ranges indicating the level of alignment between the CV and job requirements:

- **0-30: Significant gaps** - The CV has major deficiencies in required skills, experience, or keywords. Substantial revisions are needed to be competitive.
- **31-60: Moderate alignment** - The CV demonstrates some relevant qualifications but has notable gaps in key areas. Targeted improvements can significantly increase competitiveness.
- **61-80: Good fit** - The CV shows strong alignment with the job requirements, with most essential skills and experiences present. Refinements can optimize the presentation.
- **81-100: Excellent match** - The CV demonstrates comprehensive alignment with the job requirements, including all critical skills and relevant experiences.

These ranges are reflected in the UI through color coding: red for scores below 50, amber for scores 50-69, and green for scores 70 and above.

**Section sources**
- [ai-review-summary.blade.php](file://resources/views/filament/forms/ai-review-summary.blade.php#L50-L66)

## User Interface Presentation

The match score is displayed in the application through the `ai-review-results.blade.php` and `ai-review-summary.blade.php` view files. The UI presents the score prominently with several visual elements:

- A large percentage display showing the exact match score
- Color-coded badges that indicate score quality (red, amber, or green)
- Additional context including the analysis cost in USD
- Tabbed interface for exploring detailed feedback in categories like skill gaps, section priority, and bullet point improvements
- Action items checklist summarizing key recommendations

The interface also includes status indicators for review processing and warnings when a review is stale due to subsequent CV modifications.

**Section sources**
- [ai-review-results.blade.php](file://resources/views/filament/forms/ai-review-results.blade.php#L30-L50)
- [ai-review-summary.blade.php](file://resources/views/filament/forms/ai-review-summary.blade.php#L93-L99)

## Edge Cases and Exceptions

The system handles two primary edge cases through dedicated exception classes:

- **IncompleteCvException**: Thrown when a CV lacks sufficient content for meaningful analysis. This occurs when the CV has neither experiences nor skills, as determined by the `getExperiencesList()` and `getSkillsList()` methods. The system requires at least one of these components to proceed with analysis.

- **MissingJobDescriptionException**: Thrown when the job application lacks a job description or when the description is too short (fewer than 50 characters). A substantive job description is required as the basis for comparison.

These exceptions prevent the system from generating unreliable or meaningless match scores when insufficient information is available for proper evaluation.

**Section sources**
- [CvReviewService.php](file://app/Services/CvReviewService.php#L15-L30)
- [IncompleteCvException.php](file://app/Exceptions/IncompleteCvException.php)
- [MissingJobDescriptionException.php](file://app/Exceptions/MissingJobDescriptionException.php)
- [CvReviewServiceTest.php](file://tests/Feature/CvReviewServiceTest.php#L35-L81)

## Score Improvement Strategies

Users can improve their match score through targeted CV enhancements:

- **Skills section**: Add missing technical skills and competencies identified in the job description, particularly those highlighted as high-priority gaps.
- **Experience refinement**: Modify job titles or descriptions to include role focus areas from the job posting, which directly impacts the experience relevance score.
- **Keyword optimization**: Incorporate important keywords from the job description into achievement bullets and section headers.
- **Evidence strengthening**: Ensure all relevant experiences, education, and project highlights are included, as their presence contributes to the evidence quality component.
- **Section organization**: Reorder sections to prioritize those most relevant to the job's focus areas, following the system's section recommendation guidance.

Improvements should be authentic and factually accurate, focusing on better representation of existing qualifications rather than fabrication.

**Section sources**
- [CvReviewService.php](file://app/Services/CvReviewService.php#L177-L224)
- [ai-review-results.blade.php](file://resources/views/filament/forms/ai-review-results.blade.php#L60-L100)

## Reliability and Limitations

While the automated match score provides valuable guidance, users should be aware of its limitations:

- The score depends on the quality of the OpenAI API's parsing of both the CV and job description, which may occasionally misinterpret context or nuance.
- String matching for skills and keywords may not account for semantic equivalence (e.g., "React" vs "React.js").
- The experience relevance calculation is relatively simple, focusing primarily on title matching rather than deeper assessment of responsibilities and achievements.
- The system cannot verify the authenticity or depth of the experiences and skills claimed in the CV.
- Formatting and presentation aspects that might influence human reviewers are not fully captured in the automated analysis.

The match score should be used as one input among several in the CV refinement process, complemented by human judgment and industry-specific knowledge.

**Section sources**
- [CvReviewService.php](file://app/Services/CvReviewService.php#L69-L97)
- [CvReviewServiceTest.php](file://tests/Feature/CvReviewServiceTest.php#L35-L151)