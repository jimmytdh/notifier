<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CSMC PatientFlow Login</title>
    <link href="assets/img/favicon.png" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bg: #edf3f8;
            --panel: #ffffff;
            --text: #12263f;
            --muted: #60758f;
            --brand: #0f766e;
            --brand-strong: #0b5f58;
            --accent: #d97706;
        }

        body {
            min-height: 100vh;
            margin: 0;
            font-family: "Manrope", sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at 0% 0%, rgba(15, 118, 110, 0.2), rgba(15, 118, 110, 0) 40%),
                radial-gradient(circle at 100% 0%, rgba(217, 119, 6, 0.18), rgba(217, 119, 6, 0) 30%),
                var(--bg);
            display: grid;
            place-items: center;
            padding: 20px;
        }

        .login-shell {
            width: 100%;
            max-width: 980px;
            background: var(--panel);
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid #dce7f3;
            box-shadow: 0 24px 60px rgba(18, 38, 63, 0.14);
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .brand-side {
            background:
                linear-gradient(155deg, rgba(15, 118, 110, 0.96), rgba(11, 95, 88, 0.98)),
                var(--brand);
            color: #f8fffe;
            padding: 44px;
            position: relative;
        }

        .brand-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            padding: 8px 12px;
            background: rgba(255, 255, 255, 0.14);
            font-size: 12px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-weight: 700;
        }

        .brand-side h1 {
            font-family: "Plus Jakarta Sans", sans-serif;
            font-size: 34px;
            line-height: 1.15;
            margin: 22px 0 10px;
        }

        .brand-side p {
            opacity: 0.9;
            max-width: 360px;
            margin: 0;
        }

        .form-side {
            padding: 42px 36px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .logo-wrap img {
            width: 88px;
            height: 88px;
            object-fit: contain;
            border-radius: 50%;
            border: 4px solid #eef5ff;
            background: #fff;
        }

        .form-title {
            font-family: "Plus Jakarta Sans", sans-serif;
            font-size: 26px;
            margin: 12px 0 2px;
        }

        .form-subtitle {
            color: var(--muted);
            font-size: 14px;
            margin-bottom: 24px;
        }

        .form-control {
            height: 48px;
            border-radius: 12px;
            border: 1px solid #c9d8ea;
        }

        .form-control:focus {
            border-color: rgba(15, 118, 110, 0.45);
            box-shadow: 0 0 0 0.2rem rgba(15, 118, 110, 0.12);
        }

        .btn-login {
            height: 48px;
            border: 0;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--brand), var(--brand-strong));
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #0d6861, #09524c);
        }

        .alert {
            border-radius: 12px;
        }

        .copyright {
            margin-top: 14px;
            font-size: 12px;
            color: var(--muted);
            text-align: center;
        }

        @media (max-width: 900px) {
            .login-shell {
                grid-template-columns: 1fr;
            }

            .brand-side {
                padding: 26px 24px;
            }

            .brand-side h1 {
                font-size: 26px;
            }
        }
    </style>
</head>
<body>
<div class="login-shell">
    <div class="brand-side">
        <span class="brand-chip">CSMC Digital</span>
        <h1>Inter-Departmental Referral</h1>
        <p>Once a patient is referred from another hospital to CSMC, this system handles inter-department forwarding, tracks each movement, and keeps receiving departments informed.</p>
    </div>
    <div class="form-side">
        <div class="logo-wrap text-center">
            <img src="assets/img/logo.png" alt="CSMC Logo">
        </div>
        <h2 class="form-title text-center">CSMC Notifier</h2>
        <div class="form-subtitle text-center">Sign in to continue</div>

        @if(Session::has('message'))
            <div class="alert alert-danger text-center">
                {{ Session::get('message') }}
            </div>
        @endif

        <form method="post" action="{{ route('authenticate') }}">
            {{ csrf_field() }}
            <div class="mb-3">
                <label for="inputUsername" class="form-label">Username</label>
                <input type="text" name="username" id="inputUsername" class="form-control" required autofocus>
            </div>
            <div class="mb-4">
                <label for="inputPassword" class="form-label">Password</label>
                <input type="password" name="password" id="inputPassword" class="form-control" required>
            </div>
            <button class="btn btn-success btn-login w-100" type="submit">Login</button>
        </form>
        <div class="copyright">Cebu South Medical Center</div>
    </div>
</div>
</body>
</html>
