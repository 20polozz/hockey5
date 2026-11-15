<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/settings.php';

$year = date('Y');

$donationCurrent = (float) get_setting('donation_current', '0');
$donationGoal = (float) get_setting('donation_goal', '150');
if ($donationGoal <= 0) $donationGoal = 150;
$donationPercent = max(0, min(100, ($donationCurrent / $donationGoal) * 100));
?>
<!doctype html>
<html lang="<?php echo current_lang(); ?>">
<head>
  <?php include __DIR__ . '/../includes/analytics.php'; ?>
  <meta charset="utf-8"/>
  <?php include __DIR__ . '/../includes/ads.php'; ?>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Hockey5 | Grid y Wordle de Hockey Patines</title>
  <meta name="description" content="¡Demuestra cuánto sabes de hockey! Juega cada día al HockeyGrid y al Wordle de la OK Liga. El reto diario para los amantes del hockey sobre patines.">
  <link rel="stylesheet" href="<?php echo asset_url('assets/'); ?>styles.css"/>

  <!-- Hreflang Tags -->
  <link rel="alternate" hreflang="es" href="https://www.hockey5.pro/es" />
  <link rel="alternate" hreflang="ca" href="https://www.hockey5.pro/ca" />
  <link rel="alternate" hreflang="en" href="https://www.hockey5.pro/en" />
  <link rel="alternate" hreflang="fr" href="https://www.hockey5.pro/fr" />
  <link rel="alternate" hreflang="pt" href="https://www.hockey5.pro/pt" />
  <link rel="alternate" hreflang="x-default" href="https://www.hockey5.pro" />

<link rel="canonical" href="https://www.hockey5.pro<?php echo get_url_for_lang(current_lang()); ?>"> <!-- Canonical should point to current lang version? Or default? Usually current. -->
<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" type="image/png" href="<?php echo asset_url('assets/'); ?>img/favicon-48.png" sizes="48x48">
<link rel="icon" type="image/png" href="<?php echo asset_url('assets/'); ?>img/favicon-192.png" sizes="192x192">
<link rel="apple-touch-icon" href="<?php echo asset_url('assets/'); ?>img/favicon-192.png">

  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://www.hockey5.pro/">
  <meta property="og:title" content="Hockey5 | Juegos Diarios de Hockey Patines">
  <meta property="og:description" content="Juega al HockeyGrid y Wordle de la OK Liga. Pon a prueba tus conocimientos sobre hockey patines con nuevos retos cada día.">
  <meta property="og:image" content="https://www.hockey5.pro/assets/img/logo_h5.png">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">

  <!-- Twitter -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="Hockey5 | Juegos Diarios de Hockey Patines">
  <meta name="twitter:description" content="Juega al HockeyGrid y Wordle de la OK Liga. Pon a prueba tus conocimientos sobre hockey patines con nuevos retos cada día.">
  <meta name="twitter:image" content="https://www.hockey5.pro/assets/img/logo_h5.png">

  <!-- Structured Data (JSON-LD) -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "Hockey5",
    "url": "https://www.hockey5.pro/",
    "description": "Juegos diarios de hockey sobre patines: HockeyGrid y Wordle.",
    "publisher": {
      "@type": "Organization",
      "name": "Hockey5",
      "logo": {
        "@type": "ImageObject",
        "url": "https://www.hockey5.pro/assets/img/logo_h5.png"
      }
    },
    "image": "https://www.hockey5.pro/assets/img/logo_h5.png"
  }
  </script>

</head>
<body>

  <?php include __DIR__ . '/../includes/header.php'; ?>

