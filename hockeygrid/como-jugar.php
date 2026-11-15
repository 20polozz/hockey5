<?php
declare(strict_types=1);
?>
<!doctype html>
<html lang="<?= current_lang() ?>">
<head>
    <?php include __DIR__ . '/../includes/ads.php'; ?>
  <?php include __DIR__ . '/../includes/analytics.php'; ?>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title><?= __('grid.howto.seo_title') ?></title>
  <meta name="description" content="<?= htmlspecialchars(__('grid.howto.seo_desc')) ?>">
  <link rel="stylesheet" href="<?= asset_url('assets/styles.css') ?>"/>

  <style>
    .howto-wrap{
      padding: 42px 18px 84px;
      min-height: calc(100vh - 84px);
      display:flex;
      justify-content:center;
    }
    .howto-content{
      width: min(760px, 100%);
    }
    .howto-title{
      margin: 0 0 12px;
      font-size: clamp(22px, 3vw, 34px);
      font-weight: 1000;
      letter-spacing: .01em;
      color: var(--text);
    }
    .howto-lead{
      margin: 0 0 22px;
      color: var(--muted);
      line-height: 1.7;
      max-width: 90ch;
    }
    .howto-h2{
      margin: 18px 0 8px;
      font-size: 15px;
      font-weight: 950;
      color: var(--text);
    }
    .howto-text{
      margin: 0 0 14px;
      color: var(--muted);
      line-height: 1.7;
      max-width: 92ch;
    }
    .howto-list{
      margin: 10px 0 16px 18px;
      color: var(--muted);
      line-height: 1.7;
    }
    .howto-list li{ margin: 4px 0; }
    .howto-actions{
      margin-top: 28px;
      display:flex;
      gap:10px;
      justify-content:center;
      flex-wrap:wrap;
    }
    .howto-btn{
      display:inline-block;
      padding: 10px 14px;
      border-radius: 12px;
      font-weight: 900;
      font-size: 13px;
      border: 1px solid rgba(25,211,255,.35);
      background: rgba(25,211,255,.14);
      color: var(--text);
      transition: transform .12s ease, background .12s ease, border-color .12s ease;
    }
    .howto-btn:hover{
      transform: translateY(-1px);
      border-color: rgba(25,211,255,.60);
      background: rgba(25,211,255,.20);
    }
    .howto-btn.secondary{
      border: 1px solid rgba(255,255,255,.18);
      background: rgba(255,255,255,.06);
    }
    .howto-btn.secondary:hover{
      border-color: rgba(255,255,255,.30);
      background: rgba(255,255,255,.10);
    }
  </style>

<link rel="canonical" href="https://www.hockey5.pro/hockeygrid/como-jugar.php">
<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" type="image/png" href="/assets/img/favicon-48.png" sizes="48x48">
<link rel="icon" type="image/png" href="/assets/img/favicon-192.png" sizes="192x192">
<link rel="apple-touch-icon" href="/assets/img/favicon-192.png">

<meta property="og:type" content="website">
<meta property="og:title" content="<?= htmlspecialchars(__('grid.howto.seo_title')) ?>">
<meta property="og:description" content="<?= htmlspecialchars(__('grid.howto.seo_desc')) ?>">
<meta property="og:image" content="https://www.hockey5.pro/assets/img/logo_h5.png">
<meta property="og:image:width" content="512">
<meta property="og:image:height" content="512">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= htmlspecialchars(__('grid.howto.seo_title')) ?>">
<meta name="twitter:description" content="<?= htmlspecialchars(__('grid.howto.seo_desc')) ?>">
<meta name="twitter:image" content="https://www.hockey5.pro/assets/img/logo_h5.png">

</head>
<body>

<?php include __DIR__ . '/../includes/header.php'; ?>

  <main class="howto-wrap">
    <article class="howto-content" role="article" aria-labelledby="howto-title">
      <h1 id="howto-title" class="howto-title"><?= __('grid.howto.h1') ?></h1>

      <p class="howto-lead">
        <?= __('grid.howto.lead') ?>
      </p>

      <h2 class="howto-h2"><?= __('grid.howto.objective_title') ?></h2>
      <p class="howto-text">
        <?= __('grid.howto.objective_text') ?>
      </p>

      <h2 class="howto-h2"><?= __('grid.howto.how_title') ?></h2>
      <ul class="howto-list">
        <li><?= __('grid.howto.how_li1') ?></li>
        <li><?= __('grid.howto.how_li2') ?></li>
        <li><?= __('grid.howto.how_li3') ?></li>
        <li><?= __('grid.howto.how_li4') ?></li>
      </ul>

      <h2 class="howto-h2"><?= __('grid.howto.tips_title') ?></h2>
      <ul class="howto-list">
        <li><?= __('grid.howto.tips_li1') ?></li>
        <li><?= __('grid.howto.tips_li2') ?></li>
        <li><?= __('grid.howto.tips_li3') ?></li>
      </ul>

      <div class="howto-actions" aria-label="<?= __('grid.howto.play_btn') ?>">
        <a class="howto-btn" href="<?= url('hockeygrid_menu') ?>"><?= __('grid.howto.play_btn') ?></a>
        <a class="howto-btn secondary" href="<?= url('home') ?>"><?= __('grid.howto.back_btn') ?></a>
      </div>
    </article>
  </main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
