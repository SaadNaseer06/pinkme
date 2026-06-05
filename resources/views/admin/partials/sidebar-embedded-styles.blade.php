{{-- Sidebar + icon alignment: inlined so it works when Vite public/build is stale on the server.
     Scoped with .admin-app on <body>. Keep in sync with resources/css/custom.css (.navigation / .box / .sign-out). --}}
<style id="admin-sidebar-inline-css">
    .admin-app .box {
        position: relative;
        width: 300px;
        min-width: 300px;
        flex-shrink: 0;
        min-height: 100vh;
        background: #fafafa !important;
        border-right: 1px solid #e5d6e0 !important;
    }

    .admin-app .navigation {
        position: fixed;
        width: 300px;
        height: 100%;
        background: #fafafa !important;
        transition: 0.5s;
        overflow: hidden;
        display: flex !important;
        flex-direction: column !important;
    }

    .admin-app .navigation ul {
        position: relative;
        width: 100%;
        flex-grow: 1;
        padding: 0;
        margin: 0;
    }

    .admin-app .navigation ul li {
        position: relative;
        z-index: 0;
        width: 100%;
        list-style: none;
        border-top-left-radius: 30px;
        border-bottom-left-radius: 30px;
    }

    .admin-app .navigation ul li.hovered {
        z-index: 2;
        background: #f3e8ef !important;
    }

    /* Theme + custom.css use huge box-shadow “tabs” that spill into adjacent rows — hide on admin */
    .admin-app .navigation ul li.hovered a::before,
    .admin-app .navigation ul li.hovered a::after {
        display: none !important;
        content: none !important;
        box-shadow: none !important;
    }

    .admin-app .navigation ul li:nth-child(1) {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 40px 0;
        pointer-events: none;
        margin-bottom: 80px;
        margin-left: 20px;
    }

    .admin-app .navigation ul li:nth-child(1) a img {
        width: 224px;
    }

    /* Flex row: icon vertically centered with single- or multi-line .title */
    .admin-app .navigation ul li a {
        position: relative;
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        align-items: center !important;
        width: 100%;
        max-width: 100%;
        min-height: 52px;
        padding: 10px 12px 10px 16px !important;
        box-sizing: border-box !important;
        text-decoration: none !important;
        color: #213430 !important;
    }

    .admin-app .navigation ul li.hovered a {
        color: #9e2469 !important;
    }

    .admin-app .navigation ul li a .icon {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
        width: 44px !important;
        min-width: 44px !important;
        margin: 0 !important;
        padding: 0 10px 0 0 !important;
        box-sizing: border-box !important;
    }

    .admin-app .navigation ul li a .icon img {
        width: 20px !important;
        height: 20px !important;
        margin: 0 !important;
        display: block !important;
    }

    .admin-app .navigation ul li a .title {
        display: block !important;
        flex: 1 1 auto !important;
        min-width: 0 !important;
        padding: 0 6px 0 0 !important;
        font-size: 16px !important;
        line-height: 1.35 !important;
        text-align: start !important;
        white-space: normal !important;
    }

    .admin-app .navigation ul li a .title .sidebar-nav-line {
        display: block;
    }

    .admin-app .mobile-sidebar .sidebar-nav-line {
        display: block;
    }

    .admin-app .sign-out {
        position: fixed;
        bottom: 20px;
        left: 0;
        width: 300px;
        background: #fafafa !important;
        padding: 10px 20px;
        text-align: center;
        box-sizing: border-box;
        border-top-left-radius: 30px;
        border-top: 1px solid #e5d6e0 !important;
    }

    .admin-app .sign-out a {
        display: flex !important;
        align-items: center !important;
        justify-content: flex-start !important;
        margin-left: 42px;
        text-decoration: none !important;
        color: #9e2469 !important;
    }

    .admin-app .sign-out a .icon {
        margin-right: 10px;
        flex-shrink: 0;
    }

    .admin-app .sign-out a .title {
        font-weight: 400;
    }

    .admin-app .mobile-sidebar .mobile-menu li a {
        display: flex !important;
        align-items: center !important;
    }

    .admin-app .mobile-sidebar .mobile-menu li a .title {
        flex: 1 1 auto !important;
        min-width: 0 !important;
        text-align: left !important;
        font-size: inherit !important;
    }

    @@media (min-width: 1024px) and (max-width: 1668px) {
        .admin-app .box {
            width: 250px !important;
            min-width: 250px !important;
        }

        .admin-app .navigation {
            width: 250px !important;
        }

        .admin-app .navigation ul li:nth-child(1) a img {
            width: 180px !important;
        }

        .admin-app .navigation ul li:nth-child(1) {
            margin-left: 18px !important;
            padding: 20px 0 !important;
            margin-bottom: 30px !important;
        }

        .admin-app .navigation ul li a {
            padding-left: 10px !important;
            padding-right: 8px !important;
        }

        .admin-app .sign-out {
            width: 250px !important;
            bottom: 2px !important;
        }

        .admin-app .sign-out a {
            margin-left: 32px !important;
            font-size: 14px !important;
        }

        .admin-app .navigation ul li a .icon {
            width: 40px !important;
            min-width: 40px !important;
            padding: 0 8px 0 0 !important;
        }

        .admin-app .navigation ul li a .title {
            font-size: 14px !important;
        }
    }
</style>
