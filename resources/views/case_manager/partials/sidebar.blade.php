<!-- Mobile Sidebar -->
<div class="mobile-sidebar" id="mobileSidebar">
    <!-- Close Button -->
    <button class="close-btn" id="closeBtn">
        <img src="{{ asset('/images/cross-white.svg') }}" alt="Close">
    </button>

    <!-- Logo -->
    <div class="mobile-logo">
        <img src="{{ asset('/images/logo-white.png') }}" alt="PINK ME Logo">
    </div>

    <!-- Menu -->
    <ul class="mobile-menu">
        <li class="{{ request()->routeIs('case_manager.dashboard') ? 'active' : '' }}"><a
                href="{{ route('case_manager.dashboard') }}"><img
                    src="{{ request()->routeIs('case_manager.dashboard') ? asset('images/Dashboard-svg.svg') : asset('images/App-dash.svg') }}"
                    alt="">Dashboard</a></li>
        <li
            class="{{ request()->routeIs('case_manager.myApplication', 'case_manager.viewAssignedApplication') ? 'active' : '' }}">
            <a href="{{ route('case_manager.myApplication') }}">
                <img src="{{ request()->routeIs('case_manager.myApplication', 'case_manager.viewAssignedApplication') ? asset('images/App-app.svg') : asset('images/My_Application.svg') }}"
                    alt="">
                My Applications
            </a>
        </li>
        <li class="{{ request()->routeIs('case_manager.patientProfiles') ? 'active' : '' }}"><a
                href="{{ route('case_manager.patientProfiles') }}"><img
                    src="{{ request()->routeIs('case_manager.patientProfiles') ? asset('images/patient-pink.svg') : asset('images/patient.svg') }}"
                    alt="">Patient Profiles</a></li>
        <li class="{{ request()->routeIs('case_manager.patientChats') ? 'active' : '' }}"><a
                href="{{ route('case_manager.patientChats') }}"><img
                    src="{{ request()->routeIs('case_manager.patientChats') ? asset('images/chat-svg-pink.svg') : asset('images/chat.svg') }}"
                    alt="">Patient Chats</a></li>
        <li class="{{ request()->routeIs('case_manager.setting') ? 'active' : '' }}"><a
                href="{{ route('case_manager.setting') }}"><img
                    src="{{ request()->routeIs('case_manager.setting') ? asset('images/setting-pink.svg') : asset('images/setting.svg') }}"
                    alt="">Setting</a></li>
    </ul>


</div>
<!-- Left Sidebar with Background Image -->
<div class="box relative">
    <div class="navigation">
        <ul>
            <li>
                <a href="#">
                    <img src="{{ asset('/images/logo-white.png') }}" alt="" />
                </a>
            </li>
            <li class="{{ request()->routeIs('case_manager.dashboard') ? 'hovered' : '' }}">
                <a href="{{ route('case_manager.dashboard') }}">
                    <span class="icon"><img
                            src="{{ request()->routeIs('case_manager.dashboard') ? asset('images/Dashboard-svg.svg') : asset('images/App-dash.svg') }}"
                            alt="" class="width: 20px; height: 20px;" /></span>
                    <span class="title">Dashboard</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('case_manager.myApplication') ? 'hovered' : '' }}">
                <a href="{{ route('case_manager.myApplication') }}">
                    <span class="icon"><img
                            src="{{ request()->routeIs('case_manager.myApplication') ? asset('images/App-app.svg') : asset('images/My_Application.svg') }}"
                            alt="" /></span>
                    <span class="title">My Application</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('case_manager.patientProfiles') ? 'hovered' : '' }}">
                <a href="{{ route('case_manager.patientProfiles') }}">
                    <span class="icon"><img
                            src="{{ request()->routeIs('case_manager.patientProfiles') ? asset('images/patient-pink.svg') : asset('images/patient.svg') }}"
                            alt="" /></span>
                    <span class="title">Patient Profiles</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('case_manager.patientChats') ? 'hovered' : '' }}">
                <a href="{{ route('case_manager.patientChats') }}">
                    <span class="icon"><img
                            src="{{ request()->routeIs('case_manager.patientChats') ? asset('images/chat-svg-pink.svg') : asset('images/chat.svg') }}"
                            alt="" /></span>
                    <span class="title">Patient Chats</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('case_manager.setting') ? 'hovered' : '' }}">
                <a href="{{ route('case_manager.setting') }}">
                    <span class="icon"><img
                            src="{{ request()->routeIs('case_manager.setting') ? asset('images/setting-pink.svg') : asset('images/setting.svg') }}"
                            alt="" /></span>
                    <span class="title">Setting</span>
                </a>
            </li>

        </ul>
    </div>
    <div class="sign-out">
        <a href="registration.html">
            <span class="icon"><img src="{{ asset('/images/signout.svg') }}" alt="" /></span>
            <span class="title">Sign Out</span>
        </a>
    </div>
</div>
