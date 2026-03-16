<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="theme-color" content="#0f172a">

    <title>@yield('Title', 'Patient Flow Tracker') - {{ session('access_rights') }}</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicons -->
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

<!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="assets/css/style.css?v={{ date('ymdhis') }}" rel="stylesheet">
    <link href="css/toastr.min.css?v={{ date('ymdhis') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <style>
        .spa-fade-out {
            opacity: 0;
            transition: opacity 160ms ease;
        }
        .spa-fade-in {
            opacity: 1;
            transition: opacity 200ms ease;
        }
        .spa-loading #spa-content {
            filter: blur(3px);
            opacity: 0.6;
            pointer-events: none;
        }
        .sidebar-nav .nav-link.active {
            border-color: rgba(15, 118, 110, 0.35);
            background: rgba(15, 118, 110, 0.18);
            color: var(--pf-brand-strong);
            box-shadow: inset 0 0 0 1px rgba(15, 118, 110, 0.06);
        }
        .sidebar-nav .nav-link.active i {
            color: var(--pf-brand-strong);
        }
    </style>
    <!-- =======================================================
    * Template Name: NiceAdmin - v2.5.0
    * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
    * Author: BootstrapMade.com
    * License: https://bootstrapmade.com/license/
    ======================================================== -->
</head>

<body>
<div class="fullscreen-modal d-none" id="spinner">
    <div class="spinner-border text-light" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
    <div class="text-white fs-5 mt-1">Loading, please wait ...</div>
</div>
<!-- ======= Header ======= -->
@include('layouts.navbar')
<!-- ======= Sidebar ======= -->
@include('layouts.sidebar')
@include('modals.settings')
<main id="main" class="main">
    <div class="pagetitle page-header">
        <div>
            <h1 id="page-title">@yield('Title', 'Patient Tracking')</h1>
            <div class="page-subtitle" id="page-subtitle">@yield('Subtitle', 'Inter-department patient forwarding from external referrals with full tracking and department notifications.')</div>
        </div>
    </div>
    <section class="section dashboard">
        <div class="row g-3">
            <div id="spa-content" class="col-12 p-0">
                @yield('content')
            </div>
        </div>
    </section>

</main>
<!-- End #main -->
<!-- ======= Footer ======= -->
<footer id="footer" class="footer">
    <div class="copyright">
        <strong><span>Patient Flow Tracker</span></strong> &copy; {{ date('Y') }}. All rights reserved.
    </div>
</footer><!-- End Footer -->
<div class="d-none" id="user_department">{{ session('access_rights') }}</div>
<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
        class="bi bi-arrow-up-short"></i></a>

<!-- Vendor JS Files -->
<script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/chart.js/chart.umd.js"></script>
<script src="assets/vendor/echarts/echarts.min.js"></script>
<script src="assets/vendor/quill/quill.min.js"></script>
<script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
<script src="assets/vendor/tinymce/tinymce.min.js"></script>
<script src="assets/vendor/php-email-form/validate.js"></script>
<!-- Template Main JS File -->
<script src="assets/js/main.js"></script>
<script src="js/jquery.min.js"></script>
<script src="js/moment.min.js"></script>
<script src="js/toastr.min.js"></script>
<script src="https://code.responsivevoice.org/responsivevoice.js?key=kiFSuLiB"></script>
<script>
    const _DEPARTMENTS = ['PEDIA','IM','SURGERY','OB','ER','GS'];
    toastr.options = {
        "positionClass": "toast-bottom-right",
    }
    const settings_modal = new bootstrap.Modal(document.getElementById('settings_modal'));
    $(document).ready(function () {
        $("#change_password").click(function (e) {
            e.preventDefault();
            settings_modal.toggle();
        });

        $("#btn_change_password").click(function () {
            let is_valid = true;
            $("#settings_modal .is-invalid").removeClass("is-invalid");
            $("#settings_modal .error_container").addClass("d-none");
            $("#settings_modal .error_list").empty();

            const old_password = $("#old_password").val().trim();
            const new_password = $("#new_password").val().trim();
            const confirm_password = $("#confirm_password").val().trim();

            if (old_password == "") {
                is_valid = false;
                $("#settings_modal .error_list").append("<li>Old password is required</li>");
                $("#old_password").addClass("is-invalid");
            }

            if (new_password == "") {
                is_valid = false;
                $("#settings_modal .error_list").append("<li>New password is required</li>");
                $("#old_password").addClass("is-invalid");
            }

            if (confirm_password == "") {
                is_valid = false;
                $("#settings_modal .error_list").append("<li>Confirm password is required</li>");
                $("#old_password").addClass("is-invalid");
            }

            if(new_password.length < 4){
                is_valid = false;
                $("#settings_modal .error_list").append("<li>New password must at least 4 characters</li>");
                $("#new_password").addClass("is-invalid");
            }

            if(new_password != confirm_password){
                is_valid = false;
                $("#settings_modal .error_list").append("<li>Password mismatch</li>");
                $("#new_password").addClass("is-invalid");
            }

            if (!is_valid) {
                $("#settings_modal .error_container").removeClass("d-none");
                return;
            }

            const params = {
                old_password: old_password,
                new_password: new_password,
                confirm_password: confirm_password
            }

            fetch('{{route('changePassword')}}', {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify(params)
            })
                .then(res => res.json())
                .then(data => {
                    if (data.code == 500) {
                        toastr.error(data.message, "Something went wrong")
                        return;
                    }

                    settings_modal.toggle();
                    toastr.success(data.message, "Information");
                    $("#old_password").val("");
                    $("#new_password").val("");
                    $("#confirm_password").val("");
                }).catch(err => {
                alert(err);
            });
        });
    });
