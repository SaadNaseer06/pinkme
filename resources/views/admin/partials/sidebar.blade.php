<!-- Mobile Sidebar -->
<div class="mobile-sidebar" id="mobileSidebar">
    <!-- Close Button -->
    <button class="close-btn" id="closeBtn">
        <img src="{{ asset('public/images/cross-white.svg') }}" alt="Close" />
    </button>

    <!-- Logo -->
    <div class="mobile-logo">
        <img src="{{ asset('public/images/pink_me_logo.png') }}" alt="PINK ME Logo" />
    </div>

    <!-- Menu -->
    <ul class="mobile-menu">
        <li class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}"><img
                    src="{{ request()->is('admin/dashboard') ? asset('public/images/Dashboard-svg.svg') : asset('public/images/App-Dash.svg') }}"
                    alt="" />Dashboard</a>
        </li>
        {{-- <li class="{{ request()->is('admin/applications') ? 'active' : '' }}">
            <a href="{{ route('admin.applications') }}"><img
                    src="{{ request()->is('admin/applications') ? asset('public/images/App-app.svg') : asset('public/images/My_Application.svg') }}"
                    alt="" />Applications</a>
        </li> --}}
        <li class="{{ request()->is('admin/registrations*') ? 'active' : '' }}">
            <a href="{{ route('admin.registrations.index') }}">
                <img src="{{ request()->is('admin/registrations*') ? asset('public/images/document-pink.svg') : asset('public/images/document.svg') }}"
                     alt="Registrations" />
                Registrations
            </a>
        </li>
        <li class="{{ request()->is('admin/reviewers*') ? 'active' : '' }}">
            <a href="{{ route('admin.reviewers') }}"><img
                    src="{{ request()->is('admin/reviewers*') ? asset('public/images/review-pink.svg') : asset('public/images/review.svg') }}"
                    alt="Case Managers" />Case Managers</a>
        </li>
        <li class="{{ request()->is('admin/patients') ? 'active' : '' }}">
            <a href="{{ route('admin.patients') }}"><img
                    src="{{ request()->is('admin/patients') ? asset('public/images/patient-pink.svg') : asset('public/images/patient.svg') }}"
                    alt="" />Patients</a>
        </li>
        <li class="{{ request()->is('admin/sponsors*') ? 'active' : '' }}">
            <a href="{{ route('admin.sponsors') }}"><img
                    src="{{ request()->is('admin/sponsors*') ? asset('public/images/Sponsor-pink.svg') : asset('public/images/Sponsor.svg') }}"
                    alt="" />Sponsors</a>
        </li>
        <li class="{{ request()->is('admin/programs-events') || request()->is('admin/programs*') || request()->is('admin/events*') || request()->is('admin/events-registrations*') ? 'active' : '' }}">
            <a href="{{ route('admin.programs-events') }}"><img
                    src="{{ request()->is('admin/programs-events') || request()->is('admin/programs*') || request()->is('admin/events*') || request()->is('admin/events-registrations*') ? asset('public/images/program-svg.svg') : asset('public/images/program.svg') }}"
                    alt="" />Programs & Events</a>
        </li>
        <li class="{{ request()->is('admin/webinars*') ? 'active' : '' }}">
            <a href="{{ route('admin.webinars.index') }}"><img
                    src="{{ request()->is('admin/webinars*') ? asset('public/images/program-svg.svg') : asset('public/images/program.svg') }}"
                    alt="" />Webinars</a>
        </li>
        <li class="{{ request()->is('admin/settings') ? 'active' : '' }}">
            <a href="{{ route('admin.settings') }}"><img
                    src="{{ request()->is('admin/settings') ? asset('public/images/setting-pink.svg') : asset('public/images/setting.svg') }}"
                    alt="" />Setting</a>
        </li>
    </ul>
</div>


