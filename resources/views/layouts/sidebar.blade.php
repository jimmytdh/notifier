<style>
    /* ── Stitch Sidebar ── */
    #sidebar.sidebar {
        background: #f2f4f6 !important;
        border-right: 1px solid #c2c6d2 !important;
        box-shadow: none !important;
        width: 260px;
        display: flex;
        flex-direction: column;
        padding: 0 !important;
    }

    /* Profile block */
    .sb-profile {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 20px 16px 16px;
        border-bottom: 1px solid #c2c6d2;
    }
    .sb-initials {
        width: 40px; height: 40px;
        border-radius: 50%;
        background: #003166;
        color: #fff;
        font-family: 'Manrope', sans-serif;
        font-size: 13px;
        font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        letter-spacing: .02em;
    }
    .sb-name {
        font-family: 'Manrope', sans-serif;
        font-size: 13px;
        font-weight: 700;
        color: #191c1e;
        margin: 0 0 2px;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .sb-role {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .07em;
        text-transform: uppercase;
        color: #737782;
        margin: 0;
    }

    /* New Referral button */
    .sb-new-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        background: #003166;
        color: #fff;
        border: none;
        font-family: 'Manrope', sans-serif;
        font-size: 13px;
        font-weight: 700;
        padding: 10px 16px;
        border-radius: 10px;
        text-decoration: none;
        transition: opacity .15s, transform .1s;
        margin: 14px 16px 4px;
    }
    .sb-new-btn:hover  { opacity: .88; color: #fff; }
    .sb-new-btn:active { transform: scale(.97); }

    /* Nav */
    #sidebar .sidebar-nav {
        list-style: none;
        padding: 10px 10px 0;
        margin: 0;
        flex: 1;
        overflow-y: auto;
    }
    #sidebar .sidebar-nav .nav-item { margin-bottom: 2px; }
    #sidebar .sidebar-nav .nav-link {
        display: flex !important;
        align-items: center;
        gap: 10px;
        padding: 10px 14px !important;
        border-radius: 10px !important;
        font-family: 'Manrope', sans-serif !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        color: #424751 !important;
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        transition: background .12s, color .12s !important;
        text-decoration: none;
    }
    #sidebar .sidebar-nav .nav-link i {
        font-size: 16px;
        width: 20px;
        text-align: center;
        flex-shrink: 0;
    }
    #sidebar .sidebar-nav .nav-link:hover {
        background: #e0e3e5 !important;
        color: #191c1e !important;
    }
    #sidebar .sidebar-nav .nav-link.active {
        background: #acedda !important;
        color: #2e6d5f !important;
        font-weight: 700 !important;
        box-shadow: none !important;
        border-color: transparent !important;
    }
    #sidebar .sidebar-nav .nav-link.active i { color: #2e6d5f !important; }
    #sidebar .sidebar-nav .nav-link.collapsed {
        background: transparent !important;
        color: #424751 !important;
    }

    /* Footer links */
    .sb-footer {
        border-top: 1px solid #c2c6d2;
        padding: 10px;
        margin-top: auto;
    }
    .sb-footer a {
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 9px 14px;
        border-radius: 10px;
        font-family: 'Manrope', sans-serif;
        font-size: 13px;
        font-weight: 600;
        color: #424751;
        text-decoration: none;
        transition: background .12s, color .12s;
        margin-bottom: 2px;
    }
    .sb-footer a:hover { background: #e0e3e5; color: #191c1e; }
    .sb-footer a.sb-signout { color: #ba1a1a; }
    .sb-footer a.sb-signout:hover { background: #ffdad6; color: #ba1a1a; }
    .sb-footer a i { font-size: 15px; width: 18px; text-align: center; }
</style>

@php
    $routeName = \Illuminate\Support\Facades\Route::currentRouteName();
    $user      = \Illuminate\Support\Facades\Auth::user();
    $initials  = strtoupper(substr($user->fname, 0, 1) . substr($user->lname, 0, 1));
    $fullName  = $user->fname . ' ' . $user->lname;
@endphp

<aside id="sidebar" class="sidebar">

    <!-- Navigation -->
    <ul class="sidebar-nav" id="sidebar-nav">

        @if(session('code') == 'TRIAGGE')
        <li class="nav-item">
            <a class="nav-link {{ $routeName == 'triagge' ? 'active' : 'collapsed' }}" href="{{ route('triagge') }}">
                <i class="bi bi-hospital-fill"></i>
                <span>Triagge Desk</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $routeName == 'returned_referrals' ? 'active' : 'collapsed' }}" href="{{ route('returned_referrals') }}">
                <i class="bi bi-arrow-return-left"></i>
                <span>Returned Referrals</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $routeName == 'manual_referral' ? 'active' : 'collapsed' }}" href="{{ route('manual_referral') }}">
                <i class="bi bi-pencil-square"></i>
                <span>Manual Referral</span>
            </a>
        </li>
        @else
        <li class="nav-item">
            <a class="nav-link {{ $routeName == 'referrals' ? 'active' : 'collapsed' }}" href="{{ route('referrals') }}">
                <i class="bi bi-clipboard2-pulse-fill"></i>
                <span>Incoming Referrals</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $routeName == 'manual_referral' ? 'active' : 'collapsed' }}" href="{{ route('manual_referral') }}">
                <i class="bi bi-pencil-square"></i>
                <span>Manual Referral</span>
            </a>
        </li>
        @endif

        <li class="nav-item">
            <a class="nav-link {{ $routeName == 'track_referrals' ? 'active' : 'collapsed' }}" href="{{ route('track_referrals') }}">
                <i class="bi bi-search"></i>
                <span>Track Referrals</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $routeName == 'referral_queue' ? 'active' : 'collapsed' }}" href="{{ route('referral_queue') }}">
                <i class="bi bi-hourglass-split"></i>
                <span>Referral Queue</span>
            </a>
        </li>
    </ul>
</aside>
