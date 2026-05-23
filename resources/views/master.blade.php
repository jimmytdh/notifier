<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="theme-color" content="#0f172a">

    <title>@yield('Title', 'Notifier') </title>
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
        .modal {
            z-index: 2000;
        }
        .modal-backdrop {
            z-index: 1990;
        }
        .speaker-modal-body p {
            color: #4b5563;
            font-size: 14px;
            margin-bottom: 0;
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
    <section class="section dashboard">
        <div class="row g-3">
            <div id="spa-content" class="col-12 p-0">
                @yield('content')
            </div>
        </div>
    </section>

</main>
<!-- End #main -->
<div class="modal fade" id="speaker_permission_modal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body speaker-modal-body">
                <h5 class="mb-2">Enable Sound Alerts</h5>
                <p class="mb-3">Sound alerts are required for incoming referrals. Please enable the speaker to continue.</p>
                <button class="btn btn-success w-100" id="btn_enable_speaker">Enable Speaker</button>
            </div>
        </div>
    </div>
</div>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    .swal2-popup { font-family: 'Manrope', sans-serif !important; border-radius: 16px !important; }
    .swal2-title { font-size: 18px !important; font-weight: 700 !important; color: #191c1e !important; }
    .swal2-html-container { font-size: 13px !important; color: #424751 !important; }
    .swal2-input { border-radius: 10px !important; border: 1px solid #c2c6d2 !important; font-size: 13px !important; }
    .swal2-input:focus { border-color: #00478f !important; box-shadow: 0 0 0 3px rgba(0,71,143,.1) !important; }
    .swal2-confirm, .swal2-cancel { border-radius: 9px !important; font-family: 'Manrope', sans-serif !important; font-size: 13px !important; font-weight: 700 !important; padding: 10px 22px !important; }
    .swal2-cancel { background: #f2f4f6 !important; color: #424751 !important; }
    .swal2-cancel:hover { background: #e0e3e5 !important; }
    .swal2-container { z-index: 99999 !important; }
    .swal2-backdrop-show { z-index: 99998 !important; }
</style>
<script>
    // Prevent Bootstrap modal focus trap from stealing focus when SweetAlert is open
    document.addEventListener('focusin', function (e) {
        if (document.querySelector('.swal2-container')) {
            e.stopImmediatePropagation();
        }
    }, true);

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
        const speakerModalEl = document.getElementById('speaker_permission_modal');
        const speakerButton = document.getElementById('btn_enable_speaker');
        let speakerModalInstance = null;

        function canSpeak() {
            return !!(window.responsiveVoice || window.speechSynthesis);
        }

        function isSpeakerEnabled() {
            return localStorage.getItem('speaker_enabled') === '1';
        }

        function setSpeakerEnabled(enabled) {
            localStorage.setItem('speaker_enabled', enabled ? '1' : '0');
        }

        function speak(text) {
            if (window.responsiveVoice) {
                window.responsiveVoice.speak(text);
                return;
            }
            if (window.speechSynthesis) {
                const msg = new SpeechSynthesisUtterance(text);
                window.speechSynthesis.speak(msg);
            }
        }

        function syncSpeakerModal() {
            if (!speakerModalEl) return;
            if (!speakerModalInstance && window.bootstrap && window.bootstrap.Modal) {
                speakerModalInstance = new bootstrap.Modal(speakerModalEl);
            }
            if (!canSpeak()) return;
            if (isSpeakerEnabled()) {
                if (speakerModalInstance) speakerModalInstance.hide();
            } else {
                if (speakerModalInstance) speakerModalInstance.show();
            }
        }

        if (speakerButton) {
            speakerButton.addEventListener('click', function () {
                setSpeakerEnabled(true);
                speak('Sound alerts enabled');
                syncSpeakerModal();
            });
        }

        function initGlobalReferralNotifications() {
            if (!window.observe || !window.get) return;
            const department = (document.getElementById('user_department')?.textContent || '').trim();
            const userCode = (document.getElementById('user_code')?.textContent || '').trim();
            const topic = department === 'TRIAGGE' ? '23' : userCode;
            if (!topic) return;

            let isInit = false;
            observe(topic, function (snapshot) {
                if (!isInit || !snapshot || !snapshot.val()) return;
                toastr.success('New incoming referral', 'New Referral');
                if (isSpeakerEnabled()) {
                    speak('New incoming referral');
                }
                const isReferralsPage = window.location.pathname === '/referrals';
                if (isReferralsPage && typeof window.refreshReferrals === 'function') {
                    window.refreshReferrals();
                }
            });
            get(topic, function () {
                isInit = true;
            });
        }

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

        function hoistModals() {
            document.querySelectorAll('#spa-content .modal').forEach(modal => {
                if (modal.id) {
                    document.querySelectorAll('body #' + modal.id).forEach(function (el) {
                        if (el !== modal) el.remove();
                    });
                }
                document.body.appendChild(modal);
            });
        }
        window.hoistModals = hoistModals;

        function swapTo(doc, urlPath) {
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
            const newTitle = doc.title || '';
            if (newTitle) document.title = newTitle;

            const incomingTitle = doc.querySelector('#page-title');
            const incomingSubtitle = doc.querySelector('#page-subtitle');
            if (incomingTitle) titleEl.textContent = incomingTitle.textContent;
            if (incomingSubtitle) subtitleEl.textContent = incomingSubtitle.textContent;

            const path = urlPath || window.location.pathname;
            setActiveNav(path);
            syncSpeakerModal();
            initGlobalReferralNotifications();

            setTimeout(() => {
                contentEl.innerHTML = newContent.innerHTML;
                contentEl.classList.remove('spa-fade-out');
                contentEl.classList.add('spa-fade-in');
                hoistModals();
                populateDepartmentSelects();
                runPageScripts(doc);
            }, 120);
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
                    const urlPath = new URL(url, window.location.origin).pathname;
                    swapTo(doc, urlPath);
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

        window._departments = [];

        function populateDepartmentSelects() {
            const depts = window._departments;
            if (!depts.length) return;
            document.querySelectorAll('.dept-select-dynamic').forEach(function (select) {
                const current = select.value;
                while (select.options.length > 1) select.remove(1);
                depts.forEach(function (dept) {
                    const opt = document.createElement('option');
                    opt.value = dept.code;
                    opt.textContent = dept.department;
                    select.appendChild(opt);
                });
                select.value = current;
            });
            document.querySelectorAll('.dept-filters-dynamic').forEach(function (container) {
                const active = container.querySelector('.btn-filter.btn-success');
                const activeFilter = active ? active.getAttribute('data-filter') : null;
                container.innerHTML = '';
                depts.forEach(function (dept) {
                    const btn = document.createElement('button');
                    btn.className = 'btn btn-secondary btn-filter' + (activeFilter === dept.code ? ' btn-success' : '');
                    btn.setAttribute('data-filter', dept.code);
                    btn.textContent = dept.department;
                    container.appendChild(btn);
                });
            });
        }
        window.populateDepartmentSelects = populateDepartmentSelects;

        const REFERRAL_QUEUE_DEPTS = ['IM', 'SURGERY', 'OB', 'PEDIA'];

        window.setQueueDeptOptions = function (type) {
            const select = document.getElementById('queue_dept_select');
            if (!select) return;
            const isReferral = !type || type.toString().trim().toLowerCase() !== 'manual';
            const depts = isReferral
                ? window._departments.filter(function (d) { return REFERRAL_QUEUE_DEPTS.indexOf((d.code || '').toUpperCase()) !== -1; })
                : window._departments;
            while (select.options.length > 1) select.remove(1);
            depts.forEach(function (dept) {
                const opt = document.createElement('option');
                opt.value = dept.code;
                opt.textContent = dept.department;
                select.appendChild(opt);
            });
            select.value = '';
        };

        function loadDepartments() {
            fetch('{{ route('getDepartments') }}')
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (data.results) {
                        window._departments = data.results;
                        populateDepartmentSelects();
                    }
                })
                .catch(function () {});
        }

        hoistModals();
        loadDepartments();
        syncSpeakerModal();
        initGlobalReferralNotifications();

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
