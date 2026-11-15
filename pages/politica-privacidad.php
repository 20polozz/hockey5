<?php
declare(strict_types=1);

// pages/politica-privacidad.php
$lastUpdate = "01/02/2026"; // cámbialo si quieres
?>
<!doctype html>
<html lang="<?= current_lang() ?>">
<head>
    <?php include __DIR__ . '/../includes/ads.php'; ?>
  <?php include __DIR__ . '/../includes/analytics.php'; ?>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= __('privacy.title') ?></title>
  <meta name="description" content="<?= __('privacy.desc') ?>">
  <link rel="stylesheet" href="<?php echo asset_url('assets/'); ?>styles.css">


<link rel="canonical" href="https://www.hockey5.pro/pages/politica-privacidad.php">
<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" type="image/png" href="/assets/img/favicon-48.png" sizes="48x48">
<link rel="icon" type="image/png" href="/assets/img/favicon-192.png" sizes="192x192">
<link rel="apple-touch-icon" href="/assets/img/favicon-192.png">

<meta property="og:type" content="website">
<meta property="og:title" content="<?= __('privacy.title') ?>">
<meta property="og:description" content="<?= __('privacy.desc') ?>">
<meta property="og:image" content="https://www.hockey5.pro/assets/img/logo_h5.png">
<meta property="og:image:width" content="512">
<meta property="og:image:height" content="512">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= __('privacy.title') ?>">
<meta name="twitter:description" content="<?= __('privacy.desc') ?>">
<meta name="twitter:image" content="https://www.hockey5.pro/assets/img/logo_h5.png">

</head>

<body>

<?php include __DIR__ . '/../includes/header.php'; ?>

<main class="container" style="padding: 32px 16px;">
  <!-- Card con fondo uniforme para evitar bandas de color -->
<div style="
  max-width: 900px;
  margin: 0 auto;
  padding: 28px 22px;
">

    <section style="margin-bottom: 18px;">
      <h1 style="margin: 0 0 8px 0;"><?= __('privacy.h1') ?></h1>
      <p style="opacity:.85; margin:0;">
        <?= sprintf(__('privacy.update'), htmlspecialchars($lastUpdate, ENT_QUOTES, 'UTF-8')) ?>
      </p>
    </section>

    <section class="legal-content" style="line-height: 1.7; background: transparent;">
      <p style="background: transparent;">
        <?= __('privacy.intro') ?>
      </p>

      <h2 style="margin-top: 22px; background: transparent;"><?= __('privacy.t1') ?></h2>
      <p style="background: transparent;">
        <?= __('privacy.p1') ?>
      </p>

      <h2 style="margin-top: 22px; background: transparent;"><?= __('privacy.t2') ?></h2>
      <p style="background: transparent;">
        <?= __('privacy.p2') ?>
      </p>

      <h2 style="margin-top: 22px; background: transparent;"><?= __('privacy.t3') ?></h2>
      <p style="background: transparent;">
        <?= __('privacy.p3') ?>
      </p>

      <h2 style="margin-top: 22px; background: transparent;"><?= __('privacy.t4') ?></h2>
      <p style="background: transparent;">
        <?= __('privacy.p4') ?>
      </p>

      <h2 style="margin-top: 22px; background: transparent;"><?= __('privacy.t5') ?></h2>
      <p style="background: transparent;">
        <?= __('privacy.p5') ?>
      </p>

      <h2 style="margin-top: 22px; background: transparent;"><?= __('privacy.t6') ?></h2>
      <p style="background: transparent;">
        <?= __('privacy.p6') ?>
      </p>

      <h2 style="margin-top: 22px; background: transparent;"><?= __('privacy.t7') ?></h2>
      <p style="background: transparent;"><?= __('privacy.p7') ?></p>

      <h2 style="margin-top: 22px; background: transparent;"><?= __('privacy.t8') ?></h2>
      <p style="background: transparent;">
        <?= __('privacy.p8') ?>
      </p>

      <h2 style="margin-top: 22px; background: transparent;"><?= __('privacy.t9') ?></h2>
      <p style="background: transparent;">
        <?= __('privacy.p9') ?>
      </p>

      <h2 style="margin-top: 22px; background: transparent;"><?= __('privacy.t10') ?></h2>
      <p style="background: transparent;">
        <?= __('privacy.p10') ?>
      </p>

      <h2 style="margin-top: 22px; background: transparent;"><?= __('privacy.t11') ?></h2>
      <p style="background: transparent;">
        <?= __('privacy.p11') ?>
      </p>

      <h2 style="margin-top: 22px; background: transparent;"><?= __('privacy.t12') ?></h2>
      <p style="background: transparent;">
        <?= __('privacy.p12') ?>
      </p>

      <h2 style="margin-top: 22px; background: transparent;"><?= __('privacy.t13') ?></h2>
      <p style="background: transparent;">
        <?= __('privacy.p13') ?>
      </p>

      <h2 style="margin-top: 22px; background: transparent;"><?= __('privacy.t14') ?></h2>
      <p style="background: transparent;">
        <?= __('privacy.p14') ?>
      </p>

      <h2 style="margin-top: 22px; background: transparent;"><?= __('privacy.t15') ?></h2>
      <p style="background: transparent;">
        <?= __('privacy.p15') ?>
      </p>

      <h2 style="margin-top: 22px; background: transparent;"><?= __('privacy.t16') ?></h2>
      <p style="background: transparent;">
        <?= __('privacy.p16') ?>
      </p>
    </section>

  </div>
</main>




<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
