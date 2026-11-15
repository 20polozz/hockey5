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
  <title>Hockey5 - <?php echo __('clubs.title'); ?></title>
  <meta name="description" content="<?= htmlspecialchars(__('clubs.seo.desc'), ENT_QUOTES, 'UTF-8') ?>">
  <link rel="stylesheet" href="<?php echo asset_url('assets/'); ?>styles.css"/>
  <link rel="stylesheet" href="<?php echo asset_url('assets/'); ?>hoqueigrid.css"/>



  <link rel="canonical" href="https://www.hockey5.pro/clubs">
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
      <span class="accent">HOCKEY</span>5 CLUBS
    </h1>

    <div class="hg-grid">
      <div class="hg-preview">
        <div class="hg-preview-box hg-preview-box--fancy">
          <span class="hg-preview-glow"></span>
          <img
            src="<?php echo asset_url('assets/'); ?>img/logo_clubs.png" 
            class="hg-preview-img"
            alt="Hockey5 <?php echo __('clubs.title'); ?>">
        </div>
      </div>

      <div class="hg-text">
        <p>
          <?php echo __('clubs.menu.desc'); ?>
        </p>
        <div class="hg-subtitle"><?php echo __('clubs.menu.select'); ?></div>
        <div class="hg-pills" data-group="league">
          <div class="hg-pill <?php echo $league === 'oklliga' ? 'active' : ''; ?>" data-value="oklliga" data-label="OK Lliga">OK Lliga</div>
          <div class="hg-pill <?php echo $league === 'placard' ? 'active' : ''; ?>" data-value="placard" data-label="Placard">Placard</div>
          <div class="hg-pill <?php echo $league === 'internacional' ? 'active' : ''; ?>" data-value="internacional" data-label="Internacional">Internacional</div>
        </div>

        <div class="hg-actions">
          <button class="hg-start ga-track" id="clubsStart" type="button"
                  data-ga-action="start_game" data-ga-category="game_setup" data-ga-label="clubs"><?php echo __('guess.menu.start_btn'); ?></button>
        </div>
      </div>
    </div>
  </div>
 </main>

  </div>

</div>

  <!-- INSTRUCCIONES ESPECÍFICAS POR LIGA -->
  <?php
    // Generic instructions for now, can be specific later
    $instructions = [
      'oklliga' => [
        'title' => '¿Cómo jugar a <span>Clubs - OK Liga</span>?',
        'intro' => 'Adivina qué equipo de la OK Liga se esconde tras el quinteto inicial. 1 Portero, 2 Defensas, 2 Delanteros.',
        'steps' => [
          'Selecciona un jugador de la OK Liga para colocar en la pista.',
          'Si el jugador pertenece al equipo oculto, se marcará en verde.',
          'Si no pertenece, se marcará en rojo.',
          '¡Completa el quinteto para ganar!'
        ]
      ],
      'placard' => [
        'title' => '¿Cómo jugar a <span>Clubs - Placard</span>?',
        'intro' => 'El mismo desafío, pero con los equipos de la Liga Placard portuguesa.',
        'steps' => [
          'Busca jugadores de la liga portuguesa.',
          'Intenta adivinar la alineación del equipo secreto.',
          'Usa tus conocimientos sobre plantillas actuales.',
          '¡Tienes que acertar los 5 jugadores!'
        ]
      ],
      'internacional' => [
        'title' => '¿Cómo jugar a <span>Clubs - Internacional</span>?',
        'intro' => 'El reto global. Equipos de cualquier liga importante.',
        'steps' => [
          'Puede ser cualquier equipo internacional.',
          'Empieza probando con jugadores conocidos.',
          'Si aciertas uno, ¡deduce el resto del equipo!',
          '¡Demuestra que eres un experto mundial!'
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

  document.getElementById('clubsStart')?.addEventListener('click', (e) => {
    const active = document.querySelector('.hg-pills[data-group="league"] .hg-pill.active');
    const val = (active && active.dataset.value) ? active.dataset.value : 'internacional';
    
    // GA: Update league attribute
    e.currentTarget.setAttribute('data-ga-league', val);

    // Map internal code to new URL slug
    const slugs = {
      'oklliga': 'ok-liga',
      'placard': 'liga-placard', // Note: clubs uses 'liga-placard' in routes
      'internacional': 'internacional'
    };
    const slug = slugs[val] || 'internacional';

    const baseUrl = "<?php echo rtrim(url(''), '/'); ?>";
    window.location.href = baseUrl + '/clubs/' + encodeURIComponent(slug);
  });
</script>


<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
