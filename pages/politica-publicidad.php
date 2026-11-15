<?php
declare(strict_types=1);

// pages/politica-publicidad.php
$lastUpdate = "01/02/2026";
?>
<!doctype html>
<html lang="<?= current_lang() ?>">
<head>
    <?php include __DIR__ . '/../includes/ads.php'; ?>
  <?php include __DIR__ . '/../includes/analytics.php'; ?>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= __('ads.title') ?></title>
  <meta name="description" content="<?= __('ads.desc') ?>">
  <link rel="stylesheet" href="<?php echo asset_url('assets/'); ?>styles.css">

<link rel="canonical" href="https://www.hockey5.pro/pages/politica-publicidad.php">
<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" type="image/png" href="/assets/img/favicon-48.png" sizes="48x48">
<link rel="icon" type="image/png" href="/assets/img/favicon-192.png" sizes="192x192">
<link rel="apple-touch-icon" href="/assets/img/favicon-192.png">

<meta property="og:type" content="website">
<meta property="og:title" content="<?= __('ads.title') ?>">

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
      <h1 style="margin: 0 0 8px 0;"><?= __('ads.h1') ?></h1>
      <p style="opacity:.85; margin:0;">
        <?= sprintf(__('cookies.update'), htmlspecialchars($lastUpdate, ENT_QUOTES, 'UTF-8')) ?>
      </p>
    </section>

    <section class="editorial-content" style="line-height: 1.7; background: transparent;">
      <h2 style="margin-top: 22px;"><?= __('ads.transparency.title') ?></h2>
      <p>
        <?= __('ads.transparency.p1') ?>
      </p>

      <h2 style="margin-top: 22px;"><?= __('ads.google.title') ?></h2>
      <p>
        <?= __('ads.google.p1') ?>
      </p>
      <p>
        <?= __('ads.google.p2') ?>
      </p>

      <h2 style="margin-top: 22px;"><?= __('ads.control.title') ?></h2>
      <p>
        <?= __('ads.control.p1') ?>
      </p>

      <h2 style="margin-top: 22px;"><?= __('ads.standards.title') ?></h2>
      <p>
        <?= __('ads.standards.p1') ?>
      </p>
      <ul>
        <li><?= __('ads.standards.li1') ?></li>
        <li><?= __('ads.standards.li2') ?></li>
        <li><?= __('ads.standards.li3') ?></li>
      </ul>
    </section>

  </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
