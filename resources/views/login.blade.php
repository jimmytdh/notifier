<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CSMC Notifier</title>
    <link href="assets/img/favicon.png" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; }

        :root {
            --ln-primary:     #003166;
            --ln-primary-h:   #00478f;
            --ln-secondary:   #29695b;
            --ln-sec-cont:    #acedda;
            --ln-teal-fixed:  #93f1ff;
            --ln-surface:     #f7f9fb;
            --ln-outline-var: #c2c6d2;
            --ln-on-surface:  #191c1e;
            --ln-muted:       #424751;
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            color: var(--ln-on-surface);
            background: var(--ln-surface);
            overflow-x: hidden;
        }

        /* ── Navbar ── */
        .ln-nav {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(255,255,255,.92);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--ln-outline-var);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            height: 60px;
        }
        .ln-nav-brand {
            font-family: 'Manrope', sans-serif;
            font-size: 20px;
            font-weight: 800;
            color: var(--ln-primary);
            text-decoration: none;
            letter-spacing: -.01em;
        }
        .ln-nav-links {
            display: flex;
            align-items: center;
            gap: 28px;
            list-style: none;
            margin: 0; padding: 0;
        }
        .ln-nav-links a {
            font-size: 13px;
            font-weight: 500;
            color: var(--ln-muted);
            text-decoration: none;
            transition: color .15s;
        }
        .ln-nav-links a:hover { color: var(--ln-primary); }
        .ln-nav-actions { display: flex; align-items: center; gap: 10px; }
        .ln-btn-ghost {
            background: transparent;
            border: none;
            font-family: 'Manrope', sans-serif;
            font-size: 13px;
            font-weight: 700;
            color: var(--ln-primary);
            padding: 8px 14px;
            border-radius: 8px;
            cursor: pointer;
            transition: background .12s;
        }
        .ln-btn-ghost:hover { background: #f2f4f6; }
        .ln-btn-primary {
            background: var(--ln-primary);
            color: #fff;
            border: none;
            font-family: 'Manrope', sans-serif;
            font-size: 13px;
            font-weight: 700;
            padding: 9px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: opacity .15s, transform .1s;
        }
        .ln-btn-primary:hover  { opacity: .88; }
        .ln-btn-primary:active { transform: scale(.97); }

        /* ── Hero ── */
        .ln-hero {
            max-width: 1200px;
            margin: 0 auto;
            padding: 72px 40px 60px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 48px;
            align-items: center;
        }
        .ln-hero-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--ln-teal-fixed);
            color: #001f23;
            font-family: 'Manrope', sans-serif;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            padding: 5px 12px;
            border-radius: 9999px;
            margin-bottom: 20px;
        }
        .ln-hero-chip::before {
            content: '';
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--ln-secondary);
        }
        .ln-hero h1 {
            font-family: 'Manrope', sans-serif;
            font-size: 44px;
            font-weight: 800;
            line-height: 1.15;
            color: var(--ln-on-surface);
            margin: 0 0 16px;
        }
        .ln-hero h1 .accent { color: var(--ln-secondary); }
        .ln-hero-sub {
            font-size: 16px;
            color: var(--ln-muted);
            line-height: 1.7;
            max-width: 460px;
            margin: 0 0 32px;
        }
        .ln-hero-btns { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
        .ln-hero-cta {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--ln-primary);
            color: #fff;
            border: none;
            font-family: 'Manrope', sans-serif;
            font-size: 14px;
            font-weight: 700;
            padding: 13px 24px;
            border-radius: 10px;
            cursor: pointer;
            transition: opacity .15s, transform .1s;
        }
        .ln-hero-cta:hover  { opacity: .88; }
        .ln-hero-cta:active { transform: scale(.97); }
        .ln-hero-outline {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: transparent;
            color: var(--ln-primary);
            border: 1.5px solid var(--ln-outline-var);
            font-family: 'Manrope', sans-serif;
            font-size: 14px;
            font-weight: 700;
            padding: 12px 22px;
            border-radius: 10px;
            cursor: pointer;
            text-decoration: none;
            transition: border-color .15s, background .12s;
        }
        .ln-hero-outline:hover { border-color: var(--ln-primary); background: #f2f4f6; color: var(--ln-primary); }

        /* Hero right — dashboard mockup */
        .ln-hero-visual {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .ln-mockup-shell {
            background: #fff;
            border: 1px solid var(--ln-outline-var);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,49,102,.12);
            width: 100%;
            max-width: 460px;
        }
        .ln-mockup-bar {
            background: #f2f4f6;
            border-bottom: 1px solid var(--ln-outline-var);
            padding: 10px 16px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .ln-mockup-dot { width: 10px; height: 10px; border-radius: 50%; }
        .ln-mockup-title {
            font-family: 'Manrope', sans-serif;
            font-size: 11px;
            font-weight: 700;
            color: var(--ln-muted);
            margin-left: 8px;
            letter-spacing: .04em;
            text-transform: uppercase;
        }
        .ln-mockup-body { padding: 16px; display: flex; flex-direction: column; gap: 10px; }
        .ln-mock-card {
            background: #fff;
            border: 1px solid var(--ln-outline-var);
            border-radius: 10px;
            overflow: hidden;
            position: relative;
        }
        .ln-mock-card-accent { position: absolute; left: 0; top: 0; bottom: 0; width: 3px; }
        .ln-mock-card-inner { padding: 10px 12px 10px 15px; }
        .ln-mock-hosp {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--ln-muted);
            margin-bottom: 4px;
        }
        .ln-mock-patient {
            font-family: 'Manrope', sans-serif;
            font-size: 13px;
            font-weight: 700;
            color: var(--ln-on-surface);
            margin-bottom: 3px;
        }
        .ln-mock-meta { font-size: 11px; color: var(--ln-muted); }
        .ln-mock-badge {
            display: inline-block;
            font-size: 9px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 9999px;
            margin-top: 5px;
        }
        /* floating notification */
        .ln-notif {
            position: absolute;
            bottom: -16px;
            right: -16px;
            background: #fff;
            border: 1px solid var(--ln-outline-var);
            border-radius: 12px;
            padding: 10px 14px;
            box-shadow: 0 8px 24px rgba(0,49,102,.12);
            display: flex;
            align-items: center;
            gap: 10px;
            width: 220px;
        }
        .ln-notif-icon {
            width: 32px; height: 32px;
            border-radius: 8px;
            background: #ffdad6;
            color: #ba1a1a;
            display: flex; align-items: center; justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
        }
        .ln-notif-title { font-family: 'Manrope', sans-serif; font-size: 11px; font-weight: 700; color: var(--ln-on-surface); margin: 0 0 1px; }
        .ln-notif-sub   { font-size: 10px; color: var(--ln-muted); margin: 0; }

        /* ── Features ── */
        .ln-features-section {
            background: #fff;
            border-top: 1px solid var(--ln-outline-var);
            border-bottom: 1px solid var(--ln-outline-var);
        }
        .ln-features {
            max-width: 1200px;
            margin: 0 auto;
            padding: 72px 40px;
        }
        .ln-features-head { text-align: center; margin-bottom: 48px; }
        .ln-features-head h2 {
            font-family: 'Manrope', sans-serif;
            font-size: 32px;
            font-weight: 800;
            color: var(--ln-on-surface);
            margin: 0 0 10px;
        }
        .ln-features-head p { font-size: 15px; color: var(--ln-muted); margin: 0; }
        .ln-feature-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }
        .ln-feature-card {
            background: #fff;
            border: 1px solid var(--ln-outline-var);
            border-radius: 14px;
            padding: 24px;
            transition: border-color .15s, box-shadow .15s;
        }
        .ln-feature-card:hover {
            border-color: var(--ln-primary-h);
            box-shadow: 0 4px 20px rgba(0,49,102,.07);
        }
        .ln-feature-card.dark {
            background: var(--ln-primary);
            border-color: var(--ln-primary);
            color: #fff;
        }
        .ln-feat-icon {
            width: 42px; height: 42px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
            margin-bottom: 16px;
        }
        .ln-feat-icon-teal    { background: var(--ln-sec-cont); color: var(--ln-secondary); }
        .ln-feat-icon-navy    { background: #d6e3ff; color: var(--ln-primary); }
        .ln-feat-icon-white   { background: rgba(255,255,255,.15); color: #fff; }
        .ln-feat-icon-amber   { background: #fde68a; color: #92400e; }
        .ln-feature-card h3 {
            font-family: 'Manrope', sans-serif;
            font-size: 16px;
            font-weight: 700;
            margin: 0 0 8px;
        }
        .ln-feature-card p { font-size: 13px; color: var(--ln-muted); margin: 0; line-height: 1.6; }
        .ln-feature-card.dark p { color: rgba(255,255,255,.7); }
        .ln-feature-card.dark h3 { color: #fff; }
        .ln-hipaa-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: var(--ln-teal-fixed);
            color: #001f23;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .06em;
            padding: 4px 10px;
            border-radius: 9999px;
            margin-top: 12px;
            text-transform: uppercase;
        }

        /* ── CTA banner ── */
        .ln-cta {
            background: var(--ln-primary);
            padding: 64px 40px;
            text-align: center;
        }
        .ln-cta h2 {
            font-family: 'Manrope', sans-serif;
            font-size: 30px;
            font-weight: 800;
            color: #fff;
            margin: 0 0 12px;
        }
        .ln-cta p { font-size: 15px; color: rgba(255,255,255,.75); margin: 0 0 28px; }
        .ln-cta-btns { display: flex; align-items: center; justify-content: center; gap: 12px; }
        .ln-cta-btn-white {
            background: #fff;
            color: var(--ln-primary);
            border: none;
            font-family: 'Manrope', sans-serif;
            font-size: 14px;
            font-weight: 700;
            padding: 13px 28px;
            border-radius: 10px;
            cursor: pointer;
            transition: opacity .15s;
        }
        .ln-cta-btn-white:hover { opacity: .9; }
        .ln-cta-btn-outline {
            background: transparent;
            color: #fff;
            border: 1.5px solid rgba(255,255,255,.5);
            font-family: 'Manrope', sans-serif;
            font-size: 14px;
            font-weight: 700;
            padding: 12px 26px;
            border-radius: 10px;
            cursor: pointer;
            text-decoration: none;
            transition: border-color .15s, background .12s;
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }
        .ln-cta-btn-outline:hover { border-color: rgba(255,255,255,.9); background: rgba(255,255,255,.08); color: #fff; }

        /* ── Footer ── */
        .ln-footer {
            background: #fff;
            border-top: 1px solid var(--ln-outline-var);
            padding: 20px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }
        .ln-footer-brand {
            font-family: 'Manrope', sans-serif;
            font-size: 14px;
            font-weight: 700;
            color: var(--ln-primary);
        }
        .ln-footer-copy { font-size: 12px; color: var(--ln-muted); }
        .ln-footer-links { display: flex; gap: 20px; }
        .ln-footer-links a { font-size: 12px; color: var(--ln-muted); text-decoration: none; }
        .ln-footer-links a:hover { color: var(--ln-primary); }

        /* ── Login Modal ── */
        #loginModal .modal-content {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 24px 60px rgba(0,49,102,.18);
        }
        #loginModal .modal-body { padding: 0; }
        .lm-shell {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }
        .lm-brand {
            background: linear-gradient(150deg, #003166, #00478f);
            padding: 40px 32px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: #fff;
        }
        .lm-brand-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,.15);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            padding: 5px 12px;
            border-radius: 9999px;
            margin-bottom: 20px;
            width: fit-content;
        }
        .lm-brand h3 {
            font-family: 'Manrope', sans-serif;
            font-size: 22px;
            font-weight: 800;
            margin: 0 0 10px;
            line-height: 1.25;
        }
        .lm-brand p {
            font-size: 13px;
            opacity: .8;
            line-height: 1.65;
            margin: 0;
        }
        .lm-form-side {
            padding: 36px 32px;
            background: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .lm-logo {
            width: 64px; height: 64px;
            border-radius: 12px;
            object-fit: contain;
            border: 2px solid #eef5ff;
            margin-bottom: 14px;
        }
        .lm-title {
            font-family: 'Manrope', sans-serif;
            font-size: 20px;
            font-weight: 800;
            color: var(--ln-on-surface);
            margin: 0 0 3px;
        }
        .lm-subtitle { font-size: 13px; color: var(--ln-muted); margin: 0 0 22px; }
        .lm-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--ln-muted);
            margin-bottom: 5px;
            display: block;
        }
        .lm-input {
            width: 100%;
            height: 44px;
            border: 1px solid var(--ln-outline-var);
            border-radius: 10px;
            padding: 0 14px;
            font-size: 13px;
            color: var(--ln-on-surface);
            background: #f7f9fb;
            outline: none;
            transition: border-color .15s, background .15s, box-shadow .15s;
            font-family: 'Inter', sans-serif;
        }
        .lm-input:focus {
            border-color: var(--ln-primary-h);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(0,71,143,.12);
        }
        .lm-btn-submit {
            width: 100%;
            height: 44px;
            background: var(--ln-primary);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-family: 'Manrope', sans-serif;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: .04em;
            cursor: pointer;
            margin-top: 20px;
            transition: opacity .15s, transform .1s;
        }
        .lm-btn-submit:hover  { opacity: .88; }
        .lm-btn-submit:active { transform: scale(.98); }
        .lm-error {
            background: #ffdad6;
            color: #ba1a1a;
            font-size: 13px;
            font-weight: 500;
            padding: 10px 14px;
            border-radius: 8px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        /* ── Responsive ── */
        @media (max-width: 900px) {
            .ln-hero { grid-template-columns: 1fr; padding: 48px 24px 40px; }
            .ln-hero-visual { display: none; }
            .ln-hero h1 { font-size: 32px; }
            .ln-feature-grid { grid-template-columns: 1fr; }
            .ln-nav { padding: 0 20px; }
            .ln-nav-links { display: none; }
            .lm-shell { grid-template-columns: 1fr; }
            .lm-brand { display: none; }
            .ln-features { padding: 48px 24px; }
            .ln-footer { padding: 16px 24px; }
        }
    </style>
</head>
<body>

<!-- ── Navbar ── -->
<nav class="ln-nav">
    <a class="ln-nav-brand" href="#">Notifier</a>
    <div class="ln-nav-actions">
        <button class="ln-btn-ghost" data-bs-toggle="modal" data-bs-target="#loginModal">Login</button>
        <button class="ln-btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">Get Started</button>
    </div>
</nav>

<!-- ── Hero ── -->
<section>
    <div class="ln-hero">
        <div class="ln-hero-left">
            <h1>Inter-departmental referrals <span class="accent">made simple.</span></h1>
            <p class="ln-hero-sub">The unified communication hub for hospital staff. Seamlessly coordinate patient transitions, track referral queues, and ensure HIPAA-compliant data exchange in real-time.</p>
            <div class="ln-hero-btns">
                <button class="ln-hero-cta" data-bs-toggle="modal" data-bs-target="#loginModal">
                    Get Started <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </div>
        <div class="ln-hero-visual">
            <div class="ln-mockup-shell">
                <div class="ln-mockup-bar">
                    <span class="ln-mockup-dot" style="background:#fca5a5"></span>
                    <span class="ln-mockup-dot" style="background:#fde68a"></span>
                    <span class="ln-mockup-dot" style="background:#6ee7b7"></span>
                    <span class="ln-mockup-title">Referral Queue — CSMC Notifier</span>
                </div>
                <div class="ln-mockup-body">
                    <div class="ln-mock-card">
                        <div class="ln-mock-card-accent" style="background:#ba1a1a"></div>
                        <div class="ln-mock-card-inner">
                            <div class="ln-mock-hosp"><i class="bi bi-hospital-fill" style="margin-right:4px;color:#003166"></i> Minglanilla District Hospital</div>
                            <div class="ln-mock-patient">JERRAH D. DOE</div>
                            <div class="ln-mock-meta">20Y &middot; Female</div>
                            <span class="ln-mock-badge" style="background:#ffdad6;color:#ba1a1a">● ER OB</span>
                        </div>
                    </div>
                    <div class="ln-mock-card">
                        <div class="ln-mock-card-accent" style="background:#ba1a1a"></div>
                        <div class="ln-mock-card-inner">
                            <div class="ln-mock-hosp"><i class="bi bi-hospital-fill" style="margin-right:4px;color:#003166"></i> Vicente Mendiola Center</div>
                            <div class="ln-mock-patient">JANE D. DOE</div>
                            <div class="ln-mock-meta">13Y &middot; Female</div>
                            <span class="ln-mock-badge" style="background:#ffdad6;color:#ba1a1a">● ER</span>
                        </div>
                    </div>
                    <div class="ln-mock-card">
                        <div class="ln-mock-card-accent" style="background:#29695b"></div>
                        <div class="ln-mock-card-inner">
                            <div class="ln-mock-hosp"><i class="bi bi-hospital-fill" style="margin-right:4px;color:#003166"></i> Cebu Provincial Hospital</div>
                            <div class="ln-mock-patient">JOHN D. DOE</div>
                            <div class="ln-mock-meta">68Y &middot; Male</div>
                            <span class="ln-mock-badge" style="background:#eceef0;color:#424751">INTERNAL MED</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ln-notif">
                <div class="ln-notif-icon"><i class="bi bi-bell-fill"></i></div>
                <div>
                    <p class="ln-notif-title">Urgent Referral</p>
                    <p class="ln-notif-sub">Trauma Ward · Incoming</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── Features ── -->
<section class="ln-features-section"><div class="ln-features">
    <div class="ln-features-head">
        <h2>Precision Engineered Features</h2>
        <p>Designed for the high-stakes environment of modern clinical workflows.</p>
    </div>
    <div class="ln-feature-grid">
        <div class="ln-feature-card">
            <div class="ln-feat-icon ln-feat-icon-teal"><i class="bi bi-bell-fill"></i></div>
            <h3>Real-time Notifications</h3>
            <p>Zero-latency alerts for critical department transfers. Every millisecond counts when handling trauma referrals.</p>
        </div>
        <div class="ln-feature-card">
            <div class="ln-feat-icon ln-feat-icon-navy"><i class="bi bi-list-check"></i></div>
            <h3>Triage-Based Prioritization</h3>
            <p>Incoming referrals are manually assessed and queued according to urgency and hospital capacity.</p>
        </div>
        <div class="ln-feature-card">
            <div class="ln-feat-icon ln-feat-icon-amber"><i class="bi bi-bar-chart-fill"></i></div>
            <h3>Department Analytics</h3>
            <p>Deep insights into referral patterns and bottlenecks to optimize hospital-wide operational efficiency.</p>
        </div>
    </div>
</section>

<!-- ── CTA Banner ── -->
<section class="ln-cta">
    <h2>Ready to streamline your facility?</h2>
    <p>Get a personalised demo of Notifier and see how we can reduce your department transfer times by up to 35%.</p>
    <div class="ln-cta-btns">
        <button class="ln-cta-btn-white" data-bs-toggle="modal" data-bs-target="#loginModal">
            Sign In Now
        </button>
        <a href="{{ route('download') }}" class="ln-cta-btn-outline">
            <i class="bi bi-android2"></i> Download App
        </a>
    </div>
</section>

<!-- ── Footer ── -->
<footer class="ln-footer">
    <span class="ln-footer-brand">Notifier</span>
    <span class="ln-footer-copy">&copy; {{ date('Y') }} Cebu South Medical Center. All rights reserved.</span>
    <div class="ln-footer-links">
        <a href="#">Support</a>
        <a href="#">Legal</a>
        <a href="#">Privacy Policy</a>
    </div>
</footer>

<!-- ── Login Modal ── -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" style="max-width:680px">
        <div class="modal-content">
            <div class="modal-body">
                <div class="lm-shell">

                    <!-- Brand side -->
                    <div class="lm-brand">
                        <div class="lm-brand-chip">
                            <i class="bi bi-shield-fill-check"></i> CSMC Digital
                        </div>
                        <h3>Inter-Departmental Referral System</h3>
                        <p>Once a patient is referred to CSMC, this system handles inter-department forwarding, tracks each movement, and keeps receiving departments informed.</p>
                    </div>

                    <!-- Form side -->
                    <div class="lm-form-side">
                        <button type="button" class="btn-close ms-auto d-block mb-2" data-bs-dismiss="modal" aria-label="Close" style="font-size:12px"></button>
                        <img src="assets/img/logo.png" alt="CSMC" class="lm-logo">
                        <h4 class="lm-title">CSMC Notifier</h4>
                        <p class="lm-subtitle">Sign in to continue</p>

                        @if(Session::has('message'))
                        <div class="lm-error">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            {{ Session::get('message') }}
                        </div>
                        @endif

                        <form method="post" action="{{ route('authenticate') }}">
                            {{ csrf_field() }}
                            <div style="margin-bottom:14px">
                                <label class="lm-label" for="inputUsername">Username</label>
                                <input type="text" name="username" id="inputUsername" class="lm-input" required autofocus placeholder="Enter your username">
                            </div>
                            <div>
                                <label class="lm-label" for="inputPassword">Password</label>
                                <input type="password" name="password" id="inputPassword" class="lm-input" required placeholder="Enter your password">
                            </div>
                            <button type="submit" class="lm-btn-submit">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Sign In
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
@if(Session::has('message'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modal = new bootstrap.Modal(document.getElementById('loginModal'));
        modal.show();
    });
</script>
@endif
</body>
</html>
