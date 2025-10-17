 <!-- Mobile Sidebar -->
 <div class="mobile-sidebar" id="mobileSidebar">
     <!-- Close Button -->
     <button class="close-btn" id="closeBtn">
        <img src="{{ asset('images/cross-white.svg') }}" alt="Close">
     </button>

     <!-- Logo -->
     <div class="mobile-logo">
        <img src="{{ asset('images/logo-white.png') }}" alt="PINK ME Logo">
     </div>

     <!-- Menu -->
     <ul class="mobile-menu">
        <!-- apply image conditionally -->
         <li class="{{ request()->routeIs('sponsor.dashboard') ? 'active' : '' }}"><a href="{{ route('sponsor.dashboard') }}"><img src="{{ request()->routeIs('sponsor.dashboard') ? asset('images/Dashboard-svg.svg') : asset('images/App-Dash.svg') }}"
                     alt="">Dashboard</a></li>
         <li class="{{ request()->routeIs('sponsor.events') ? 'active' : '' }}"><a href="{{ route('sponsor.events') }}"><img src="{{ request()->routeIs('sponsor.events') ? asset('images/Sponsor-pink.svg') : asset('images/sponsor.svg') }}" alt="">Events</a></li>
         <li class="{{ request()->routeIs('sponsor.sponsorships') ? 'active' : '' }}"><a href="{{ route('sponsor.sponsorships') }}"><img src="{{ request()->routeIs('sponsor.sponsorships') ? asset('images/Sponsor-pink.svg') : asset('images/Sponsor.svg') }}" alt="">Sponsorships</a></li>
         <li class="{{ request()->routeIs('sponsor.becomeASponsor') ? 'active' : '' }}"><a href="{{ route('sponsor.becomeASponsor') }}"><img src="{{ request()->routeIs('sponsor.becomeASponsor') ? asset('images/affiliate-pink.svg') : asset('images/affiliate.svg') }}" alt=""> Become A Sponsor</a>
         </li>
         <li class="{{ request()->routeIs('sponsor.reviews') ? 'active' : '' }}"><a href="{{ route('sponsor.reviews') }}"><img src="{{ request()->routeIs('sponsor.reviews') ? asset('images/review-pink.svg') : asset('images/review.svg') }}" alt="">Reviews</a></li>
         <li class="{{ request()->routeIs('sponsor.setting') ? 'active' : '' }}"><a href="{{ route('sponsor.setting') }}"><img src="{{ request()->routeIs('sponsor.setting') ? asset('images/setting-pink.svg') : asset('images/setting.svg') }}" alt="">Setting</a></li>
     </ul>


 </div>
 <!-- Left Sidebar with Background Image -->
 <div class="box relative">
     <div class="navigation">
         <ul>
             <li>
                 <a href="{{ route('sponsor.dashboard') }}">
                    <img src="{{ asset('images/logo-white.png') }}" alt="" />
                 </a>
             </li>
             <li class="{{ request()->routeIs('sponsor.dashboard') ? 'hovered' : '' }}">
                 <a href="{{ route('sponsor.dashboard') }}">
                     <span class="icon"><img src="{{ request()->routeIs('sponsor.dashboard') ? asset('images/Dashboard-svg.svg') : asset('images/App-Dash.svg') }}" alt="" /></span>
                     <span class="title">Dashboard</span>
                 </a>
             </li>
             <li class="{{ request()->routeIs('sponsor.events') ? 'hovered' : '' }}">
                 <a href="{{ route('sponsor.events') }}">
                     <span class="icon"><img src="{{ request()->routeIs('sponsor.events') ? asset('images/Sponsor-pink.svg') : asset('images/sponsor.svg') }}" alt="" /></span>
                     <span class="title">Events</span>
                 </a>
             </li>
             <li class="{{ request()->routeIs('sponsor.sponsorships') ? 'hovered' : '' }}">
                 <a href="{{ route('sponsor.sponsorships') }}">
                     <span class="icon"><img src="{{ request()->routeIs('sponsor.sponsorships') ? asset('images/Sponsor-pink.svg') : asset('images/Sponsor.svg') }}" alt="" /></span>
                     <span class="title">Sponsorships</span>
                 </a>
             </li>
             <li class="{{ request()->routeIs('sponsor.becomeASponsor') ? 'hovered' : '' }}">
                 <a href="{{ route('sponsor.becomeASponsor') }}">
                     <span class="icon"><img src="{{ request()->routeIs('sponsor.becomeASponsor') ? asset('images/affiliate-pink.svg') : asset('images/affiliate.svg') }}" alt="" /></span>
                     <span class="title">Become A Sponsor</span>
                 </a>
             </li>
             <li class="{{ request()->routeIs('sponsor.reviews') ? 'hovered' : '' }}">
                 <a href="{{ route('sponsor.reviews') }}">
                     <span class="icon"><img src="{{ request()->routeIs('sponsor.reviews') ? asset('images/review-pink.svg') : asset('images/review.svg') }}" alt="" /></span>
                     <span class="title">Reviews</span>
                 </a>
             </li>
             <li class="{{ request()->routeIs('sponsor.payment') ? 'hovered' : '' }}">
                 <a href="{{ route('sponsor.payment') }}">
                     <span class="icon"><img src="{{ request()->routeIs('sponsor.payment') ? asset('images/payment-pink.svg') : asset('images/payment.svg') }}" alt="" /></span>
                     <span class="title">Payment</span>
                 </a>
             </li>
             <li class="{{ request()->routeIs('sponsor.setting') ? 'hovered' : '' }}">
                 <a href="{{ route('sponsor.setting') }}">
                     <span class="icon"><img src="{{ request()->routeIs('sponsor.setting') ? asset('images/setting-pink.svg') : asset('images/setting.svg') }}" alt="" /></span>
                     <span class="title">Setting</span>
                 </a>
             </li>

         </ul>
     </div>
     <div class="sign-out">
         <a href="registration.html">
            <span class="icon"><img src="{{ asset('images/signout.svg') }}" alt="" /></span>
             <span class="title">Sign Out</span>
         </a>
     </div>
 </div>
