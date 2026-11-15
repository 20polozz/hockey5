<?php
require_once __DIR__ . '/../includes/i18n.php';
$lang = current_lang();
$breadcrumbs = [
    ['label' => __('menu.home'), 'url' => url('home')],
    ['label' => __('menu.glossary'), 'url' => '']
];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') ?>">
<head>
    <?php include __DIR__ . '/../includes/ads.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('glossary.title') ?></title>
    <meta name="description" content="<?= __('glossary.desc') ?>">
    <link rel="stylesheet" href="<?= asset_url('assets/styles.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="<?= asset_url('favicon.ico') ?>">
    <style>
        .glossary-page { max-width: 800px; margin: 0 auto; padding: 40px 18px; line-height: 1.7; }
        .glossary-term { margin-bottom: 30px; border-bottom: 1px solid var(--line); padding-bottom: 20px; }
        .glossary-term h3 { color: var(--brand2); font-size: 22px; margin-bottom: 10px; }
    </style>
</head>
<body class="legal-page">
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main class="container page-content glossary-page">
        <?php include __DIR__ . '/../includes/breadcrumbs.php'; ?>

        <h1 style="font-size: 36px; margin-top: 20px;"><?= __('glossary.h1') ?></h1>
        <p style="font-size: 18px; color: var(--muted); margin-bottom: 40px;"><?= __('glossary.lead') ?></p>

        <?php 
        $terms = ['arrastre', 'bola_parada', 'boya', 'falta_equipo', 'falta_directa', 'guarda', 'okliga', 'tarjeta_azul', 'zona', 'pala', 'contrameta'];
        foreach ($terms as $term): 
        ?>
        <div class="glossary-term">
            <h3><?= __('glossary.term.' . $term . '.title') ?></h3>
            <p><?= __('glossary.term.' . $term . '.desc') ?></p>
        </div>
        <?php endforeach; ?>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
    <script src="<?= asset_url('assets/script.js') ?>"></script>
</body>
</html>
