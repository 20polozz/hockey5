<?php
require_once __DIR__ . '/../includes/i18n.php';
$lang = current_lang();
$breadcrumbs = [
    ['label' => __('menu.home'), 'url' => url('home')],
    ['label' => __('menu.faq'), 'url' => '']
];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') ?>">
<head>
    <?php include __DIR__ . '/../includes/ads.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - <?= __('header.title') ?></title>
    <meta name="description" content="<?= __('home.faq.title') ?>">
    <link rel="stylesheet" href="<?= asset_url('assets/styles.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="<?= asset_url('favicon.ico') ?>">
    <style>
        .faq-page { max-width: 800px; margin: 0 auto; padding: 40px 18px; }
        .faq-item { margin-bottom: 25px; background: var(--bg2); padding: 24px; border-radius: 12px; border: 1px solid var(--line); }
        .faq-item h3 { color: var(--brand2); font-size: 20px; margin-bottom: 12px; }
        .faq-item p { line-height: 1.6; color: var(--text); }
    </style>
</head>
<body class="legal-page">
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main class="container page-content faq-page">
        <?php include __DIR__ . '/../includes/breadcrumbs.php'; ?>


        <div class="faq-item-page">
            <h3>¿Qué significa el % de popularidad en el Hockey Grid?</h3>
            <p>Al acertar una casilla del Hockey Grid, obtendrás una puntuación de "popularidad" (porcentaje). Este número indica el porcentaje de otros usuarios que eligieron a ese mismo jugador para esa casilla. Cuanto menor sea el porcentaje, ¡más inusual e ingeniosa ha sido tu respuesta!</p>
        </div>

        <div class="faq-item-page">
            <h3>El buscador no reconoce a un jugador, ¿qué hago?</h3>
            <p>La base de datos contiene más de 500 jugadores seleccionados manualmente, cubriendo las ligas principales (España, Portugal, Italia y Argentina). Intenta buscar su apellido en lugar de su primer nombre, o usa la escritura original. Si estás completamente seguro de que debería estar, puedes pulsar en "¿Algún error?" e informarnos de la ausencia.</p>
        </div>

        <div class="faq-item-page">
            <h3>¿Hay clasificación mundial?</h3>
            <p>Actualmente no disponemos de un ranking global persistente, el juego está pensado para el reto personal diario y poder compartir cuadrados de colores en redes sociales como Twitter (X) o Whatsapp. En el futuro, evaluaremos crear sistemas de cuentas.</p>
        </div>

        <div class="faq-item-page">
            <h3>¿Cómo puedo colaborar con el proyecto de Hockey5?</h3>
            <p>Hockey5 es un hub de la comunidad. Puedes apoyarnos donando a través del enlace de PayPal, o aportando sugerencias en el formulario de contacto para añadir más jugadores históricos o datos estadísticos.</p>
        </div>

    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
    <script src="<?= asset_url('assets/script.js') ?>"></script>
</body>
</html>