{{-- Desktop Sidebar --}}
<div class="box relative">
    <div class="navigation">
        <ul>
            <li><a href="#"><img src="{{ asset('public/images/pink_me_logo.png') }}" alt="Logo"></a></li>
            <li class="{{ request()->is('admin/dashboard') ? 'hovered' : '' }}">
                <a href="{{ route('admin.dashboard') }}">
                    <span class="icon"><img
                            src="{{ request()->is('admin/dashboard') ? asset('public/images/Dashboard-svg.svg') : asset('public/images/App-Dash.svg') }}"
                            alt="Dashboard Icon" class="w-5 h-5" /></span>
                    <span class="title">Dashboard</span>
                </a>
            </li>
            {{-- <li
                class="{{ request()->is('admin/applications*') || request()->routeIs('admin.viewApplication') ? 'hovered' : '' }}">
                <a href="{{ route('admin.applications') }}">
                    <span class="icon">
                        <img src="{{ request()->is('admin/applications*') || request()->routeIs('admin.viewApplication')
                            ? asset('public/images/App-app.svg')
                            : asset('public/images/My_Application.svg') }}"
                            class="w-5 h-5" />
                    </span>
                    <span class="title">Applications</span>
                </a>
            </li> --}}
            <li class="{{ request()->is('admin/registrations*') ? 'hovered' : '' }}">
                <a href="{{ route('admin.registrations.index') }}">
                    <span class="icon"><img
                            src="{{ request()->is('admin/registrations*') ? asset('public/images/document-pink.svg') : asset('public/images/document.svg') }}"
                            class="w-5 h-5" alt="Registrations" /></span>
                    <span class="title">Registrations</span>
                </a>
            </li>
            <li class="{{ request()->is('admin/reviewers*') ? 'hovered' : '' }}">
                <a href="{{ route('admin.reviewers') }}">
                    <span class="icon"><img
                            src="{{ request()->is('admin/reviewers*') ? asset('public/images/review-pink.svg') : asset('public/images/review.svg') }}"
                            class="w-5 h-5" alt="Case Managers" /></span>
                    <span class="title">Case Managers</span>
                </a>
            </li>
            <li class="{{ request()->is('admin/patients') ? 'hovered' : '' }}">
                <a href="{{ route('admin.patients') }}">
                    <span class="icon"><img
                            src="{{ request()->is('admin/patients') ? asset('public/images/patient-pink.svg') : asset('public/images/patient.svg') }}"
                            class="w-5 h-5" /></span>
                    <span class="title">Patients</span>
                </a>
            </li>
            <li class="{{ request()->is('admin/sponsors*') ? 'hovered' : '' }}">
                <a href="{{ route('admin.sponsors') }}">
                    <span class="icon"><img
                            src="{{ request()->is('admin/sponsors*') ? asset('public/images/Sponsor-pink.svg') : asset('public/images/Sponsor.svg') }}"
                            class="w-5 h-5" /></span>
                    <span class="title">Sponsors</span>
                </a>
            </li>
            <li
                class="{{ request()->is('admin/programs-events') || request()->is('admin/programs*') || request()->is('admin/events*') || request()->is('admin/events-registrations*') ? 'hovered' : '' }}">
                <a href="{{ route('admin.programs-events') }}">
                    <span class="icon"><img
                            src="{{ request()->is('admin/programs-events') || request()->is('admin/programs*') || request()->is('admin/events*') || request()->is('admin/events-registrations*') ? asset('public/images/program-svg.svg') : asset('public/images/program.svg') }}"
                            class="w-5 h-5" /></span>
                    <span class="title">Programs & Events</span>
                </a>
            </li>
            <li class="{{ request()->is('admin/webinars*') ? 'hovered' : '' }}">
                <a href="{{ route('admin.webinars.index') }}">
                    <span class="icon"><img
                            src="{{ request()->is('admin/webinars*') ? asset('public/images/program-svg.svg') : asset('public/images/program.svg') }}"
                            class="w-5 h-5" /></span>
                    <span class="title">Webinars</span>
                </a>
            </li>
            <li class="{{ request()->is('admin/settings') ? 'hovered' : '' }}">
                <a href="{{ route('admin.settings') }}">
                    <span class="icon"><img
                            src="{{ request()->is('admin/settings') ? asset('public/images/setting-pink.svg') : asset('public/images/setting.svg') }}" /></span>
                    <span class="title">Settings</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="sign-out">
        <a href="{{ route('logout') }}"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <span class="icon">
                <img src="{{ asset('images/signout.svg') }}" alt="Sign Out" />
            </span>
            <span class="title">Sign Out</span>
        </a>
        
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>

    </div>
</div>
