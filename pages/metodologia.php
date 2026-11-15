<?php
require_once __DIR__ . '/../includes/i18n.php';
$lang = current_lang();
$breadcrumbs = [
    ['label' => __('menu.home'), 'url' => url('home')],
    ['label' => 'Metodología', 'url' => '']
];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') ?>">
<head>
    <?php include __DIR__ . '/../includes/analytics.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metodología - <?= __('header.title') ?></title>
    <meta name="description" content="Conoce la rigurosa metodología utilizada para recopilar datos, estadísticas y hechos históricos de Hockey5.">
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
        
        <h1 style="color: var(--brand); margin-bottom: 24px; font-size: 28px;">Nuestra Metodología</h1>

        <div class="info-section">
            <h2>Recopilación Exhaustiva de Datos</h2>
            <p>El núcleo de Hockey5, incluidos nuestros juegos (Hockey Grid, Wordle, Adivina el Jugador) y nuestro buscador de clubes, se alimenta de una vasta base de datos estadística y documental propia. Cada entrada se investiga meticulosamente, cruzando información de actas oficiales presenciales, hemerotecas deportivas y registros históricos de las federaciones europeas y mundiales correspondientes.</p>
        </div>

        <div class="info-section">
            <h2>Actualización del Contenido</h2>
            <p>Las plantillas de los equipos y la composición de las ligas principales (OK Liga española, Liga Placard portuguesa, Serie A1 italiana y competiciones internacionales) se revisan periódicamente para reflejar altas, bajas y cambios de dorsales.</p>
            <p>Cuando encontramos una discrepancia en las estadísticas históricas, siempre aplicamos el principio de prevalencia: utilizamos la ficha federativa oficial del jugador en la temporada en curso o el acta arbitral de la competición principal como fuente de la verdad.</p>
        </div>

        <div class="info-section">
            <h2>Los Juegos Interactivos</h2>
            <p>Nuestros retos diarios están diseñados por algoritmos que garantizan que siempre exista, al menos, una solución lógica y verificable para cada casillero del Grid o para el Wordle. Nos enorgullecemos de ofrecer desafíos de dificultad calibrada que premian tanto el conocimiento actual como la memoria histórica de este deporte.</p>
            <p>Parámetros como el "porcentaje de popularidad" (en el modo Grid) son un reflejo transparente y en tiempo real del conocimiento colectivo de nuestra gran comunidad; lo actualizamos con cada acierto, ofreciendo una métrica justa e interesante sobre qué jugadores son los más legendarios o recordados por la afición.</p>
        </div>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>