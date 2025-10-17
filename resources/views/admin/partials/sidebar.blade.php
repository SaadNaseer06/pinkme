<!-- Mobile Sidebar -->
<div class="mobile-sidebar" id="mobileSidebar">
    <!-- Close Button -->
    <button class="close-btn" id="closeBtn">
        <img src="{{ asset('images/cross-white.svg') }}" alt="Close" />
    </button>

    <!-- Logo -->
    <div class="mobile-logo">
        <img src="{{ asset('images/logo-white.png') }}" alt="PINK ME Logo" />
    </div>

    <!-- Menu -->
    <ul class="mobile-menu">
        <li class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}"><img
                    src="{{ request()->is('admin/dashboard') ? asset('images/Dashboard-svg.svg') : asset('images/App-Dash.svg') }}"
                    alt="" />Dashboard</a>
        </li>
        <li class="{{ request()->is('admin/applications') ? 'active' : '' }}">
            <a href="{{ route('admin.applications') }}"><img
                    src="{{ request()->is('admin/applications') ? asset('images/App-app.svg') : asset('images/My_Application.svg') }}"
                    alt="" />Applications</a>
        </li>
        <li class="{{ request()->is('admin/assigned') ? 'active' : '' }}">
            <a href="{{ route('admin.assigned') }}"><img
                    src="{{ request()->is('admin/assigned') ? asset('images/App-app.svg') : asset('images/My_Application.svg') }}"
                    alt="" />Assigned
                Applications</a>
        </li>
        <li class="{{ request()->is('admin/program-registration-requests*') ? 'active' : '' }}">
            <a href="{{ route('admin.program_registrations.index') }}"><img
                    src="{{ request()->is('admin/program-registration-requests*') ? asset('images/App-app.svg') : asset('images/My_Application.svg') }}"
                    alt="" />Program
                Registrations</a>
        </li>
        <li class="{{ request()->is('admin/reviewers') ? 'active' : '' }}">
            <a href="{{ route('admin.reviewers') }}"><img
                    src="{{ request()->is('admin/reviewers') ? asset('images/patient-pink.svg') : asset('images/patient.svg') }}"
                    alt="" />Reviewers</a>
        </li>
        <li class="{{ request()->is('admin/case-managers*') ? 'active' : '' }}">
            <a href="{{ route('admin.case-managers.index') }}"><img
                    src="{{ request()->is('admin/case-managers*') ? asset('images/patient-pink.svg') : asset('images/patient.svg') }}"
                    alt="" />Case Managers</a>
        </li>
        <li class="{{ request()->is('admin/patients') ? 'active' : '' }}">
            <a href="{{ route('admin.patients') }}"><img
                    src="{{ request()->is('admin/patients') ? asset('images/patient-pink.svg') : asset('images/patient.svg') }}"
                    alt="" />Patients</a>
        </li>
        <li class="{{ request()->is('admin/sponsors') ? 'active' : '' }}">
            <a href="{{ route('admin.sponsors') }}"><img
                    src="{{ request()->is('admin/sponsors') ? asset('images/Sponsor-pink.svg') : asset('images/Sponsor.svg') }}"
                    alt="" />Sponsers & Events</a>
        </li>
        <li class="{{ request()->is('admin/settings') ? 'active' : '' }}">
            <a href="{{ route('admin.settings') }}"><img
                    src="{{ request()->is('admin/settings') ? asset('images/setting-pink.svg') : asset('images/setting.svg') }}"
                    alt="" />Setting</a>
        </li>
    </ul>
</div>


