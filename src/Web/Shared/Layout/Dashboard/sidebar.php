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

<body>

<?php $this->beginBody(); ?>

<div class="flex min-h-screen">

    <!-- Sidebar -->
    <aside class="relative w-64 min-h-screen bg-gray-900 text-white">

        <!-- Application title -->
        <div class="p-6">

            <a class="text-xl font-bold" href="/home">
                My Application
            </a>

        </div>

        <!-- Navigation -->
        <nav class="px-4 space-y-2">

            <a
                href="/"
                class="block rounded-lg px-4 py-3 hover:bg-gray-800 transition"
            >
                Dashboard
            </a>

            <a
                href="/user"
                class="block rounded-lg px-4 py-3 hover:bg-gray-800 transition"
            >
                Users
            </a>

        </nav>

        <!-- Logout -->
        <div class="absolute bottom-4 left-0 right-0 px-4">

            <a
                href="/logout"
                class="block rounded-lg px-4 py-3 hover:bg-gray-800 transition text-red-500 font-semibold text-xl"
            >
                Logout
            </a>

        </div>

    </aside>


    <!-- Main content -->
    <main class="flex-1 bg-gray-100 p-6">

        <?= $content ?>

    </main>

</div>

<?php $this->endBody(); ?>

</body>

</html>

<?php $this->endPage(); ?>