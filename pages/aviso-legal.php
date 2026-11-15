<?php
require_once __DIR__ . '/../includes/i18n.php';
$lang = current_lang();
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') ?>">
<head>
    <?php include __DIR__ . '/../includes/ads.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('legal.title') ?> | <?= __('header.title') ?></title>
    <meta name="description" content="<?= htmlspecialchars(__('legal.desc'), ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= asset_url('assets/styles.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="<?= asset_url('favicon.ico') ?>">
</head>
<body class="legal-page">
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main class="container page-content">
        <h1><?= __('legal.h1') ?></h1>

        <div class="legal-text">
            <h2><?= __('legal.t1') ?></h2>
            <p><?= __('legal.p1') ?></p>

            <h2><?= __('legal.t2') ?></h2>
            <p><?= __('legal.p2') ?></p>

            <h2><?= __('legal.t3') ?></h2>
            <p><?= __('legal.p3') ?></p>

            <h2><?= __('legal.t4') ?></h2>
            <p><?= __('legal.p4') ?></p>

            <h2><?= __('legal.t5') ?></h2>
            <p><?= __('legal.p5') ?></p>
        </div>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
    <script src="<?= asset_url('assets/script.js') ?>"></script>
</body>
</html>
