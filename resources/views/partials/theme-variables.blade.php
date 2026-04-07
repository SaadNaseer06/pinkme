{{-- Shared portal chrome: off-white sidebar, dark nav text, pink active state (all roles) --}}
<style>
    :root {
        --pink: #9E2469;
        --white: #fff;
        --sidebar-bg: #FAFAFA;
        --sidebar-border: #E5D6E0;
        --sidebar-text: #213430;
        --sidebar-hover-bg: #F3E8EF;
        --sidebar-active-bg: transparent;
    }
    /* Remove blue from sidebar links */
    .box a,
    .navigation a,
    .mobile-sidebar a,
    .sign-out a {
        outline: none !important;
        box-shadow: none !important;
    }
    /* Desktop nav: default dark text on light sidebar */
    .navigation ul li a,
    .navigation ul li a:link,
    .navigation ul li a:visited {
        color: #213430 !important;
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
        color: #9E2469 !important;
    }
    /* Sidebar shell: off-white (all portals) */
    .box,
    .navigation,
    .mobile-sidebar,
    .sign-out {
        background: #FAFAFA !important;
        border: none !important;
        border-right: 1px solid #E5D6E0 !important;
    }
    .sign-out {
        border-top: 1px solid #E5D6E0 !important;
    }
    .box + div {
        border: none !important;
        border-left: none !important;
    }
    /* Mobile drawer: default links */
    .mobile-menu li a,
    .mobile-menu li a:link,
    .mobile-menu li a:visited {
        color: #213430 !important;
    }
    .mobile-menu li.active a,
    .mobile-menu li.active a:link,
    .mobile-menu li.active a:visited,
    .mobile-menu li.active a:hover,
    .mobile-menu li.active a:focus {
        background-color: #F3E8EF !important;
        color: #9E2469 !important;
    }
    .mobile-menu li a:hover {
        background-color: #F3E8EF !important;
        color: #9E2469 !important;
    }
    .mobile-sidebar .close-btn img {
        filter: brightness(0);
        opacity: 0.75;
    }
    /*
     * Sidebar SVGs were authored for the old dark bar; on light sidebar inactive icons read as white/washed out.
     * Tint inactive icons to body text #213430; remove filter on active row (pink icon assets).
     * Brand PNG is always a direct child of the link (a > img), never inside .icon — never tint those.
     */
    .navigation ul li a > img {
        filter: none !important;
        opacity: 1 !important;
    }
    .navigation ul li:not(.hovered) a .icon img {
        filter: brightness(0) saturate(100%) invert(13%) sepia(10%) saturate(618%) hue-rotate(131deg) brightness(95%) contrast(92%);
        opacity: 1;
    }
    .navigation ul li.hovered a .icon img {
        filter: none !important;
        opacity: 1;
    }
    .mobile-sidebar .mobile-logo img {
        filter: none !important;
        opacity: 1 !important;
    }
    .mobile-sidebar .mobile-menu li:not(.active) a img {
        filter: brightness(0) saturate(100%) invert(13%) sepia(10%) saturate(618%) hue-rotate(131deg) brightness(95%) contrast(92%);
        opacity: 1;
    }
    .mobile-sidebar .mobile-menu li.active a img {
        filter: none !important;
        opacity: 1;
    }
    .mobile-sidebar .mobile-menu li:not(.active) a:hover img {
        filter: brightness(0) saturate(100%) invert(27%) sepia(51%) saturate(2878%) hue-rotate(314deg) brightness(87%) contrast(97%);
        opacity: 1;
    }
    /* Sign out: icon matches pink link */
    .sign-out a .icon img {
        filter: brightness(0) saturate(100%) invert(27%) sepia(51%) saturate(2878%) hue-rotate(314deg) brightness(87%) contrast(97%);
        opacity: 1;
    }
    /* Active / hover row: light rose tab */
    .navigation ul li.hovered {
        background: #F3E8EF !important;
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
        box-shadow: 35px 35px 0 10px #F3E8EF;
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
        box-shadow: 35px -35px 0 10px #F3E8EF;
        pointer-events: none;
    }
</style>
