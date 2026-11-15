<?php
require_once __DIR__ . '/../includes/analytics.php';
require_once __DIR__ . '/../includes/i18n.php';

// Language detection fallback for direct access (ErrorDocument)
if (!defined('CURRENT_LANG')) {
    $lang = $_COOKIE['hockey5_lang'] ?? 'es';
    if (!in_array($lang, ['es', 'ca', 'en', 'fr', 'pt'])) {
        $lang = 'es';
    }
    define('CURRENT_LANG', $lang);
}
load_translations(CURRENT_LANG);
$local_file = __DIR__ . '/locales/' . CURRENT_LANG . '.php';
if (file_exists($local_file)) {
    $local_translations = require $local_file;
    if (is_array($local_translations)) {
        $GLOBALS['i18n_translations'] = array_merge($GLOBALS['i18n_translations'], $local_translations);
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo current_lang(); ?>">
<head>
    <?php include __DIR__ . '/../includes/ads.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('error.400.title'); ?> - Hockey5</title>
    <link rel="stylesheet" href="<?php echo asset_url('paginas_errores/assets/styles.css'); ?>">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main class="error-container">
        <h1 class="error-title"><?php echo __('error.400.title'); ?></h1>
        <p class="error-subtitle"><?php echo __('error.400.subtitle'); ?></p>
        <p class="error-text">
            <?php echo __('error.400.text'); ?>
        </p>
        <p class="error-text">
            <?php echo __('error.400.system'); ?>
        </p>
        <a href="<?php echo url('home'); ?>" class="error-button"><?php echo __('guess.howto.back_btn'); ?></a>
        
        <div class="games-grid">
            <a href="<?php echo url('hockeygrid_league'); ?>" class="game-card">
                 <span class="game-title">HockeyGrid</span>
                 <span class="game-desc">Immaculate Grid de Hockey</span>
            </a>
            <a href="<?php echo url('contact'); ?>" class="game-card">
                <span class="game-title"><?php echo __('contact'); ?></span>
                <span class="game-desc">Hockey5 Support</span>
            </a>
        </div>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
