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
        <!-- apply image conditionally -->
         <li class="{{ request()->routeIs('sponsor.dashboard') ? 'active' : '' }}"><a href="{{ route('sponsor.dashboard') }}"><img src="{{ request()->routeIs('sponsor.dashboard') ? asset('public/images/Dashboard-svg.svg') : asset('public/images/App-Dash.svg') }}"
                     alt="">Dashboard</a></li>
         @php $isEventsActive = request()->routeIs('sponsor.events') && request()->query('type') !== 'full'; @endphp
         <li class="{{ $isEventsActive ? 'active' : '' }}"><a href="{{ route('sponsor.events') }}"><img src="{{ $isEventsActive ? asset('public/images/Sponsor-pink.svg') : asset('public/images/sponsor.svg') }}" alt="">Events</a></li>
         <li class="{{ request()->routeIs('sponsor.webinars') ? 'active' : '' }}"><a href="{{ route('sponsor.webinars') }}"><img src="{{ request()->routeIs('sponsor.webinars') ? asset('public/images/program-svg.svg') : asset('public/images/program.svg') }}" alt="">Webinars</a></li>
         <li class="{{ request()->routeIs('sponsor.sponsorships') ? 'active' : '' }}"><a href="{{ route('sponsor.sponsorships') }}"><img src="{{ request()->routeIs('sponsor.sponsorships') ? asset('public/images/Sponsor-pink.svg') : asset('public/images/Sponsor.svg') }}" alt="">Sponsorships</a></li>
         @php $isBecomeASponsorActive = request()->routeIs('sponsor.becomeASponsor') || (request()->routeIs('sponsor.events') && request()->query('type') === 'full'); @endphp
         <li class="{{ $isBecomeASponsorActive ? 'active' : '' }}"><a href="{{ route('sponsor.becomeASponsor') }}"><img src="{{ $isBecomeASponsorActive ? asset('public/images/affiliate-pink.svg') : asset('public/images/affiliate.svg') }}" alt=""> Become A Sponsor</a>
         </li>
         {{-- Reviews link hidden per client request --}}
         <li class="{{ request()->routeIs('sponsor.setting') ? 'active' : '' }}"><a href="{{ route('sponsor.setting') }}"><img src="{{ request()->routeIs('sponsor.setting') ? asset('public/images/setting-pink.svg') : asset('public/images/setting.svg') }}" alt="">Setting</a></li>
     </ul>


 </div>
 <!-- Left Sidebar with Background Image -->
 <div class="box relative">
     <div class="navigation">
         <ul>
             <li>
                 <a href="{{ route('sponsor.dashboard') }}">
                    <img src="{{ asset('public/images/pink_me_logo.png') }}" alt="" />
                 </a>
             </li>
             <li class="{{ request()->routeIs('sponsor.dashboard') ? 'hovered' : '' }}">
                 <a href="{{ route('sponsor.dashboard') }}">
                     <span class="icon"><img src="{{ request()->routeIs('sponsor.dashboard') ? asset('public/images/Dashboard-svg.svg') : asset('public/images/App-Dash.svg') }}" alt="" /></span>
                     <span class="title">Dashboard</span>
                 </a>
             </li>
            @php $isEventsActive = request()->routeIs('sponsor.events') && request()->query('type') !== 'full'; @endphp
            <li class="{{ $isEventsActive ? 'hovered' : '' }}">
                <a href="{{ route('sponsor.events') }}">
                    <span class="icon"><img src="{{ $isEventsActive ? asset('public/images/Sponsor-pink.svg') : asset('public/images/sponsor.svg') }}" alt="" /></span>
                    <span class="title">Events</span>
                </a>
            </li>
             <li class="{{ request()->routeIs('sponsor.webinars') ? 'hovered' : '' }}">
                 <a href="{{ route('sponsor.webinars') }}">
                     <span class="icon"><img src="{{ request()->routeIs('sponsor.webinars') ? asset('public/images/program-svg.svg') : asset('public/images/program.svg') }}" alt="" /></span>
                     <span class="title">Webinars</span>
                 </a>
             </li>
             <li class="{{ request()->routeIs('sponsor.sponsorships') ? 'hovered' : '' }}">
                 <a href="{{ route('sponsor.sponsorships') }}">
                     <span class="icon"><img src="{{ request()->routeIs('sponsor.sponsorships') ? asset('public/images/Sponsor-pink.svg') : asset('public/images/Sponsor.svg') }}" alt="" /></span>
                     <span class="title">Sponsorships</span>
                 </a>
             </li>
            @php $isBecomeASponsorActive = request()->routeIs('sponsor.becomeASponsor') || (request()->routeIs('sponsor.events') && request()->query('type') === 'full'); @endphp
            <li class="{{ $isBecomeASponsorActive ? 'hovered' : '' }}">
                <a href="{{ route('sponsor.becomeASponsor') }}">
                    <span class="icon"><img src="{{ $isBecomeASponsorActive ? asset('public/images/affiliate-pink.svg') : asset('public/images/affiliate.svg') }}" alt="" /></span>
                    <span class="title">Become A Sponsor</span>
                </a>
            </li>
             {{-- Reviews link hidden per client request --}}
             <li class="{{ request()->routeIs('sponsor.payment') ? 'hovered' : '' }}">
                 <a href="{{ route('sponsor.payment') }}">
                     <span class="icon"><img src="{{ request()->routeIs('sponsor.payment') ? asset('public/images/payment-pink.svg') : asset('public/images/payment.svg') }}" alt="" /></span>
                     <span class="title">Payment</span>
                 </a>
             </li>
             <li class="{{ request()->routeIs('sponsor.setting') ? 'hovered' : '' }}">
                 <a href="{{ route('sponsor.setting') }}">
                     <span class="icon"><img src="{{ request()->routeIs('sponsor.setting') ? asset('public/images/setting-pink.svg') : asset('public/images/setting.svg') }}" alt="" /></span>
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
