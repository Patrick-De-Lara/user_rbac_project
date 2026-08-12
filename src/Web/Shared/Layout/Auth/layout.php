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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php $this->head(); ?>
</head>

<body class="bg-gray-100">

<?php $this->beginBody(); ?>

<?= $content ?>

<?php $this->endBody(); ?>

</body>

</html>

<?php $this->endPage(); ?>