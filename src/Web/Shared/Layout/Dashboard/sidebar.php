<?php

declare(strict_types=1);

use App\Web\Shared\Layout\Main\MainAsset;

/** @var \Yiisoft\Assets\AssetManager $assetManager */
/** @var \Yiisoft\View\WebView $this */
/** @var string $content */

$assetManager->register(MainAsset::class);

$this->addCssFiles($assetManager->getCssFiles());
$this->addCssStrings($assetManager->getCssStrings());

$this->beginPage();

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <?php $this->head(); ?>

</head>


<body class="m-0 min-h-screen overflow-x-hidden bg-gray-100">


<?php $this->beginBody(); ?>


<!-- =============================================================
     APPLICATION LAYOUT
     ============================================================= -->

<div class="flex min-h-screen w-full">


    <!-- =========================================================
         SIDEBAR OVERLAY
         
         Mobile/tablet only.
         ========================================================= -->

    <div
        id="sidebar-overlay"

        class="
            fixed
            inset-0
            z-30

            bg-black/50

            opacity-0
            pointer-events-none

            transition-opacity
            duration-300

            lg:hidden
        "

        aria-hidden="true"
    ></div>


    <!-- =========================================================
         SIDEBAR
         
         Mobile/tablet:
             Fixed
             Hidden off-screen by default
             Slides in when opened

         Desktop (lg and above):
             Always visible
             256px wide
             Full viewport height
         ========================================================= -->

    <aside
        id="sidebar"

        class="
            fixed
            inset-y-0
            left-0
            z-40

            flex
            w-64
            shrink-0
            flex-col

            bg-gray-900
            text-white

            shadow-xl

            -translate-x-full
            transition-transform
            duration-300
            ease-in-out

            lg:sticky
            lg:top-0
            lg:h-screen
            lg:translate-x-0
            lg:shadow-none
        "
    >


        <!-- =====================================================
             APPLICATION TITLE
             ===================================================== -->

        <div
            class="
                flex
                h-20
                shrink-0
                items-center
                justify-between
                px-6
            "
        >

            <a
                href="/home"
                class="text-xl font-bold"
            >
                My Application
            </a>


            <!-- Mobile / tablet close button -->

            <button
                id="close-sidebar"
                type="button"
                aria-label="Close sidebar"

                class="
                    flex
                    h-10
                    w-10
                    items-center
                    justify-center

                    rounded-lg

                    text-white

                    transition
                    hover:bg-gray-800
                    hover:text-gray-300

                    lg:hidden
                "
            >

                <svg
                    class="h-6 w-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"
                    ></path>

                </svg>

            </button>

        </div>


        <!-- =====================================================
             NAVIGATION
             ===================================================== -->

        <nav
            class="
                flex-1
                overflow-y-auto
                px-4
                py-2
            "
        >

            <!-- Dashboard -->

            <a
                href="/"
                class="
                    block
                    rounded-lg
                    px-4
                    py-3

                    text-sm
                    font-medium

                    transition-colors

                    hover:bg-gray-800
                "
            >
                Dashboard
            </a>


            <!-- Users -->

            <a
                href="/user"
                class="
                    mt-1
                    block
                    rounded-lg
                    px-4
                    py-3

                    text-sm
                    font-medium

                    transition-colors

                    hover:bg-gray-800
                "
            >
                Users
            </a>

        </nav>


        <!-- =====================================================
             LOGOUT
             ===================================================== -->

        <div
            class="
                shrink-0
                px-4
                pb-4
                pt-2
            "
        >

            <a
                href="/logout"

                class="
                    block
                    rounded-lg
                    px-4
                    py-3

                    text-sm
                    font-semibold
                    text-red-500

                    transition-colors

                    hover:bg-gray-800
                    hover:text-red-400
                "
            >
                Logout
            </a>

        </div>


    </aside>


    <!-- =========================================================
         MAIN CONTENT
         ========================================================= -->

    <main
        class="
            min-h-screen
            min-w-0
            flex-1
        "
    >


        <!-- =====================================================
             MOBILE / TABLET HEADER
             
             Hidden on desktop.
             ===================================================== -->

        <header
            class="
                flex
                w-full
                items-center
                gap-4

                bg-gray-900
                px-4
                py-3
                text-white

                lg:hidden
            "
        >

            <!-- Open sidebar -->

            <button
                id="open-sidebar"
                type="button"
                aria-label="Open sidebar"
                aria-expanded="false"

                class="
                    flex
                    h-10
                    w-10
                    shrink-0
                    items-center
                    justify-center

                    rounded-lg

                    text-white

                    transition

                    hover:bg-gray-800
                "
            >

                <svg
                    class="h-6 w-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"
                    ></path>

                </svg>

            </button>


            <span
                class="
                    text-base
                    font-semibold
                "
            >
                My Application
            </span>

        </header>


        <!-- =====================================================
             PAGE CONTENT
             ===================================================== -->

        <div
            class="
                w-full
                min-w-0
                box-border

                p-3

                sm:p-4

                lg:p-6
            "
        >

            <?= $content ?>

        </div>


    </main>


