<?php
declare(strict_types=1);

$year = date('Y');

$league = (string)($_GET['league'] ?? 'oklliga');
$allowedLeagues = ['oklliga', 'placard', 'internacional'];
if (!in_array($league, $allowedLeagues, true)) {
  $league = 'oklliga';
}

$leagueLabel = [
  'oklliga' => 'OK Lliga',
  'placard' => 'Liga Placard',
  'internacional' => 'Internacional',
][$league] ?? 'OK Lliga';

// Base path (supports hosting the project in a subfolder like /hockey5)
$basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$basePath = rtrim($basePath, '/');
if ($basePath === '.' || $basePath === '/') {
  $basePath = '';
}
?>
<!doctype html>
<html lang="es">
<head>
    <?php include __DIR__ . '/../includes/ads.php'; ?>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Hockey-5 Grid | Juego de hockey sobre patines</title>
  <meta name="description" content="Juego diario de hockey sobre patines para adivinar jugadores, equipos y competiciones de diferentes ligas.">
  <link rel="stylesheet" href="<?php echo asset_url('assets/'); ?>styles.css"/>
  <link rel="stylesheet" href="<?php echo asset_url('assets/'); ?>hoqueigrid.css"/>


<link rel="canonical" href="https://www.hockey5.pro/hoqueigrid">
<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" type="image/png" href="<?php echo asset_url('assets/'); ?>img/favicon-48.png" sizes="48x48">
<link rel="icon" type="image/png" href="<?php echo asset_url('assets/'); ?>img/favicon-192.png" sizes="192x192">
<link rel="apple-touch-icon" href="<?php echo asset_url('assets/'); ?>img/favicon-192.png">

<meta property="og:type" content="website">
<meta property="og:title" content="Hockey-5 Grid | Juego de hockey sobre patines">
<meta property="og:description" content="Juego diario de hockey sobre patines para adivinar jugadores, equipos y competiciones de diferentes ligas.">
<meta property="og:image" content="https://www.hockey5.pro<?php echo asset_url('assets/'); ?>img/logo_h5.png">
<meta property="og:image:width" content="512">
<meta property="og:image:height" content="512">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Hockey-5 Grid | Juego de hockey sobre patines">
<meta name="twitter:description" content="Juego diario de hockey sobre patines para adivinar jugadores, equipos y competiciones de diferentes ligas.">
<meta name="twitter:image" content="https://www.hockey5.pro<?php echo asset_url('assets/'); ?>img/logo_h5.png">

</head>

<body class="hoqueigrid">

<?php include __DIR__ . '/../includes/header.php'; ?>


<div class="layout-main">


  <div class="content-col" style="min-width:0;">
<main class="hg-wrap">
  <div class="hg-card">
    <h1 class="hg-title">
      <span class="accent">HOCKEY</span>5 GRID
    </h1>

    <div class="hg-grid">
      <div class="hg-preview">
        <div class="hg-preview-box hg-preview-box--fancy">
          <span class="hg-preview-glow"></span>
          <img
            src="<?php echo asset_url('assets/'); ?>img/logo_grid.png"
            class="hg-preview-img"
            alt="Hockey5 Grid">
        </div>
      </div>

      <div class="hg-text">
        <p>
          <?php echo __('grid.intro.text'); ?>
        </p>

        <ul>
          <li><?php echo __('grid.intro.li1'); ?></li>
          <li><?php echo __('grid.intro.li2'); ?></li>
        </ul>

        <div class="hg-subtitle"><?php echo __('grid.menu.select_league'); ?></div>
        <div class="hg-pills" data-group="league">
          <div class="hg-pill <?php echo $league === 'oklliga' ? 'active' : ''; ?>" data-value="oklliga" data-label="OK Lliga">OK Lliga</div>
          <div class="hg-pill <?php echo $league === 'placard' ? 'active' : ''; ?>" data-value="placard" data-label="Placard">Placard</div>
          <div class="hg-pill <?php echo $league === 'internacional' ? 'active' : ''; ?>" data-value="internacional" data-label="Internacional">Internacional</div>
        </div>

        <div class="hg-actions">
          <button class="hg-start" id="hgStart" type="button"><?php echo __('grid.menu.start_btn'); ?></button>
        </div>
      </div>
    </div>
  </div>
</main>

</main>

  </div>

</div>

  <!-- INSTRUCCIONES ESPECÍFICAS POR LIGA (NUEVO DISEÑO) -->
  <?php
    $instructions = [
      'oklliga' => [
          'title' => __('grid.instructions.okliga.title'),
        'intro' => __('grid.instructions.okliga.intro'),
        'steps' => [
          __('grid.instructions.okliga.step1'),
          __('grid.instructions.okliga.step2'),
          __('grid.instructions.okliga.step3'),
          __('grid.instructions.okliga.step4')
        ]
      ],
      'placard' => [
        'title' => __('grid.instructions.placard.title'),
        'intro' => __('grid.instructions.placard.intro'),
        'steps' => [
          __('grid.instructions.placard.step1'),
          __('grid.instructions.placard.step2'),
          __('grid.instructions.placard.step3'),
          __('grid.instructions.placard.step4')
        ]
      ],
      'internacional' => [
        'title' => __('grid.instructions.int.title'),
        'intro' => __('grid.instructions.int.intro'),
        'steps' => [
          __('grid.instructions.int.step1'),
          __('grid.instructions.int.step2'),
          __('grid.instructions.int.step3'),
          __('grid.instructions.int.step4')
        ]
      ]
    ];
    $currentInstr = $instructions[$league] ?? $instructions['internacional'];
  ?>

  <section class="howto-container">
    <div class="howto-inner">
      <h2 class="howto-title"><?= $currentInstr['title'] // Allow HTML spans ?></h2>
      
      <p class="howto-text">
        <?= $currentInstr['intro'] ?>
      </p>

      <div class="howto-steps">
        <?php foreach ($currentInstr['steps'] as $index => $step): ?>
          <div class="howto-step">
            <div class="howto-step-icon"><?= $index + 1 ?></div>
            <div class="howto-step-content"><?= $step ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    
    </div>
  </section>





<script>
  function activatePill(groupEl, pillEl){
    groupEl.querySelectorAll('.hg-pill').forEach(p => p.classList.remove('active'));
    pillEl.classList.add('active');
  }

  document.querySelectorAll('.hg-pills').forEach(groupEl => {
    groupEl.addEventListener('click', (e) => {
      const pill = e.target.closest('.hg-pill');
      if (!pill) return;
      activatePill(groupEl, pill);
    });
  });

  document.getElementById('hgStart').addEventListener('click', () => {
    const active = document.querySelector('[data-group="league"] .hg-pill.active');
    const val = (active && active.dataset.value) ? active.dataset.value : 'oklliga';

    // Map internal code to new URL slug
    const slugs = {
      'oklliga': 'ok-liga',
      'placard': 'liga-placard',
      'internacional': 'internacional'
    };
    const slug = slugs[val] || 'ok-liga';

    // Use "pretty" per-league URLs (no direct .php access)
    // Dynamic base path based on current language
    const baseUrl = "<?php echo rtrim(url(''), '/'); ?>";
    window.location.href = baseUrl + '/grid/' + encodeURIComponent(slug);
  });
</script>


<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
