<aside id="sidebar" class="sidebar">
    @php $routeName = \Illuminate\Support\Facades\Route::currentRouteName(); @endphp
    <ul class="sidebar-nav" id="sidebar-nav">
        @if(session('code') == "TRIAGGE")
            <li class="nav-item">
                <a class="nav-link {{ $routeName == 'triagge' ? 'active' : 'collapsed' }}" href="{{ route('triagge') }}">
                    <i class="bi bi-hospital"></i>
                    <span>Triagge Desk</span>
                </a>
            </li>
            @if(false)
            <li class="nav-item">
                <a class="nav-link {{ $routeName == 'all_referrals' ? 'active' : 'collapsed' }}" href="{{ route('all_referrals') }}">
                    <i class="bi bi-clipboard2-data"></i>
                    <span>All Referrals</span>
                </a>
            </li>
            @endif
            <li class="nav-item">
                <a class="nav-link {{ $routeName == 'returned_referrals' ? 'active' : 'collapsed' }}" href="{{ route('returned_referrals') }}">
                    <i class="bi bi-arrow-return-left"></i>
                    <span>Returned Referrals</span>
                </a>
            </li>
        @else
            <li class="nav-item">
                <a class="nav-link {{ $routeName == 'referrals' ? 'active' : 'collapsed' }}" href="{{ route('referrals') }}">
                    <i class="bi bi-clipboard2-pulse"></i>
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
        @if(session('code') == "TRIAGGE")
            <li class="nav-item">
                <a class="nav-link {{ $routeName == 'queue_archives' ? 'active' : 'collapsed' }}" href="{{ route('queue_archives') }}">
                    <i class="bi bi-archive"></i>
                    <span>Queue Archives</span>
                </a>
            </li>
        @endif
    </ul>
</aside><!-- End Sidebar-->