</div>


<!-- =============================================================
     SIDEBAR JAVASCRIPT
     ============================================================= -->

<script>

    const sidebar = document.getElementById('sidebar');

    const overlay = document.getElementById('sidebar-overlay');

    const openBtn = document.getElementById('open-sidebar');

    const closeBtn = document.getElementById('close-sidebar');


    /*
     * ============================================================
     * OPEN SIDEBAR
     * ============================================================
     */

    function openSidebar() {

        if (!sidebar || !overlay) {
            return;
        }


        // Slide sidebar into view.

        sidebar.classList.remove('-translate-x-full');

        sidebar.classList.add('translate-x-0');


        // Show overlay.

        overlay.classList.remove(
            'opacity-0',
            'pointer-events-none'
        );

        overlay.classList.add(
            'opacity-100',
            'pointer-events-auto'
        );


        // Prevent page from scrolling behind sidebar.

        document.body.classList.add('overflow-hidden');


        // Accessibility.

        openBtn?.setAttribute(
            'aria-expanded',
            'true'
        );

        overlay.setAttribute(
            'aria-hidden',
            'false'
        );

    }


    /*
     * ============================================================
     * CLOSE SIDEBAR
     * ============================================================
     */

    function closeSidebar() {

        if (!sidebar || !overlay) {
            return;
        }


        // Slide sidebar out.

        sidebar.classList.remove('translate-x-0');

        sidebar.classList.add('-translate-x-full');


        // Hide overlay.

        overlay.classList.remove(
            'opacity-100',
            'pointer-events-auto'
        );

        overlay.classList.add(
            'opacity-0',
            'pointer-events-none'
        );


        // Restore page scrolling.

        document.body.classList.remove('overflow-hidden');


        // Accessibility.

        openBtn?.setAttribute(
            'aria-expanded',
            'false'
        );

        overlay.setAttribute(
            'aria-hidden',
            'true'
        );

    }


    /*
     * ============================================================
     * OPEN BUTTON
     * ============================================================
     */

    openBtn?.addEventListener(
        'click',
        openSidebar
    );


    /*
     * ============================================================
     * CLOSE BUTTON
     * ============================================================
     */

    closeBtn?.addEventListener(
        'click',
        closeSidebar
    );


    /*
     * ============================================================
     * CLICK OVERLAY
     * ============================================================
     */

    overlay?.addEventListener(
        'click',
        closeSidebar
    );


    /*
     * ============================================================
     * NAVIGATION
     *
     * Close sidebar after clicking a link on mobile/tablet.
     * ============================================================
     */

    sidebar
        ?.querySelectorAll('nav a')
        .forEach((link) => {

            link.addEventListener(
                'click',
                closeSidebar
            );

        });


    /*
     * ============================================================
     * ESCAPE KEY
     * ============================================================
     */

    document.addEventListener(
        'keydown',
        (event) => {

            if (event.key === 'Escape') {

                closeSidebar();

            }

        }
    );


    /*
     * ============================================================
     * WINDOW RESIZE
     *
     * If the browser changes from mobile/tablet to desktop,
     * remove the mobile overlay state.
     * ============================================================
     */

    window.addEventListener(
        'resize',
        () => {

            if (window.innerWidth >= 1024) {

                document.body.classList.remove(
                    'overflow-hidden'
                );


                overlay?.classList.remove(
                    'opacity-100',
                    'pointer-events-auto'
                );

                overlay?.classList.add(
                    'opacity-0',
                    'pointer-events-none'
                );


                openBtn?.setAttribute(
                    'aria-expanded',
                    'false'
                );


                overlay?.setAttribute(
                    'aria-hidden',
                    'true'
                );

            }

        }
    );

</script>


<?php $this->endBody(); ?>

</body>

</html>


<?php $this->endPage(); ?>