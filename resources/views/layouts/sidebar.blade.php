<aside id="sidebar" class="sidebar">
    @php $routeName = \Illuminate\Support\Facades\Route::currentRouteName(); @endphp
    <ul class="sidebar-nav" id="sidebar-nav">
        @if(session('access_rights') == "TRIAGGE")
            <li class="nav-item">
                <a class="nav-link {{ $routeName == 'triagge' ? 'active' : 'collapsed' }}" href="{{ route('triagge') }}">
                    <i class="bi bi-hospital"></i>
                    <span>Triagge Desk</span>
                </a>
            </li>
        @else
            <li class="nav-item">
                <a class="nav-link {{ $routeName == 'referrals' ? 'active' : 'collapsed' }}" href="{{ route('referrals') }}">
                    <i class="bi bi-clipboard2-pulse"></i>
                    <span>Referrals</span>
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
                    <i class="bi bi-activity"></i>
                    <span>Patient Timeline</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $routeName == 'referral_queue' ? 'active' : 'collapsed' }}" href="{{ route('referral_queue') }}">
                    <i class="bi bi-hourglass-split"></i>
                    <span>Referral Queue</span>
                </a>
            </li>
    </ul>
</aside><!-- End Sidebar-->
