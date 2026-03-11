{{-- Ensures admin sidebar color (#9E2469) and active-item styling match across all user portals --}}
<style>
    :root {
        --pink: #9E2469;
        --white: #fff;
        /* Prevent sidebar-active-bg from appearing anywhere on any portal */
        --sidebar-active-bg: transparent;
    }
    /* Remove blue from sidebar (browser link defaults, focus outline) */
    .box a,
    .navigation a,
    .mobile-sidebar a,
    .sign-out a {
        outline: none !important;
        box-shadow: none !important;
    }
    .navigation ul li a,
    .navigation ul li a:link,
    .navigation ul li a:visited {
        color: #fff !important;
    }
    .navigation ul li.hovered a,
    .navigation ul li.hovered a:link,
    .navigation ul li.hovered a:visited,
    .navigation ul li.hovered a:hover,
    .navigation ul li.hovered a:focus {
        color: #9E2469 !important;
    }
    .sign-out a,
    .sign-out a:link,
    .sign-out a:visited,
    .sign-out a:hover,
    .sign-out a:focus {
        color: #fff !important;
    }
    /* Sidebar background: same magenta on admin, finance, case manager, patient, sponsor */
    .box,
    .navigation,
    .mobile-sidebar,
    .sign-out {
        background: #9E2469 !important;
        border: none !important;
        border-right: none !important;
    }
    /* Remove blue/dark line between sidebar and content (Tailwind default border) */
    .box + div {
        border: none !important;
        border-left: none !important;
    }
    /* Mobile sidebar: active menu item - white background, magenta text */
    .mobile-menu li.active a,
    .mobile-menu li.active a:link,
    .mobile-menu li.active a:visited,
    .mobile-menu li.active a:hover,
    .mobile-menu li.active a:focus {
        background-color: #fff !important;
        color: #9E2469 !important;
    }
    .mobile-menu li a:hover {
        background-color: #fff !important;
        color: #9E2469 !important;
    }
    /* Active/hovered menu item: light pink background, magenta text, rounded tab (matches admin) */
    .navigation ul li.hovered {
        background: #fff8fc !important;
    }
    .navigation ul li.hovered a {
        color: #9E2469 !important;
        position: relative;
    }
    .navigation ul li.hovered a::before {
        content: "";
        position: absolute;
        right: 0;
        top: -50px;
        width: 50px;
        height: 50px;
        background: transparent;
        border-radius: 50%;
        box-shadow: 35px 35px 0 10px #fff8fc;
        pointer-events: none;
    }
    .navigation ul li.hovered a::after {
        content: "";
        position: absolute;
        right: 0;
        bottom: -50px;
        width: 50px;
        height: 50px;
        background: transparent;
        border-radius: 50%;
        box-shadow: 35px -35px 0 10px #fff8fc;
        pointer-events: none;
    }
</style>