<div class="layout-main">

  <main class="container hub">
  <section class="hub-top">
    <div class="hub-announce">
      <div class="hub-announce-head">
        <span class="announce-icon" aria-hidden="true">🚧</span>
        <strong><?php echo __('home.beta.title'); ?></strong>
      </div>
      <p>
        <?php echo __('home.beta.text'); ?>
      </p>
      <a class="announce-link ga-track" href="<?php echo url('contact'); ?>"
         data-ga-action="click_contact" data-ga-category="home" data-ga-label="beta_banner"><?php echo __('home.beta.contact'); ?></a>
    </div>
  </section>

  <h1 class="hub-title"><?php echo __('home.choose_game'); ?></h1>

  <section class="hub-grid">
    <a class="hub-card ga-track" href="<?php echo url('hockeygrid_menu'); ?>" 
       data-ga-action="select_game" data-ga-category="home" data-ga-label="hockeygrid">
      <div class="hub-preview">
        <img src="<?php echo asset_url('assets/'); ?>img/logo_grid.png" alt="Hockey5 Grid">
      </div>
      <div class="hub-play"><span><?php echo __('home.play'); ?></span></div>
      <div class="hub-name">Hockey5 Grid</div>
    </a>
    <a class="hub-card ga-track" href="<?php echo url('wordle_menu'); ?>"
       data-ga-action="select_game" data-ga-category="home" data-ga-label="wordle">
      <div class="hub-preview">
        <img src="<?php echo asset_url('assets/'); ?>img/logo_wordle.png" alt="Hockey5 Wordle">
      </div>
      <div class="hub-play"><span><?php echo __('home.play'); ?></span></div>
      <div class="hub-name">Hockey5 Wordle</div>
    </a>
    <a class="hub-card ga-track" href="<?php echo url('guess_menu'); ?>"
       data-ga-action="select_game" data-ga-category="home" data-ga-label="guess">
      <div class="hub-preview">
        <img src="<?php echo asset_url('assets/'); ?>img/logo_guess.png" alt="Hockey5 <?php echo __('guess.title'); ?>">
      </div>
      <div class="hub-play"><span><?php echo __('home.play'); ?></span></div>
      <div class="hub-name">Hockey5 <?php echo __('guess.title'); ?></div>
    </a>
    <a class="hub-card ga-track" href="<?php echo url('clubs_menu'); ?>"
       data-ga-action="select_game" data-ga-category="home" data-ga-label="clubs">
      <div class="hub-preview">
        <img src="<?php echo asset_url('assets/'); ?>img/logo_clubs.png" alt="Hockey5 Clubs">
      </div>
      <div class="hub-play"><span><?php echo __('home.play'); ?></span></div>
      <div class="hub-name"><?php echo __('clubs.title'); ?></div>
    </a>
    </section>

    <section class="section stats-section" style="text-align: center;">
      <h2 class="section-title" style="margin-bottom: 26px;"><?php echo __('home.stats.title'); ?></h2>
      <div class="hero-stats" style="justify-content: center; flex-wrap: wrap; gap: 16px;">
        <div class="stat" style="min-width: 160px;">
          <div class="stat-num">14</div>
          <div class="stat-label"><?php echo __('home.stats.okliga'); ?></div>
        </div>
        <div class="stat" style="min-width: 160px;">
          <div class="stat-num">14</div>
          <div class="stat-label"><?php echo __('home.stats.placard'); ?></div>
        </div>
        <div class="stat" style="min-width: 160px;">
          <div class="stat-num">+500</div>
          <div class="stat-label"><?php echo __('home.stats.players'); ?></div>
        </div>
      </div>
    </section>
  </main>
</div>

  <!-- SECCIÓN EDITORIAL (SEO & ADSENSE) -->
  <section class="editorial-content">
    <h2><?php echo __('home.editorial.title'); ?></h2>
    <p>
      <?php echo __('home.editorial.p1'); ?>
    </p>
    <p>
      <?php echo __('home.editorial.p2'); ?>
    </p>


    <h3><?php echo __('home.editorial.subtitle'); ?></h3>
    <p>
      <?php echo __('home.editorial.p3'); ?>
    </p>
    <p>
      <?php echo __('home.editorial.p4'); ?>
    </p>

    <h3><?php echo __('home.editorial.subtitle2'); ?></h3>
    <p>
      <?php echo __('home.editorial.p5'); ?>
    </p>
    <p>
      <?php echo __('home.editorial.p6'); ?>
    </p>
    <p>
      <?php echo __('home.editorial.p7'); ?>
    </p>
    <p>
      <?php echo __('home.editorial.p8'); ?>
    </p>
    <div style="max-width: 420px; margin: 0 auto 20px;">
      <div style="text-align:center; margin-bottom:6px; font-size:14px; color:var(--text);">
        <strong><?php
          echo sprintf(
            __('donation.progress'),
            number_format($donationCurrent, 0, ',', '.'),
            number_format($donationGoal, 0, ',', '.')
          );
        ?></strong>
      </div>
      <div style="background: rgba(255,255,255,0.08); border-radius: 999px; height: 10px; overflow: hidden; border: 1px solid rgba(255,255,255,0.12);">
        <div style="height:100%; width: <?php echo $donationPercent; ?>%; background: linear-gradient(90deg, #4ade80, #22c55e); border-radius: 999px;"></div>
      </div>
      <div style="margin-top: 8px; font-size: 13px; opacity: 0.75; text-align:center;">
        <?php echo __('donation.goal_label'); ?>
      </div>
    </div>

    <a href="https://paypal.me/PHockey5" class="donation-link ga-track" target="_blank" rel="noopener noreferrer" style="display:block; text-align:center;">
      <img src="<?php echo asset_url('assets/'); ?>img/donate-paypal.png" 
          alt="Donate with PayPal" 
          style="max-width:250px; height:auto;">
    </a>

  </section>









<section class="faq-section">
  <div class="container">
    <div class="faq-inner">
    <h2 class="faq-title">
      <?php echo __('home.faq.title'); ?>
    </h2>

    <article class="faq-item">
      <h3><?php echo __('home.faq.q1'); ?></h3>
      <p>
        <?php echo __('home.faq.a1'); ?>
      </p>
    </article>

      <article class="faq-item">
        <h3><?php echo __('home.faq.q2'); ?></h3>
        <p>
          <?php echo __('home.faq.a2'); ?>
        </p>
      </article>
    
    <article class="faq-item">
      <h3><?php echo __('home.faq.q3'); ?></h3>
      <p>
        <?php echo __('home.faq.a3'); ?>
      </p>
      
    </article>
      <article class="faq-item">
        <h3><?php echo __('home.faq.q4'); ?></h3>
        <p>
          <?php echo __('home.faq.a4'); ?>
        </p>
      </article>
    </div>
      </div>
  </div>
</section>



  <?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="<?php echo asset_url('assets/'); ?>script.js"></script>
</body>
</html>
