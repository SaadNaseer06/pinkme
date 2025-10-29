<!-- Mobile Sidebar -->
<div class="mobile-sidebar" id="mobileSidebar">
    <!-- Close Button -->
    <button class="close-btn" id="closeBtn">
        <img src="{{ asset('public/images/cross-white.svg') }}" alt="Close">
    </button>

    <!-- Logo -->
    <div class="mobile-logo">
        <img src="{{ asset('public/images/logo-white.png') }}" alt="PINK ME Logo">
    </div>

    <!-- Menu -->
    <ul class="mobile-menu">
        <li
            class="{{ request()->routeIs('patient.dashboard') || request()->routeIs('patient.profile') ? 'active' : '' }}">
            <a href="{{ route('patient.dashboard') }}">
                <img src="{{ request()->routeIs('patient.dashboard') || request()->routeIs('patient.profile') ? asset('public/images/Dashboard-svg.svg') : asset('public/images/App-Dash.svg') }}"
                    alt="">
                Dashboard
            </a>
        </li>

        <li
            class="{{ request()->routeIs('patient.myApplication') || request()->routeIs('patient.editApplication') || request()->routeIs('patient.viewApplication') || request()->routeIs('patient.createApplication') ? 'active' : '' }}">
            <a href="{{ route('patient.myApplication') }}">
                <img src="{{ request()->routeIs('patient.myApplication') || request()->routeIs('patient.editApplication') || request()->routeIs('patient.viewApplication') ? asset('public/images/App-app.svg') : asset('public/images/My_Application.svg') }}"
                    alt="">
                My Applications
            </a>
        </li>

        <li class="{{ request()->routeIs('patient.programsAndAids') ? 'active' : '' }}">
            <a href="{{ route('patient.programsAndAids') }}">
                <img src="{{ request()->routeIs('patient.programsAndAids') ? asset('public/images/program-svg.svg') : asset('public/images/program.svg') }}"
                    alt="">
                Programs & Aids
            </a>
        </li>

        <!--<li class="{{ request()->routeIs('patient.patientChats') ? 'active' : '' }}">-->
        <!--    <a href="{{ route('patient.patientChats') }}">-->
        <!--        <img src="{{ request()->routeIs('patient.patientChats') ? asset('public/images/chat-svg-pink.svg') : asset('public/images/chat.svg') }}"-->
        <!--            alt="">-->
        <!--        Chat-->
        <!--    </a>-->
        <!--</li>-->

        <li class="{{ request()->routeIs('patient.faq') ? 'active' : '' }}">
            <a href="{{ route('patient.faq') }}">
                <img src="{{ request()->routeIs('patient.faq') ? asset('public/images/Faq-pink.svg') : asset('public/images/FAQ.svg') }}"
                    alt="">
                FAQ
            </a>
        </li>

        {{-- <li class="{{ request()->routeIs('patient.invoices') || request()->routeIs('invoices.show') ? 'active' : '' }}">
            <a href="{{ route('patient.invoices') }}">
                <img src="{{ request()->routeIs('patient.invoices') || request()->routeIs('invoices.show') ? asset('public/images/invoice.svg') : asset('public/images/invoice-pink.svg') }}"
                    alt="">
                Invoices
            </a>
        </li> --}}

        <li class="{{ request()->routeIs('patient.setting') ? 'active' : '' }}">
            <a href="{{ route('patient.setting') }}">
                <img src="{{ request()->routeIs('patient.setting') ? asset('public/images/setting-pink.svg') : asset('public/images/setting.svg') }}"
                    alt="">
                Setting
            </a>
        </li>
    </ul>
</div>

<!-- Left Sidebar with Background Image -->
<div class="box relative">
    <div class="navigation">
        <ul>
            <li>
                <a href="#">
                    <img src="{{ asset('public/images/logo-white.png') }}" alt="" />
                </a>
            </li>
            <li
                class="{{ request()->routeIs('patient.dashboard') || request()->routeIs('patient.profile') ? 'hovered' : '' }}">
                <a href="{{ route('patient.dashboard') }}">
                    <span class="icon">
                        <img src="{{ request()->routeIs('patient.dashboard') || request()->routeIs('patient.profile') ? asset('public/images/Dashboard-svg.svg') : asset('public/images/App-Dash.svg') }}"
                            alt="" class="width: 20px; height: 20px;" />
                    </span>
                    <span class="title">Dashboard</span>
                </a>
            </li>
            <li
                class="{{ request()->routeIs('patient.myApplication') || request()->routeIs('patient.editApplication') || request()->routeIs('patient.viewApplication') ? 'hovered' : '' }}">
                <a href="{{ route('patient.myApplication') }}">
                    <span class="icon">
                        <img src="{{ request()->routeIs('patient.myApplication') || request()->routeIs('patient.editApplication') || request()->routeIs('patient.viewApplication') ? asset('public/images/App-app.svg') : asset('public/images/My_Application.svg') }}"
                            alt="" />
                    </span>
                    <span class="title">My Application</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('patient.programsAndAids') ? 'hovered' : '' }}">
                <a href="{{ route('patient.programsAndAids') }}">
                    <span class="icon"><img
                            src="{{ request()->routeIs('patient.programsAndAids') ? asset('public/images/program-svg.svg') : asset('public/images/program.svg') }}"
                            alt="" /></span>
                    <span class="title">Programs & Aids</span>
                </a>
            </li>
            <!--<li class="{{ request()->routeIs('patient.patientChats') ? 'hovered' : '' }}">-->
            <!--    <a href="{{ route('patient.patientChats') }}">-->
            <!--        <span class="icon"><img-->
            <!--                src="{{ request()->routeIs('patient.patientChats') ? asset('public/images/chat-svg-pink.svg') : asset('public/images/chat.svg') }}"-->
            <!--                alt="" /></span>-->
            <!--        <span class="title">Chat</span>-->
            <!--    </a>-->
            <!--</li>-->
            <li class="{{ request()->routeIs('patient.faq') ? 'hovered' : '' }}">
                <a href="{{ route('patient.faq') }}">
                    <span class="icon"><img
                            src="{{ request()->routeIs('patient.faq') ? asset('public/images/Faq-pink.svg') : asset('public/images/FAQ.svg') }}"
                            alt="" /></span>
                    <span class="title">FAQ</span>
                </a>
            </li>
            {{-- <li class="{{ request()->routeIs('patient.invoices') || request()->routeIs('invoices.show') ? 'hovered' : '' }}">
                <a href="{{ route('patient.invoices') }}">
                    <span class="icon"><img
                            src="{{ request()->routeIs('patient.invoices') || request()->routeIs('invoices.show') ? asset('public/images/invoice.svg') : asset('public/images/invoice-pink.svg') }}"
                            alt="" /></span>
                    <span class="title">Invoices</span>
                </a>
            </li> --}}
            <li class="{{ request()->routeIs('patient.setting') ? 'hovered' : '' }}">
                <a href="{{ route('patient.setting') }}">
                    <span class="icon"><img
                            src="{{ request()->routeIs('patient.setting') ? asset('public/images/setting-pink.svg') : asset('public/images/setting.svg') }}"
                            alt="" /></span>
                    <span class="title">Setting</span>
                </a>
            </li>
        </ul>
    </div>
    <!-- Separate Sign Out button outside navigation -->
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
