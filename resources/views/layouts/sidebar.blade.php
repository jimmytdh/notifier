<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
        @if(session('access_rights') == "TRIAGGE")
            <li class="nav-item">
                <a class="nav-link collapsed" href="{{ route('triagge') }}">
                    <i class="bi bi-grid"></i>
                    <span>Triagge</span>
                </a>
            </li>
        @else
            <li class="nav-item">
                <a class="nav-link collapsed" href="{{ route('referrals') }}">
                    <i class="bi bi-grid"></i>
                    <span>Referrals</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link collapsed" href="{{ route('track_referrals') }}">
                    <i class="bi bi-grid"></i>
                    <span>Track Referrals</span>
                </a>
            </li>
        @endif
    </ul>
</aside><!-- End Sidebar-->
