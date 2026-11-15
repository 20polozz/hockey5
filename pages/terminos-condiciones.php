<?php
declare(strict_types=1);

// pages/terminos-condiciones.php
$lastUpdate = "01/02/2026";
?>
<!doctype html>
<html lang="<?= current_lang() ?>">
<head>
    <?php include __DIR__ . '/../includes/ads.php'; ?>
  <?php include __DIR__ . '/../includes/analytics.php'; ?>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= __('terms.title') ?></title>
  <meta name="description" content="<?= __('terms.desc') ?>">
  <link rel="stylesheet" href="<?php echo asset_url('assets/'); ?>styles.css">

<link rel="canonical" href="https://www.hockey5.pro/pages/terminos-condiciones.php">
<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" type="image/png" href="/assets/img/favicon-48.png" sizes="48x48">
<link rel="icon" type="image/png" href="/assets/img/favicon-192.png" sizes="192x192">
<link rel="apple-touch-icon" href="/assets/img/favicon-192.png">

<meta property="og:type" content="website">
<meta property="og:title" content="<?= __('terms.title') ?>">

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
      <h1 style="margin: 0 0 8px 0;"><?= __('terms.h1') ?></h1>
      <p style="opacity:.85; margin:0;">
        <?= sprintf(__('cookies.update'), htmlspecialchars($lastUpdate, ENT_QUOTES, 'UTF-8')) ?>
      </p>

    </section>

    <section class="editorial-content" style="line-height: 1.7; background: transparent;">
      <h2 style="margin-top: 22px;"><?= __('terms.intro.title') ?></h2>
      <p>
        <?= __('terms.intro.p1') ?>
      </p>

      <h2 style="margin-top: 22px;"><?= __('terms.ip.title') ?></h2>
      <p>
        <?= __('terms.ip.p1') ?>
      </p>

      <h2 style="margin-top: 22px;"><?= __('terms.use.title') ?></h2>
      <p>
        <?= __('terms.use.p1') ?>
      </p>
      <ul>
        <li><?= __('terms.use.li1') ?></li>
        <li><?= __('terms.use.li2') ?></li>
      </ul>

      <h2 style="margin-top: 22px;"><?= __('terms.liability.title') ?></h2>
      <p>
        <?= __('terms.liability.p1') ?>
      </p>
      <p>
        <?= __('terms.liability.p2') ?>
      </p>

      <h2 style="margin-top: 22px;"><?= __('terms.links.title') ?></h2>
      <p>
        <?= __('terms.links.p1') ?>
      </p>

      <h2 style="margin-top: 22px;"><?= __('terms.modifications.title') ?></h2>
      <p>
        <?= __('terms.modifications.p1') ?>
      </p>

      <h2 style="margin-top: 22px;"><?= __('terms.contact.title') ?></h2>
      <p>
        <?= sprintf(__('terms.contact.p1'), url('contact')) ?>
      </p>

      <h2 style="margin-top: 22px;"><?= __('terms.conduct.title') ?></h2>
      <p>
        <?= __('terms.conduct.p1') ?>
      </p>

      <h2 style="margin-top: 22px;"><?= __('terms.jurisdiction.title') ?></h2>
      <p>
        <?= __('terms.jurisdiction.p1') ?>
      </p>
    </section>

  </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
