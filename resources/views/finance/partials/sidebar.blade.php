<!-- Mobile Sidebar -->
<div class="mobile-sidebar" id="mobileSidebar">
    <button class="close-btn" id="closeBtn">
        <img src="{{ asset('public/images/cross-white.svg') }}" alt="Close" />
    </button>
    <div class="mobile-logo">
        <img src="{{ asset('public/images/pink_me_logo.png') }}" alt="PINK ME Logo" />
    </div>
    <ul class="mobile-menu">
        <li class="{{ request()->routeIs('finance.dashboard') || request()->is('finance/dashboard') ? 'active' : '' }}">
            <a href="{{ route('finance.dashboard') }}">
                <img src="{{ request()->routeIs('finance.dashboard') || request()->is('finance/dashboard') ? asset('public/images/Dashboard-svg.svg') : asset('public/images/App-dash.svg') }}" alt="" class="w-5 h-5" />Dashboard
            </a>
        </li>
        <li class="{{ request()->routeIs('finance.registrations*') || request()->routeIs('finance.invoice*') || request()->is('finance/registrations*') ? 'active' : '' }}">
            <a href="{{ route('finance.registrations') }}">
                <img src="{{ request()->routeIs('finance.registrations*') || request()->routeIs('finance.invoice*') || request()->is('finance/registrations*') ? asset('public/images/document-pink.svg') : asset('public/images/document.svg') }}" alt="" />Patient Requests
            </a>
        </li>
    </ul>
</div>

{{-- Desktop Sidebar --}}
<div class="box relative">
    <div class="navigation">
        <ul>
            <li><a href="#"><img src="{{ asset('public/images/pink_me_logo.png') }}" alt="" /></a></li>
            <li class="{{ request()->routeIs('finance.dashboard') ? 'hovered' : '' }}">
                <a href="{{ route('finance.dashboard') }}">
                    <span class="icon"><img src="{{ request()->routeIs('finance.dashboard') ? asset('public/images/Dashboard-svg.svg') : asset('public/images/App-dash.svg') }}" alt="" class="w-5 h-5" /></span>
                    <span class="title">Dashboard</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('finance.registrations*') ? 'hovered' : '' }}">
                <a href="{{ route('finance.registrations') }}">
                    <span class="icon"><img src="{{ request()->routeIs('finance.registrations*') ? asset('public/images/document-pink.svg') : asset('public/images/document.svg') }}" alt="" /></span>
                    <span class="title">Patient Requests</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="sign-out">
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <span class="icon"><img src="{{ asset('images/signout.svg') }}" alt="Sign Out" /></span>
            <span class="title">Sign Out</span>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    </div>
</div>
