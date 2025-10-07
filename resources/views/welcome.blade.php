<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'CV Builder') }}</title>

    <meta name="description" content="Create a professional, ATS-friendly CV in minutes with modern templates using CV Builder.">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Styles -->
    <style>
        :root {
            /* Refined light palette (desaturated / professional) */
            --primary: #3469b7; /* muted royal blue */
            --primary-dark: #2b558f;
            --secondary: #6b5fa8; /* desaturated indigo */
            --background: #f5f7fa; /* soft neutral */
            --card-bg: #ffffff;
            --text-primary: #1f2630;
            --text-secondary: #5b6776;
            --border: #d8e0e9;
            --accent: #eef2f6;
            --success: #3a7f63; /* toned green */
            --warning: #b27a1f; /* muted amber */
            --danger: #b94c47; /* softened red */
            --shadow: 0 4px 10px -2px rgba(27,35,46,0.08), 0 2px 4px rgba(27,35,46,0.05);
            --shadow-lg: 0 14px 32px -8px rgba(27,35,46,0.18), 0 4px 10px -2px rgba(27,35,46,0.08);
        }

        /* Dark theme colors */
        .dark-mode {
            --primary: #5a85c6; /* softened blue */
            --primary-dark: #41689c;
            --secondary: #8a7cc4; /* muted indigo */
            --background: #111923; /* deep neutral */
            --card-bg: #1d2732;
            --text-primary: #e6ecf2;
            --text-secondary: #8693a3;
            --border: #2c3946;
            --accent: #253240;
            --success: #4d9b7b;
            --warning: #c08d34;
            --danger: #c26660;
            --shadow: 0 4px 14px -4px rgba(0,0,0,0.55), 0 2px 6px -1px rgba(0,0,0,0.45);
            --shadow-lg: 0 18px 38px -12px rgba(0,0,0,0.7), 0 6px 14px -4px rgba(0,0,0,0.55);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--background);
            color: var(--text-primary);
            min-height: 100vh;
            transition: background-color 0.3s, color 0.3s;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        header {
            padding: 1.4rem 0 1.1rem;
            background: var(--background);
            border: none;
        }
        .dark-mode header { background: var(--background); }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
        }

        .logo span {
            color: var(--secondary);
        }

        .auth-buttons {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-block;
            border: 1px solid transparent;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .btn-outline {
            background-color: transparent;
            border-color: var(--border);
            color: var(--text-primary);
        }

        .btn-outline:hover {
            background-color: var(--accent);
        }

        .hero {
            padding: 5rem 0 4rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            border-radius: 1.75rem;
            background: linear-gradient(180deg, rgba(255,255,255,0.65), rgba(255,255,255,0.55));
            backdrop-filter: blur(10px) saturate(140%);
            border: 1px solid var(--border);
        }
        .dark-mode .hero {
            background: linear-gradient(180deg, rgba(30,41,59,0.7), rgba(30,41,59,0.55));
            border-color: #243044;
        }

        .hero:before, .hero:after {
            content: "";
            position: absolute;
            border-radius: 50%;
            filter: blur(70px);
            opacity: .35;
            pointer-events: none;
            transition: opacity .4s;
        }

        .hero:before {
            width: 480px;
            height: 480px;
            background: radial-gradient(circle at center, var(--primary) 0%, transparent 70%);
            top: -160px;
            left: -180px;
        }

        .hero:after {
            width: 520px;
            height: 520px;
            background: radial-gradient(circle at center, var(--secondary) 0%, transparent 70%);
            bottom: -200px;
            right: -220px;
        }

        .dark-mode .hero:before,
        .dark-mode .hero:after {
            opacity: .25;
        }

        .hero h1 {
            font-size: 2.85rem;
            font-weight: 800;
            margin-bottom: 1.25rem;
            line-height: 1.15;
            position: relative;
            z-index: 1;
        }

        .hero h1 span.gradient {
            background: linear-gradient(90deg,var(--primary) 0%, var(--secondary) 60%, var(--primary) 100%);
            filter: brightness(.92) saturate(.85);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .hero p {
            font-size: 1.125rem;
            color: var(--text-secondary);
            max-width: 650px;
            margin: 0 auto 2.25rem;
            line-height: 1.7;
            position: relative;
            z-index: 1;
        }

        @media (prefers-reduced-motion: reduce) {
            .hero:before, .hero:after { filter: blur(40px); }
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin: 3rem 0;
        }

        .feature-card {
            background: rgba(255,255,255,0.35);
            backdrop-filter: blur(22px) saturate(160%);
            -webkit-backdrop-filter: blur(22px) saturate(160%);
            border-radius: 1.15rem;
            padding: 2.35rem 2rem 2.3rem;
            box-shadow: 0 4px 24px -4px rgba(15,23,42,0.12), 0 2px 6px rgba(15,23,42,0.08);
            transition: transform 0.5s cubic-bezier(.5,.2,.2,1), box-shadow 0.5s, border-color .4s, background .5s;
            border: 1px solid rgba(255,255,255,0.55);
            position: relative;
            overflow: hidden;
        }

        .feature-card:before,
        .feature-card:after {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            opacity: 0;
            transition: opacity .6s;
        }
        .feature-card:before {
            background: linear-gradient(125deg, rgba(255,255,255,0.65) 0%, rgba(255,255,255,0.05) 55%);
            mix-blend-mode: overlay;
        }
        .feature-card:after {
            background:
                radial-gradient(circle at 25% 20%, rgba(59,130,246,0.18), transparent 70%),
                radial-gradient(circle at 80% 70%, rgba(139,92,246,0.18), transparent 70%);
            filter: blur(40px);
        }
        .feature-card:hover:before,
        .feature-card:hover:after { opacity: 1; }

        .feature-card:hover {
            transform: translateY(-10px) scale(1.025);
            box-shadow: 0 8px 32px -6px rgba(15,23,42,0.25), 0 4px 12px rgba(15,23,42,0.12);
            border-color: rgba(255,255,255,0.8);
            background: rgba(255,255,255,0.45);
        }

        .dark-mode .feature-card { background: rgba(30,41,59,0.55); border-color: rgba(255,255,255,0.08); box-shadow: 0 4px 24px -6px rgba(0,0,0,0.55), 0 2px 8px -2px rgba(0,0,0,0.4); }
        .dark-mode .feature-card:hover { background: rgba(30,41,59,0.7); border-color: rgba(255,255,255,0.12); }
        .dark-mode .feature-card:before { background: linear-gradient(125deg, rgba(255,255,255,0.08) 0%, rgba(255,255,255,0.02) 55%); }
        .dark-mode .feature-card:after { background: radial-gradient(circle at 25% 20%, rgba(96,165,250,0.18), transparent 70%), radial-gradient(circle at 80% 70%, rgba(167,139,250,0.18), transparent 70%); }

        .feature-icon {
            width: 3.25rem;
            height: 3.25rem;
            background: linear-gradient(135deg,var(--primary),var(--secondary));
            border-radius: 0.85rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.55rem;
            color: white;
            font-size: 1.5rem;
            box-shadow: 0 8px 16px -6px rgba(0,0,0,.2);
        }

        .feature-card h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }

        .feature-card p {
            color: var(--text-secondary);
            line-height: 1.6;
        }

        .cta-section {
            text-align: center;
            padding: 3.5rem 1rem;
            margin: 4rem 0 3rem;
            background: linear-gradient(135deg, rgba(52,105,183,0.10), rgba(107,95,168,0.10));
            border: 1px solid rgba(255,255,255,0.35);
            border-radius: 1.15rem;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(18px) saturate(150%);
        }
        .cta-section:before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 22% 28%, rgba(255,255,255,0.55), transparent 60%),
                radial-gradient(circle at 78% 70%, rgba(255,255,255,0.4), transparent 65%);
            opacity: .55;
            mix-blend-mode: overlay;
        }
        .cta-section h2 { color: var(--text-primary); }
        .cta-section p { color: var(--text-secondary); }
        .cta-section .btn-primary { background: var(--primary); color: #fff; border-color: var(--primary-dark); }
        .cta-section .btn-primary:hover { background: var(--primary-dark); }
        .dark-mode .cta-section { background: linear-gradient(135deg, rgba(90,133,198,0.12), rgba(138,124,196,0.12)); border-color: rgba(255,255,255,0.08); }
        .dark-mode .cta-section:before { background: radial-gradient(circle at 22% 28%, rgba(255,255,255,0.08), transparent 60%), radial-gradient(circle at 78% 70%, rgba(255,255,255,0.05), transparent 65%); }
        .dark-mode .cta-section h2 { color: var(--text-primary); }
        .dark-mode .cta-section p { color: var(--text-secondary); }
        .dark-mode .cta-section .btn-primary { background: var(--primary); border-color: var(--primary-dark); }
        .dark-mode .cta-section .btn-primary:hover { background: var(--primary-dark); }

        .cta-section h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .cta-section p {
            color: var(--text-secondary);
            max-width: 600px;
            margin: 0 auto 2rem;
            line-height: 1.6;
        }

        .theme-toggle {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-primary);
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            transition: background-color 0.2s;
        }

        .theme-toggle:hover {
            background-color: var(--accent);
        }

        footer {
            text-align: center;
            padding: 2rem 0;
            color: var(--text-secondary);
            border-top: 1px solid var(--border);
            margin-top: 2rem;
        }


        /* Decorative global background */
        body:before {
            content: "";
            position: fixed;
            inset: 0;
            background:
                radial-gradient(circle at 15% 20%, rgba(59,130,246,.10), transparent 60%),
                radial-gradient(circle at 85% 70%, rgba(139,92,246,.12), transparent 65%),
                linear-gradient(120deg, rgba(59,130,246,.06), rgba(139,92,246,.04));
            pointer-events: none;
            opacity: .55;
            z-index: -1;
        }

        /* Header (transparent) */
        header {
            position: static;
            background: transparent;
            backdrop-filter: none;
        }
        .dark-mode header { background: transparent; }

        /* Animated gradient border utility */
        .btn-gradient {
            position: relative;
            background: linear-gradient(var(--background), var(--background)) padding-box,
                        linear-gradient(90deg,var(--primary),var(--secondary)) border-box;
            border: 2px solid transparent;
            border-radius: .75rem;
            padding: .85rem 1.6rem;
            font-weight: 600;
            overflow: hidden;
            transition: color .3s, background-color .3s;
        }
        .btn-gradient:hover { color: var(--primary); background: linear-gradient(#fff,#fff) padding-box, linear-gradient(90deg,var(--secondary),var(--primary)) border-box; }
        .dark-mode .btn-gradient:hover { background: linear-gradient(var(--card-bg),var(--card-bg)) padding-box, linear-gradient(90deg,var(--secondary),var(--primary)) border-box; }

        /* Process (How it works) */
        .process {
            margin: 4.5rem 0 3rem;
        }
        .process h2 {
            text-align: center;
            font-size: 2.1rem;
            font-weight: 700;
            margin-bottom: 2.5rem;
            background: linear-gradient(90deg,var(--primary),var(--secondary));
            -webkit-background-clip: text; color: transparent;
        }
        .steps {
            display: grid;
            gap: 2rem;
            grid-template-columns: repeat(auto-fit,minmax(230px,1fr));
            counter-reset: step;
        }
        .step-item {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 1rem;
            padding: 1.6rem 1.4rem 1.4rem;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: transform .4s, box-shadow .4s;
        }
        .step-item:before {
            counter-increment: step;
            content: counter(step);
            position: absolute;
            top: .85rem; right: .85rem;
            font-size: .75rem;
            background: linear-gradient(135deg,var(--primary),var(--secondary));
            color: #fff;
            width: 1.9rem; height: 1.9rem;
            display: flex; align-items: center; justify-content: center;
            border-radius: 50%;
            box-shadow: 0 4px 10px -2px rgba(0,0,0,.25);
        }
        .step-item:hover { transform: translateY(-6px); box-shadow: var(--shadow-lg); }
        .step-item h3 { font-size: 1.05rem; margin-bottom: .55rem; font-weight: 600; }
        .step-item p { color: var(--text-secondary); line-height: 1.55; font-size: .9rem; }
        .dark-mode .step-item { background: var(--card-bg); }

        /* Template preview mock gallery */
        .templates {
            margin: 4.5rem 0 3rem;
        }
        .templates-header { text-align: center; margin-bottom: 2.25rem; }
        .templates-header h2 { font-size: 2rem; font-weight: 700; margin-bottom: .6rem; }
        .templates-header p { color: var(--text-secondary); max-width: 620px; margin: 0 auto; line-height: 1.55; }
        .template-grid { display: grid; gap: 1.4rem; grid-template-columns: repeat(auto-fit,minmax(220px,1fr)); }
        .template-card { position: relative; border:1px solid var(--border); border-radius: .9rem; background: var(--card-bg); overflow:hidden; aspect-ratio: 3/4; box-shadow: var(--shadow); display:flex; align-items:center; justify-content:center; }
        .template-card:before { content:""; position:absolute; inset:0; background:linear-gradient(160deg,var(--accent),transparent 55%); opacity:.7; }
        .template-card small { position: relative; font-size: .75rem; letter-spacing:.05em; text-transform: uppercase; font-weight:600; color: var(--text-secondary); }
        .template-card:hover { box-shadow: var(--shadow-lg); transform: translateY(-5px); transition: .45s; }
        .template-card:nth-child(2) { background: linear-gradient(135deg,#fff,var(--accent)); }
        .dark-mode .template-card:nth-child(2) { background: linear-gradient(135deg,var(--card-bg),rgba(255,255,255,.04)); }
        .template-card:nth-child(3) { background: linear-gradient(135deg,#fff,var(--accent)); }
        .dark-mode .template-card:nth-child(3) { background: linear-gradient(135deg,var(--card-bg),rgba(255,255,255,.05)); }

        /* Testimonials */
        .testimonials { margin: 5rem 0 3.5rem; }
        .testimonial-wrapper { display:grid; gap:2rem; grid-template-columns: repeat(auto-fit,minmax(320px,1fr)); }
        .testimonial { position:relative; background: var(--card-bg); border:1px solid var(--border); padding:2rem 1.6rem 1.8rem; border-radius:1rem; box-shadow: var(--shadow); overflow:hidden; }
        .testimonial:before { content:"\201C"; position:absolute; font-size:6rem; line-height:1; top:-1.5rem; left:.5rem; color: var(--primary); opacity:.15; font-weight:700; }
        .testimonial p { position:relative; font-size:.95rem; line-height:1.6; margin-bottom:1rem; }
        .testimonial .author { font-size:.85rem; font-weight:600; color: var(--text-secondary); letter-spacing:.04em; text-transform:uppercase; }

        /* Animated subtle floating badges in hero */
        .hero-badges { position: absolute; inset:0; pointer-events:none; }
        .hero-badge { position:absolute; background: var(--card-bg); border:1px solid var(--border); padding:.55rem .85rem; font-size:.68rem; font-weight:600; letter-spacing:.05em; text-transform:uppercase; border-radius:2rem; display:flex; align-items:center; gap:.4rem; box-shadow: var(--shadow); animation: float 9s ease-in-out infinite; }
        .hero-badge svg { width:.85rem; height:.85rem; }
        .hero-badge:nth-child(1) { top:12%; left:8%; animation-delay:0s; }
        .hero-badge:nth-child(2) { top:65%; left:12%; animation-delay:1.5s; }
        .hero-badge:nth-child(3) { top:28%; right:10%; animation-delay:3s; }
        @keyframes float { 0%,100% { transform:translateY(0); } 50% { transform:translateY(-14px); } }
        .dark-mode .hero-badge { background: var(--card-bg); }

        /* Utility section spacing override for next sections before CTA */
        .pre-cta-spacer { margin-top: 2rem; }
                <h2>How It Works</h2>
                <div class="steps">
                    <div class="step-item">
                        <h3>Choose a Template</h3>
                        <p>Select from curated, recruiter-tested layouts engineered for clarity & impact.</p>
                    </div>
                    <div class="step-item">
                        <h3>Add Your Content</h3>
                        <p>Guided sections help you structure achievements, not just job descriptions.</p>
                    </div>
                    <div class="step-item">
                        <h3>Optimise & Score</h3>
                        <p>Built‑in heuristics (coming soon) surface weak verbs, gaps & formatting issues.</p>
                    </div>
                    <div class="step-item">
                        <h3>Export Anywhere</h3>
                        <p>Download polished PDFs, DOCX or plaintext. Your data stays editable.</p>
                    </div>
                </div>
            </section>

            <section class="templates">
                <div class="templates-header">
                    <h2>Modern Templates</h2>
                    <p>A growing collection focused on legibility, semantic structure and recruiter scannability. More styles arriving soon.</p>
                </div>
                <div class="template-grid">
                    <div class="template-card"><small>Minimalist</small></div>
                    <div class="template-card"><small>Structured</small></div>
                    <div class="template-card"><small>Professional</small></div>
                    <div class="template-card"><small>Compact</small></div>
                </div>
            </section>

            <section class="testimonials">
                <div class="templates-header" style="margin-bottom:2.4rem;">
                    <h2>What Users Say</h2>
                    <p>Early adopters using the builder to refine applications and accelerate interview callbacks.</p>
                </div>
                <div class="testimonial-wrapper">
                    <div class="testimonial">
                        <p>Switched from my old Word doc and within a week had two interview invites. The formatting alone levels you up.</p>
                        <div class="author"> -  Alex (Product Manager)</div>
                    </div>
                    <div class="testimonial">
                        <p>The layout clarity forced me to rethink bullet points as achievements. It genuinely improved my story.</p>
                        <div class="author"> -  Priya (Data Analyst)</div>
                    </div>
                    <div class="testimonial">
                        <p>So fast to iterate versions. The structure keeps things tight and ATS parsing wasn’t an issue anymore.</p>
                        <div class="author"> -  Tom (Software Engineer)</div>
                    </div>
                </div>
            </section>

            <div class="pre-cta-spacer"></div>
            width: 1.9rem; height: 1.9rem;
            display: flex; align-items: center; justify-content: center;
            border-radius: 50%;
            box-shadow: 0 4px 10px -2px rgba(0,0,0,.25);
        }
        .step-item:hover { transform: translateY(-6px); box-shadow: var(--shadow-lg); }
        .step-item h3 { font-size: 1.05rem; margin-bottom: .55rem; font-weight: 600; }
        .step-item p { color: var(--text-secondary); line-height: 1.55; font-size: .9rem; }
        .dark-mode .step-item { background: var(--card-bg); }

        /* Template preview mock gallery */
        .templates {
            margin: 4.5rem 0 3rem;
        }
        .templates-header { text-align: center; margin-bottom: 2.25rem; }
        .templates-header h2 { font-size: 2rem; font-weight: 700; margin-bottom: .6rem; }
        .templates-header p { color: var(--text-secondary); max-width: 620px; margin: 0 auto; line-height: 1.55; }
        .template-grid { display: grid; gap: 1.4rem; grid-template-columns: repeat(auto-fit,minmax(220px,1fr)); }
        .template-card { position: relative; border:1px solid var(--border); border-radius: .9rem; background: var(--card-bg); overflow:hidden; aspect-ratio: 3/4; box-shadow: var(--shadow); display:flex; align-items:center; justify-content:center; }
        .template-card:before { content:""; position:absolute; inset:0; background:linear-gradient(160deg,var(--accent),transparent 55%); opacity:.7; }
        .template-card small { position: relative; font-size: .75rem; letter-spacing:.05em; text-transform: uppercase; font-weight:600; color: var(--text-secondary); }
        .template-card:hover { box-shadow: var(--shadow-lg); transform: translateY(-5px); transition: .45s; }
        .template-card:nth-child(2) { background: linear-gradient(135deg,#fff,var(--accent)); }
        .dark-mode .template-card:nth-child(2) { background: linear-gradient(135deg,var(--card-bg),rgba(255,255,255,.04)); }
        .template-card:nth-child(3) { background: linear-gradient(135deg,#fff,var(--accent)); }
        .dark-mode .template-card:nth-child(3) { background: linear-gradient(135deg,var(--card-bg),rgba(255,255,255,.05)); }

        /* Testimonials */
        .testimonials { margin: 5rem 0 3.5rem; }
        .testimonial-wrapper { display:grid; gap:2rem; grid-template-columns: repeat(auto-fit,minmax(320px,1fr)); }
        .testimonial { position:relative; background: var(--card-bg); border:1px solid var(--border); padding:2rem 1.6rem 1.8rem; border-radius:1rem; box-shadow: var(--shadow); overflow:hidden; }
        .testimonial:before { content:"\201C"; position:absolute; font-size:6rem; line-height:1; top:-1.5rem; left:.5rem; color: var(--primary); opacity:.15; font-weight:700; }
        .testimonial p { position:relative; font-size:.95rem; line-height:1.6; margin-bottom:1rem; }
        .testimonial .author { font-size:.85rem; font-weight:600; color: var(--text-secondary); letter-spacing:.04em; text-transform:uppercase; }

        /* Animated subtle floating badges in hero */
        .hero-badges { position: absolute; inset:0; pointer-events:none; }
        .hero-badge { position:absolute; background: var(--card-bg); border:1px solid var(--border); padding:.55rem .85rem; font-size:.68rem; font-weight:600; letter-spacing:.05em; text-transform:uppercase; border-radius:2rem; display:flex; align-items:center; gap:.4rem; box-shadow: var(--shadow); animation: float 9s ease-in-out infinite; }
        .hero-badge svg { width:.85rem; height:.85rem; }
        .hero-badge:nth-child(1) { top:12%; left:8%; animation-delay:0s; }
        .hero-badge:nth-child(2) { top:65%; left:12%; animation-delay:1.5s; }
        .hero-badge:nth-child(3) { top:28%; right:10%; animation-delay:3s; }
        @keyframes float { 0%,100% { transform:translateY(0); } 50% { transform:translateY(-14px); } }
        .dark-mode .hero-badge { background: var(--card-bg); }

        /* Utility section spacing override for next sections before CTA */
        .pre-cta-spacer { margin-top: 2rem; }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 1rem;
            }

            .hero {
                padding: 2rem 0;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .auth-buttons {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="header-content">
                <a href="/" class="logo">CV<span>Builder</span></a>

                <div class="auth-buttons">
                    <a href="/admin" class="btn btn-outline">Admin Login</a>

                    <button id="theme-toggle" class="theme-toggle">
                        <!-- Sun icon for light mode -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="sun-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <!-- Moon icon for dark mode -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="moon-icon" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>
                </div>
            </div>
        </header>

        <main>
            <section class="hero">
                <h1>Create Your <span class="gradient">Perfect CV</span> in Minutes</h1>
                <div class="hero-badges">
                    <div class="hero-badge"><svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> ATS Safe</div>
                    <div class="hero-badge"><svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V6m0 12v-2m8-4a8 8 0 11-16 0 8 8 0 0116 0z"/></svg> Fast Export</div>
                    <div class="hero-badge"><svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg> Instant Impact</div>
                </div>
                <p>Build professional, ATS-friendly resumes with our easy-to-use CV builder. Choose from modern templates and land your dream job.</p>
                <div>
                    <a href="/admin" class="btn btn-primary" style="padding: 0.85rem 1.75rem; font-size: 1.125rem; box-shadow: 0 6px 14px -4px rgba(59,130,246,.5);">Get Started</a>
                </div>
            </section>

            <section class="features">
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                        </svg>
                    </div>
                    <h3>Professional Templates</h3>
                    <p>Choose from a variety of modern, professionally designed templates that help you stand out to employers.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3>ATS Optimized</h3>
                    <p>Our CVs are optimized for Applicant Tracking Systems (ATS) to ensure your application gets seen by recruiters.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                    </div>
                    <h3>Easy to Update</h3>
                    <p>Update your CV anytime and download in multiple formats including PDF, Word, and plain text.</p>
                </div>
            </section>

            <section class="cta-section">
                <h2>Ready to Build Your Standout CV?</h2>
                <p>Join thousands of professionals who have successfully landed jobs with CVs created using our platform.</p>
                <div>
                    <a href="/admin" class="btn btn-primary" style="padding: 0.95rem 2rem; font-size: 1.125rem; font-weight:600; box-shadow: 0 8px 18px -6px rgba(0,0,0,.25);">Create Your CV Now</a>
                </div>
            </section>
        </main>

        <footer>
            <p>&copy; {{ date('Y') }} CV Builder. All rights reserved.</p>
        </footer>
    </div>

    <script>
        // Theme toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('theme-toggle');
            const sunIcon = themeToggle.querySelector('.sun-icon');
            const moonIcon = themeToggle.querySelector('.moon-icon');
            const body = document.body;

            // Check for saved theme preference or respect OS preference
            const savedTheme = localStorage.getItem('theme');
            const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');

            if (savedTheme === 'dark' || (!savedTheme && prefersDarkScheme.matches)) {
                body.classList.add('dark-mode');
                sunIcon.style.display = 'none';
                moonIcon.style.display = 'block';
            }

            themeToggle.addEventListener('click', function() {
                body.classList.toggle('dark-mode');

                if (body.classList.contains('dark-mode')) {
                    localStorage.setItem('theme', 'dark');
                    sunIcon.style.display = 'none';
                    moonIcon.style.display = 'block';
                } else {
                    localStorage.setItem('theme', 'light');
                    sunIcon.style.display = 'block';
                    moonIcon.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
