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
<html lang="<?= current_lang() ?>">
<head>
    <?php include __DIR__ . '/../includes/ads.php'; ?>
  <?php include __DIR__ . '/../includes/analytics.php'; ?>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Hockey5 - <?php echo __('guess.title'); ?></title>
  <meta name="description" content="<?= htmlspecialchars(__('guess.seo.desc'), ENT_QUOTES, 'UTF-8') ?>">
  <link rel="stylesheet" href="<?php echo asset_url('assets/'); ?>styles.css"/>
  <link rel="stylesheet" href="<?php echo asset_url('assets/'); ?>hoqueigrid.css"/>



  <link rel="canonical" href="https://www.hockey5.pro/guess">
  <link rel="icon" href="/favicon.ico" sizes="any">
  <link rel="icon" type="image/png" href="<?php echo asset_url('assets/'); ?>img/favicon-48.png" sizes="48x48">
  <link rel="icon" type="image/png" href="<?php echo asset_url('assets/'); ?>img/favicon-192.png" sizes="192x192">
  <link rel="apple-touch-icon" href="<?php echo asset_url('assets/'); ?>img/favicon-192.png">
</head>

<body class="hoqueigrid">

<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="layout-main">

  <div class="content-col" style="min-width:0;">

<main class="hg-wrap">
  <div class="hg-card">
    <h1 class="hg-title">
      <span class="accent">HOCKEY</span>5 <?php echo str_replace('HOCKEY5 ', '', mb_strtoupper(__('guess.hero_title'))); ?>
    </h1>

    <div class="hg-grid">
      <div class="hg-preview">
        <div class="hg-preview-box hg-preview-box--fancy">
          <span class="hg-preview-glow"></span>
          <img
            src="<?php echo asset_url('assets/'); ?>img/logo_guess.png" 
            class="hg-preview-img"
            alt="Hockey5 <?php echo __('guess.title'); ?>">
        </div>
      </div>

      <div class="hg-text">
        <p>
          <?php echo __('guess.intro.text'); ?>
        </p>
        <div class="hg-subtitle"><?php echo __('guess.menu.select_league'); ?></div>
        <div class="hg-pills" data-group="league">
          <div class="hg-pill <?php echo $league === 'oklliga' ? 'active' : ''; ?>" data-value="oklliga" data-label="OK Lliga">OK Lliga</div>
          <div class="hg-pill <?php echo $league === 'placard' ? 'active' : ''; ?>" data-value="placard" data-label="Placard">Placard</div>
          <div class="hg-pill <?php echo $league === 'internacional' ? 'active' : ''; ?>" data-value="internacional" data-label="Internacional">Internacional</div>
        </div>

        <div class="hg-actions">
          <button class="hg-start ga-track" id="guessStart" type="button"
                  data-ga-action="start_game" data-ga-category="game_setup" data-ga-label="guess"><?php echo __('guess.menu.start_btn'); ?></button>
        </div>
      </div>
    </div>
  </div>
 </main>

  </div>

</div>

  <!-- INSTRUCCIONES ESPECÍFICAS POR LIGA (Using Wordle instructions as placeholder/template for now or guess specific if available) -->
  <?php
    // Reuse specific instructions if they exist, or generic
    $instructions = [
      'oklliga' => [
        'title' => __('guess.instructions.okliga.title'),
        'intro' => __('guess.instructions.okliga.intro'),
        'steps' => [
          __('guess.instructions.okliga.step1'),
          __('guess.instructions.okliga.step2'),
          __('guess.instructions.okliga.step3')
        ]
      ],
      'placard' => [
        'title' => __('guess.instructions.placard.title'),
        'intro' => __('guess.instructions.placard.intro'),
        'steps' => [
          __('guess.instructions.placard.step1'),
          __('guess.instructions.placard.step2'),
          __('guess.instructions.placard.step3')
        ]
      ],
      'internacional' => [
        'title' => __('guess.instructions.int.title'),
        'intro' => __('guess.instructions.int.intro'),
        'steps' => [
          __('guess.instructions.int.step1'),
          __('guess.instructions.int.step2'),
          __('guess.instructions.int.step3')
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

  document.getElementById('guessStart')?.addEventListener('click', (e) => {
    const active = document.querySelector('.hg-pills[data-group="league"] .hg-pill.active');
    const val = (active && active.dataset.value) ? active.dataset.value : 'internacional';
    
    // GA: Update league attribute before it's tracked (analytics.php listens to click)
    e.currentTarget.setAttribute('data-ga-league', val);

    // Map internal code to new URL slug
    const slugs = {
      'oklliga': 'ok-liga',
      'placard': 'placard', // Note: guess game uses 'placard', unlike grid which uses 'liga-placard'
      'internacional': 'internacional'
    };
    const slug = slugs[val] || 'internacional';

    const baseUrl = "<?php echo rtrim(url(''), '/'); ?>";
    window.location.href = baseUrl + '/guess/' + encodeURIComponent(slug);
  });
</script>


<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
