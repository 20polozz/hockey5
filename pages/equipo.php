<?php
require_once __DIR__ . '/../includes/i18n.php';
$lang = current_lang();
$breadcrumbs = [
    ['label' => __('menu.home'), 'url' => url('home')],
    ['label' => 'Equipo', 'url' => '']
];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') ?>">
<head>
    <?php include __DIR__ . '/../includes/analytics.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipo - <?= __('header.title') ?></title>
    <meta name="description" content="Conoce al apasionado equipo de profesionales y aficionados detrás de la creación de Hockey5.">
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
        
        <h1 style="color: var(--brand); margin-bottom: 24px; font-size: 28px;">Nuestro Equipo</h1>

        <div class="info-section">
            <h2>Pasión por el Hockey sobre Patines</h2>
            <p>Hockey5 nace de la pasión inquebrantable de un grupo de aficionados al hockey sobre patines y profesionales del desarrollo web. Nuestro objetivo siempre ha sido llevar la riqueza estadística, histórica y cultural de este deporte al mundo digital de una forma interactiva, rigurosa y visualmente atractiva.</p>
        </div>

        <div class="info-section">
            <h2>Un Proyecto de la Comunidad</h2>
            <p>No somos solo desarrolladores; somos seguidores habituales de las principales ligas, asistentes a los pabellones cada fin de semana y estudiosos de la gran historia del hockey sobre ruedas. Invertimos cientos de horas recopilando datos, contrastando información histórica y diseñando juegos que pongan a prueba hasta al aficionado más erudito.</p>
        </div>

        <div class="info-section">
            <h2>Colaboraciones y Futuro</h2>
            <p>Creemos en el crecimiento colectivo y en la fuerza de la comunidad. Invitamos constantemente a clubes, jugadores profesionales, estadísticos y periodistas deportivos a colaborar con nosotros para mantener nuestra gran base de datos viva y constantemente actualizada. Si compartes nuestra visión y quieres sumar tu granito de arena, tienes las puertas abiertas.</p>
        </div>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>