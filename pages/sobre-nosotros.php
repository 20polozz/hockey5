<?php
declare(strict_types=1);

// pages/sobre-nosotros.php
$lastUpdate = "01/02/2026";
?>
<!doctype html>
<!doctype html>
<html lang="<?= current_lang() ?>">
<head>
  <?php include __DIR__ . '/../includes/analytics.php'; ?>
  <meta charset="utf-8" />
  <?php include __DIR__ . '/../includes/ads.php'; ?>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= __('about.title') ?></title>
  <meta name="description" content="<?= __('about.desc') ?>">
  <link rel="stylesheet" href="<?php echo asset_url('assets/'); ?>styles.css">

<link rel="canonical" href="https://www.hockey5.pro/pages/sobre-nosotros.php">
<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" type="image/png" href="/assets/img/favicon-48.png" sizes="48x48">
<link rel="icon" type="image/png" href="/assets/img/favicon-192.png" sizes="192x192">
<link rel="apple-touch-icon" href="/assets/img/favicon-192.png">

<meta property="og:type" content="website">
<meta property="og:title" content="<?= __('about.title') ?>">
<meta property="og:description" content="<?= __('about.desc') ?>">
<meta property="og:image" content="https://www.hockey5.pro/assets/img/logo_h5.png">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= __('about.title') ?>">
<meta name="twitter:description" content="<?= __('about.desc') ?>">
<meta name="twitter:image" content="https://www.hockey5.pro/assets/img/logo_h5.png">

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
      <h1 style="margin: 0 0 8px 0;"><?= __('about.h1') ?></h1>
    </section>

    <section class="editorial-content" style="line-height: 1.7; background: transparent;">
      <h2 style="margin-top: 22px;"><?= __('about.mission.title') ?></h2>
      <p>
        <?= __('about.mission.p1') ?>
      </p>
      <p>
        <?= __('about.mission.p2') ?>
      </p>

      <h2 style="margin-top: 22px;"><?= __('about.who.title') ?></h2>
      <p>
        <?= __('about.who.p1') ?>
      </p>

      <h2 style="margin-top: 22px;"><?= __('about.games.title') ?></h2>
      <p>
        <?= __('about.games.p1') ?>
      </p>
      <ul style="list-style-type: disc; margin-left: 20px;">
        <li><?= __('about.games.li1') ?></li>
        <li><?= __('about.games.li2') ?></li>
        <li><?= __('about.games.li3') ?></li>
      </ul>

      <h2 style="margin-top: 22px;"><?= __('about.quality.title') ?></h2>
      <p>
        <?= sprintf(__('about.quality.p1'), url('contact')) ?>
      </p>

      <h2 style="margin-top: 22px;"><?= __('about.community.title') ?></h2>
      <p>
        <?= __('about.community.p1') ?>
      </p>
    </section>

  </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