</script>
@include('js.firebase-config')
<div id="page-scripts">
    @yield('js')
</div>
<script>
    (function () {
        const contentEl = document.getElementById('spa-content');
        const titleEl = document.getElementById('page-title');
        const subtitleEl = document.getElementById('page-subtitle');
        const spinner = document.getElementById('spinner');

        function isSameOrigin(url) {
            try {
                const target = new URL(url, window.location.origin);
                return target.origin === window.location.origin;
            } catch (e) {
                return false;
            }
        }

        function setActiveNav(urlPath) {
            const links = document.querySelectorAll('#sidebar .nav-link');
            links.forEach(link => {
                const href = link.getAttribute('href') || '';
                let hrefPath = href;
                try {
                    hrefPath = new URL(href, window.location.origin).pathname;
                } catch (e) {
                    hrefPath = href;
                }
                if (hrefPath === urlPath) {
                    link.classList.remove('collapsed');
                    link.classList.add('active');
                } else {
                    link.classList.add('collapsed');
                    link.classList.remove('active');
                }
            });
        }

        function runPageScripts(doc) {
            document.querySelectorAll('script[data-spa-script="1"]').forEach(el => el.remove());
            const scriptContainer = doc.querySelector('#page-scripts');
            if (!scriptContainer) return;
            const scripts = scriptContainer.querySelectorAll('script');
            scripts.forEach(script => {
                const newScript = document.createElement('script');
                if (script.src) {
                    newScript.src = script.src;
                } else {
                    newScript.text = script.textContent;
                }
                newScript.setAttribute('data-spa-script', '1');
                document.body.appendChild(newScript);
            });
        }

        function cleanupModals() {
            if (window.bootstrap && window.bootstrap.Modal) {
                document.querySelectorAll('.modal.show').forEach(el => {
                    const instance = window.bootstrap.Modal.getInstance(el);
                    if (instance) {
                        instance.hide();
                        instance.dispose();
                    } else {
                        el.classList.remove('show');
                        el.style.display = 'none';
                        el.setAttribute('aria-hidden', 'true');
                        el.removeAttribute('aria-modal');
                        el.removeAttribute('role');
                    }
                });
            }

            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        }
        window.cleanupModals = cleanupModals;

        function swapTo(doc) {
            const newContent = doc.querySelector('#spa-content');
            if (!newContent) {
                window.location.reload();
                return;
            }

            if (window.clearFirebaseListeners) {
                window.clearFirebaseListeners();
            }

            cleanupModals();

            contentEl.classList.remove('spa-fade-in');
            contentEl.classList.add('spa-fade-out');
            setTimeout(() => {
                contentEl.innerHTML = newContent.innerHTML;
                contentEl.classList.remove('spa-fade-out');
                contentEl.classList.add('spa-fade-in');
            }, 120);

            const newTitle = doc.title || '';
            if (newTitle) document.title = newTitle;

            const incomingTitle = doc.querySelector('#page-title');
            const incomingSubtitle = doc.querySelector('#page-subtitle');
            if (incomingTitle) titleEl.textContent = incomingTitle.textContent;
            if (incomingSubtitle) subtitleEl.textContent = incomingSubtitle.textContent;

            runPageScripts(doc);
            setActiveNav(window.location.pathname);
        }

        function fetchPage(url, pushState) {
            cleanupModals();
            if (spinner) spinner.classList.remove('d-none');
            document.body.classList.add('spa-loading');
            fetch(url, {headers: {'X-SPA': '1'}})
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    swapTo(doc);
                    if (pushState) {
                        history.pushState({}, '', url);
                    }
                })
                .catch(() => window.location.assign(url))
                .finally(() => {
                    if (spinner) spinner.classList.add('d-none');
                    document.body.classList.remove('spa-loading');
                });
        }

        function shouldHandleLink(link) {
            if (!link || !link.href) return false;
            if (link.target && link.target !== '_self') return false;
            if (link.hasAttribute('data-no-spa')) return false;
            if (!isSameOrigin(link.href)) return false;
            if (link.getAttribute('href').startsWith('#')) return false;
            return true;
        }

        document.addEventListener('click', function (e) {
            const link = e.target.closest('a');
            if (!shouldHandleLink(link)) return;
            e.preventDefault();
            const url = link.getAttribute('href');
            if (url === window.location.pathname) return;
            fetchPage(url, true);
        });

        window.addEventListener('popstate', function () {
            fetchPage(window.location.pathname, false);
        });
    })();
</script>
</body>

</html>
