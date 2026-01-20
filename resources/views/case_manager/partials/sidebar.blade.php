<!-- Mobile Sidebar -->
<div class="mobile-sidebar" id="mobileSidebar">
    <!-- Close Button -->
    <button class="close-btn" id="closeBtn">
        <img src="{{ asset('public/images/cross-white.svg') }}" alt="Close">
    </button>

    <!-- Logo -->
    <div class="mobile-logo">
        <img src="{{ asset('public/images/pink_me_logo.png') }}" alt="PINK ME Logo">
    </div>

    <!-- Menu -->
    <ul class="mobile-menu">
        <li class="{{ request()->routeIs('case_manager.dashboard') ? 'active' : '' }}"><a
                href="{{ route('case_manager.dashboard') }}"><img
                    src="{{ request()->routeIs('case_manager.dashboard') ? asset('public/images/Dashboard-svg.svg') : asset('public/images/App-dash.svg') }}"
                    alt="">Dashboard</a></li>
        <li class="{{ request()->routeIs('case_manager.program_registrations.*') ? 'active' : '' }}">
            <a href="{{ route('case_manager.program_registrations.index') }}">
                <img src="{{ request()->routeIs('case_manager.program_registrations.*') ? asset('public/images/document-pink.svg') : asset('public/images/document.svg') }}"
                    alt="">
                Program Registrations
            </a>
        </li>
        <li class="{{ request()->routeIs('case_manager.patientProfiles') ? 'active' : '' }}"><a
                href="{{ route('case_manager.patientProfiles') }}"><img
                    src="{{ request()->routeIs('case_manager.patientProfiles') ? asset('public/images/patient-pink.svg') : asset('public/images/patient.svg') }}"
                    alt="">Patient Profiles</a></li>
        <li class="{{ request()->routeIs('case_manager.patientChats') ? 'active' : '' }}"><a
                href="{{ route('case_manager.patientChats') }}"><img
                    src="{{ request()->routeIs('case_manager.patientChats') ? asset('public/images/chat-svg-pink.svg') : asset('public/images/chat.svg') }}"
                    alt="">Patient Chats</a></li>
        <li class="{{ request()->routeIs('case_manager.setting') ? 'active' : '' }}"><a
                href="{{ route('case_manager.setting') }}"><img
                    src="{{ request()->routeIs('case_manager.setting') ? asset('public/images/setting-pink.svg') : asset('public/images/setting.svg') }}"
                    alt="">Setting</a></li>
    </ul>


</div>
<!-- Left Sidebar with Background Image -->
<div class="box relative">
    <div class="navigation">
        <ul>
            <li>
                <a href="#">
                    <img src="{{ asset('public/images/pink_me_logo.png') }}" alt="" />
                </a>
            </li>
            <li class="{{ request()->routeIs('case_manager.dashboard') ? 'hovered' : '' }}">
                <a href="{{ route('case_manager.dashboard') }}">
                    <span class="icon"><img
                            src="{{ request()->routeIs('case_manager.dashboard') ? asset('public/images/Dashboard-svg.svg') : asset('public/images/App-dash.svg') }}"
                            alt="" class="width: 20px; height: 20px;" /></span>
                    <span class="title">Dashboard</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('case_manager.program_registrations.*') ? 'hovered' : '' }}">
                <a href="{{ route('case_manager.program_registrations.index') }}">
                    <span class="icon"><img
                            src="{{ request()->routeIs('case_manager.program_registrations.*') ? asset('public/images/document-pink.svg') : asset('public/images/document.svg') }}"
                            alt="" /></span>
                    <span class="title">Program Registrations</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('case_manager.patientProfiles') ? 'hovered' : '' }}">
                <a href="{{ route('case_manager.patientProfiles') }}">
                    <span class="icon"><img
                            src="{{ request()->routeIs('case_manager.patientProfiles') ? asset('public/images/patient-pink.svg') : asset('public/images/patient.svg') }}"
                            alt="" /></span>
                    <span class="title">Patient Profiles</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('case_manager.patientChats') ? 'hovered' : '' }}">
                <a href="{{ route('case_manager.patientChats') }}">
                    <span class="icon"><img
                            src="{{ request()->routeIs('case_manager.patientChats') ? asset('public/images/chat-svg-pink.svg') : asset('public/images/chat.svg') }}"
                            alt="" /></span>
                    <span class="title">Patient Chats</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('case_manager.setting') ? 'hovered' : '' }}">
                <a href="{{ route('case_manager.setting') }}">
                    <span class="icon"><img
                            src="{{ request()->routeIs('case_manager.setting') ? asset('public/images/setting-pink.svg') : asset('public/images/setting.svg') }}"
                            alt="" /></span>
                    <span class="title">Setting</span>
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