{{-- Desktop Sidebar --}}
<div class="box relative">
    <div class="navigation">
        <ul>
            <li><a href="#"><img src="{{ asset('images/logo-white.png') }}" alt="Logo"></a></li>
            <li class="{{ request()->is('admin/dashboard') ? 'hovered' : '' }}">
                <a href="{{ route('admin.dashboard') }}">
                    <span class="icon"><img
                            src="{{ request()->is('admin/dashboard') ? asset('images/Dashboard-svg.svg') : asset('images/App-Dash.svg') }}"
                            alt="Dashboard Icon" class="w-5 h-5" /></span>
                    <span class="title">Dashboard</span>
                </a>
            </li>
            <li
                class="{{ request()->is('admin/applications*') || request()->routeIs('admin.viewApplication') ? 'hovered' : '' }}">
                <a href="{{ route('admin.applications') }}">
                    <span class="icon">
                        <img src="{{ request()->is('admin/applications*') || request()->routeIs('admin.viewApplication')
                            ? asset('images/App-app.svg')
                            : asset('images/My_Application.svg') }}"
                            class="w-5 h-5" />
                    </span>
                    <span class="title">Applications</span>
                </a>
            </li>
            <li class="{{ request()->is('admin/assigned') ? 'hovered' : '' }}">
                <a href="{{ route('admin.assigned') }}">
                    <span class="icon"><img
                            src="{{ request()->is('admin/assigned') ? asset('images/App-app.svg') : asset('images/My_Application.svg') }}"
                            class="w-5 h-5" /></span>
                    <span class="title">Assigned Applications</span>
                </a>
            </li>
            <li class="{{ request()->is('admin/program-registration-requests*') ? 'hovered' : '' }}">
                <a href="{{ route('admin.program_registrations.index') }}">
                    <span class="icon"><img
                            src="{{ request()->is('admin/program-registration-requests*') ? asset('images/App-app.svg') : asset('images/My_Application.svg') }}"
                            class="w-5 h-5" /></span>
                    <span class="title">Program Registrations</span>
                </a>
            </li>
            <li class="{{ request()->is('admin/reviewers') ? 'hovered' : '' }}">
                <a href="{{ route('admin.reviewers') }}">
                    <span class="icon"><img
                            src="{{ request()->is('admin/reviewers') ? asset('images/patient-pink.svg') : asset('images/patient.svg') }}"
                            class="w-5 h-5" /></span>
                    <span class="title">Reviewers</span>
                </a>
            </li>
            <li class="{{ request()->is('admin/case-managers*') ? 'hovered' : '' }}">
                <a href="{{ route('admin.case-managers.index') }}">
                    <span class="icon"><img
                            src="{{ request()->is('admin/case-managers*') ? asset('images/patient-pink.svg') : asset('images/patient.svg') }}"
                            class="w-5 h-5" /></span>
                    <span class="title">Case Managers</span>
                </a>
            </li>
            <li class="{{ request()->is('admin/patients') ? 'hovered' : '' }}">
                <a href="{{ route('admin.patients') }}">
                    <span class="icon"><img
                            src="{{ request()->is('admin/patients') ? asset('images/patient-pink.svg') : asset('images/patient.svg') }}"
                            class="w-5 h-5" /></span>
                    <span class="title">Patients</span>
                </a>
            </li>
            <li class="{{ request()->is('admin/sponsors') ? 'hovered' : '' }}">
                <a href="{{ route('admin.sponsors') }}">
                    <span class="icon"><img
                            src="{{ request()->is('admin/sponsors') ? asset('images/Sponsor-pink.svg') : asset('images/Sponsor.svg') }}"
                            class="w-5 h-5" /></span>
                    <span class="title">Sponsors & Events</span>
                </a>
            </li>
            <li class="{{ request()->is('admin/settings') ? 'hovered' : '' }}">
                <a href="{{ route('admin.settings') }}">
                    <span class="icon"><img
                            src="{{ request()->is('admin/settings') ? asset('images/setting-pink.svg') : asset('images/setting.svg') }}" /></span>
                    <span class="title">Settings</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="sign-out">
        {{-- <a href="{{ route('logout') }}"> --}}
        <a href="#">
            <span class="icon"><img src="{{ asset('images/signout.svg') }}" /></span>
            <span class="title">Sign Out</span>
        </a>
    </div>
</div>
