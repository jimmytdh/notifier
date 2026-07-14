<style>
    /* ── Stitch Navbar ── */
    #header.header {
        background: rgba(255,255,255,.95) !important;
        backdrop-filter: blur(10px);
        border-bottom: 1px solid #c2c6d2 !important;
        box-shadow: none !important;
        height: 60px !important;
        padding: 0 24px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
    }

    /* Brand */
    .nv-brand {
        display: flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
    }
    .nv-logo {
        width: 32px; height: 32px;
        border-radius: 8px;
        object-fit: contain;
        border: 1px solid #eceef0;
    }
    .nv-brand-name {
        font-family: 'Manrope', sans-serif;
        font-size: 18px;
        font-weight: 800;
        color: #003166;
        letter-spacing: -.01em;
    }

    /* Toggle */
    .nv-toggle {
        font-size: 22px;
        color: #424751;
        cursor: pointer;
        padding: 6px 8px;
        border-radius: 8px;
        transition: background .12s;
        line-height: 1;
        display: none;
    }
    .nv-toggle:hover { background: #f2f4f6; }
    @media (max-width: 1199px) { .nv-toggle { display: block; } }

    /* Right side */
    .nv-right {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-left: auto;
    }

    /* Profile button */
    .nv-profile-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        background: transparent;
        border: none;
        padding: 5px 10px;
        border-radius: 10px;
        cursor: pointer;
        transition: background .12s;
    }
    .nv-profile-btn:hover { background: #f2f4f6; }
    .nv-avatar {
        width: 32px; height: 32px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #c2c6d2;
    }
    .nv-user-name {
        font-family: 'Manrope', sans-serif;
        font-size: 13px;
        font-weight: 700;
        color: #191c1e;
    }
    .nv-user-role {
        font-size: 10px;
        color: #737782;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    /* Dropdown */
    .nv-dropdown {
        border: 1px solid #c2c6d2 !important;
        border-radius: 12px !important;
        box-shadow: 0 8px 24px rgba(0,49,102,.1) !important;
        padding: 8px !important;
        min-width: 200px;
        margin-top: 6px !important;
    }
    .nv-dropdown-header {
        padding: 8px 12px 10px;
        border-bottom: 1px solid #eceef0;
        margin-bottom: 6px;
    }
    .nv-dropdown-name {
        font-family: 'Manrope', sans-serif;
        font-size: 13px;
        font-weight: 700;
        color: #191c1e;
        margin: 0 0 2px;
    }
    .nv-dropdown-role {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .07em;
        text-transform: uppercase;
        color: #737782;
        margin: 0;
    }
    .nv-dropdown .dropdown-item {
        display: flex;
        align-items: center;
        gap: 9px;
        font-size: 13px;
        font-weight: 600;
        color: #424751;
        border-radius: 8px;
        padding: 9px 12px;
        transition: background .12s, color .12s;
    }
    .nv-dropdown .dropdown-item:hover {
        background: #f2f4f6;
        color: #003166;
    }
    .nv-dropdown .dropdown-item.text-danger { color: #ba1a1a !important; }
    .nv-dropdown .dropdown-item.text-danger:hover { background: #ffdad6 !important; }
    .nv-dropdown .dropdown-divider { border-color: #eceef0; margin: 4px 0; }
</style>

<header id="header" class="header fixed-top">

    <!-- Brand -->
    <a class="nv-brand" href="{{ session('access_rights') == 'TRIAGGE' ? route('triagge') : route('referrals') }}">
        <img src="assets/img/logo.png" class="nv-logo" alt="CSMC">
        <span class="nv-brand-name">Notifier</span>
    </a>

    <!-- Mobile toggle -->
    <i class="bi bi-list toggle-sidebar-btn nv-toggle"></i>

    <!-- Right actions -->
    <div class="nv-right">

        <!-- Profile dropdown -->
        <div class="dropdown">
            <button class="nv-profile-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"
                    style="text-decoration:none">
                <img src="{{ (Auth::user()->picture) ? 'https://dohcsmc.com/id/storage/crop/'.Auth::user()->picture : 'images/ic_user.png' }}?v={{ date('Ymdhis') }}"
                     alt="Profile" class="nv-avatar">
                <div class="d-none d-md-block text-start">
                    <div class="nv-user-name">
                        {{ Auth::user()->lname[0] }}. {{ Auth::user()->fname }}
                    </div>
                    <div class="nv-user-role" id="user_role">{{ session('access_rights') }}</div>
                </div>
            </button>

            <ul class="dropdown-menu dropdown-menu-end nv-dropdown">
                <li>
                    <div class="nv-dropdown-header">
                        <p class="nv-dropdown-name">{{ Auth::user()->fname.' '.Auth::user()->lname }}</p>
                        <p class="nv-dropdown-role">{{ session('access_rights') }}</p>
                    </div>
                </li>
                <li>
                    <a class="dropdown-item" href="#" id="change_password">
                        <i class="bi bi-key-fill"></i> Change Password
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger" href="{{ route('logout') }}" data-no-spa>
                        <i class="bi bi-box-arrow-right"></i> Sign Out
                    </a>
                </li>
            </ul>
        </div>

    </div>

    {{-- Hidden elements used by JS --}}
    <span class="d-none" id="user_code">{{ session('code') }}</span>

</header>
