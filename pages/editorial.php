<?php
require_once __DIR__ . '/../includes/i18n.php';
$lang = current_lang();
$breadcrumbs = [
    ['label' => __('menu.home'), 'url' => url('home')],
    ['label' => 'Línea Editorial', 'url' => '']
];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') ?>">
<head>
    <?php include __DIR__ . '/../includes/analytics.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Línea Editorial - <?= __('header.title') ?></title>
    <meta name="description" content="Descubre la misión, visión y los valores editoriales del proyecto Hockey5.">
    <link rel="stylesheet" href="<?= asset_url('assets/styles.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="<?= asset_url('favicon.ico') ?>">
    <style>
        .info-page { max-width: 800px; margin: 0 auto; padding: 40px 18px; }
        .info-section { margin-bottom: 25px; background: var(--bg2); padding: 24px; border-radius: 12px; border: 1px solid var(--line); }
        .info-section h2 { color: var(--brand2); font-size: 20px; margin-bottom: 12px; }
        .info-section p { line-height: 1.6; color: var(--text); margin-bottom: 12px; }
    </style>
</head>
<body class="legal-page">
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main class="container page-content info-page">
        <?php include __DIR__ . '/../includes/breadcrumbs.php'; ?>
        
        <h1 style="color: var(--brand); margin-bottom: 24px; font-size: 28px;">Línea Editorial y Proyecto Hockey5</h1>

        <div class="info-section">
            <h2>Nuestra Misión</h2>
            <p>Hockey5 es una plataforma digital pionera dedicada exclusivamente al hockey sobre patines. Nuestra misión principal es divulgar este maravilloso deporte combinando entretenimiento interactivo de vanguardia con un contenido informativo riguroso, educativo y de altísimo valor para el usuario.</p>
            <p>Queremos ser el punto de encuentro diario donde los aficionados de todo el mundo puedan aprender sobre clubes históricos, jugadores de élite, competiciones legendarias y las curiosidades más profundas de este deporte.</p>
        </div>

        <div class="info-section">
            <h2>Valor Añadido y Rigor</h2>
            <p>Nos comprometemos a ofrecer datos reales y debidamente contrastados. Creemos firmemente que el hockey sobre patines merece la misma calidad de tratamiento estadístico, periodístico e histórico que los deportes considerados mayoritarios. Por ello, nuestra línea editorial se basa en el respeto absoluto hacia los clubes, las instituciones y, muy especialmente, los profesionales que hacen grande este deporte.</p>
        </div>

        <div class="info-section">
            <h2>El Ecosistema Hockey5</h2>
            <p>Más allá de ser un portal de entretenimiento, Hockey5 aspira a convertirse en una biblioteca viva. Proporcionamos guías, artículos tácticos y resúmenes históricos que complementan nuestros retos diarios (Grid, Wordle, Adivina el Jugador). De esta manera, aseguramos que cada visita de nuestros usuarios se traduzca en una experiencia enriquecedora y de aprendizaje continuo.</p>
        </div>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>