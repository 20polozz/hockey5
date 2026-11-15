<?php
declare(strict_types=1);

// pages/politica-cookies.php
$lastUpdate = "01/02/2026";
?>
<!doctype html>
<html lang="<?= current_lang() ?>">
<head>
    <?php include __DIR__ . '/../includes/ads.php'; ?>
  <?php include __DIR__ . '/../includes/analytics.php'; ?>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= __('cookies.title') ?></title>
  <meta name="description" content="<?= __('cookies.desc') ?>">
  <link rel="stylesheet" href="<?php echo asset_url('assets/'); ?>styles.css">

<link rel="canonical" href="https://www.hockey5.pro/pages/politica-cookies.php">
<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" type="image/png" href="/assets/img/favicon-48.png" sizes="48x48">
<link rel="icon" type="image/png" href="/assets/img/favicon-192.png" sizes="192x192">
<link rel="apple-touch-icon" href="/assets/img/favicon-192.png">

<meta property="og:type" content="website">
<meta property="og:title" content="<?= __('cookies.title') ?>">

</head>

<body>

<?php include __DIR__ . '/../includes/header.php'; ?>

<main class="container" style="padding: 32px 16px;">
<div style="
  max-width: 900px;
  margin: 0 auto;
  padding: 28px 22px;
">

    <section style="margin-bottom: 18px;">
      <h1 style="margin: 0 0 8px 0;"><?= __('cookies.h1') ?></h1>
      <p style="opacity:.85; margin:0;">
        <?= sprintf(__('cookies.update'), htmlspecialchars($lastUpdate, ENT_QUOTES, 'UTF-8')) ?>
      </p>
    </section>

    <section class="editorial-content" style="line-height: 1.7; background: transparent;">
      <p>
        <?= __('cookies.intro') ?>
      </p>

      <h2 style="margin-top: 22px;"><?= __('cookies.what.title') ?></h2>
      <p>
        <?= __('cookies.what.p1') ?>
      </p>

      <h2 style="margin-top: 22px;"><?= __('cookies.types.title') ?></h2>
      
      <h3><?= __('cookies.type1.title') ?></h3>
      <p>
        <?= __('cookies.type1.p1') ?>
      </p>

      <h3><?= __('cookies.type2.title') ?></h3>
      <p>
        <?= __('cookies.type2.p1') ?>
      </p>

      <h3><?= __('cookies.type3.title') ?></h3>
      <p>
        <?= __('cookies.type3.p1') ?>
      </p>

      <h2 style="margin-top: 22px;"><?= __('cookies.manage.title') ?></h2>
      <p>
        <?= __('cookies.manage.p1') ?>
      </p>
      <ul style="list-style-type: disc; margin-left: 20px;">
        <li><a href="https://support.google.com/chrome/answer/95647?hl=es" target="_blank" rel="nofollow">Google Chrome</a></li>
        <li><a href="https://support.microsoft.com/es-es/help/17442/windows-internet-explorer-delete-manage-cookies" target="_blank" rel="nofollow">Internet Explorer / Edge</a></li>
        <li><a href="https://support.mozilla.org/es/kb/habilitar-y-deshabilitar-cookies-sitios-web-rastrear-preferencias" target="_blank" rel="nofollow">Mozilla Firefox</a></li>
        <li><a href="https://support.apple.com/es-es/guide/safari/sfri11471/mac" target="_blank" rel="nofollow">Safari</a></li>
      </ul>
      <p>
        <?= __('cookies.manage.p2') ?>
      </p>
    </section>

  </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
