<?php
// --- REPORT FORM HANDLER (envía email) ---
$report_sent = false;
$report_error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["report_message"])) {
  $to = "soporte@hockey5.pro";

  $message = trim((string)($_POST["report_message"] ?? ""));
  $gridCols = trim((string)($_POST["grid_cols"] ?? ""));
  $gridRows = trim((string)($_POST["grid_rows"] ?? ""));
  $userAgent = $_SERVER["HTTP_USER_AGENT"] ?? "";
  $ip = $_SERVER["REMOTE_ADDR"] ?? "";

  if ($message === "") {
    $report_error = __('contact.error.required');
  } else {
    // Sanitización básica
    $safeMessage = strip_tags($message);
    $safeCols = strip_tags($gridCols);
    $safeRows = strip_tags($gridRows);

    $subject = __('report.subject');
    $body =
      __('report.body_prefix') . $safeMessage . "\n\n" .
      "Grid (Columnas): " . $safeCols . "\n" .
      "Grid (Filas): " . $safeRows . "\n\n" .
      "IP: " . $ip . "\n" .
      "User-Agent: " . $userAgent . "\n";

    // ---- FIX MAIL ----
    $from = "noreply@hockey5.pro";

    $headers  = "From: Hockey5 Grid <{$from}>\r\n";
    $headers .= "Reply-To: {$from}\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

    if (mail($to, $subject, $body, $headers, "-f {$from}")) {
      $report_sent = true;
    } else {
      $report_error = __('contact.error.send');
    }
    // ------------------
  }
}

// --- SELECTOR DE LIGA ---
$league = $_GET["league"] ?? "";

// IMPORTANTE: ahora el juego se activa para OKLLIGA / PLACARD / INTERNACIONAL
$show_game = in_array($league, ["oklliga", "placard", "internacional"], true) || ($_SERVER["REQUEST_METHOD"] === "POST");

// Calculate base path for absolute asset URLs
$basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$basePath = rtrim($basePath, '/');

// Fix: When accessing physical wrapper files (e.g. /hockeygrid/ok-liga/index.php),
// SCRIPT_NAME includes the subdirectories. We need to strip them.
$knownLeagues = ['/ok-liga', '/liga-placard', '/internacional'];
foreach ($knownLeagues as $slug) {
    if (substr($basePath, -strlen($slug)) === $slug) {
        $basePath = substr($basePath, 0, -strlen($slug));
    }
}
// Strip game directory (hockeygrid is 11 chars)
if (substr($basePath, -11) === '/hockeygrid') {
    $basePath = substr($basePath, 0, -11);
}

if ($basePath === '.' || $basePath === '/') {
  $basePath = '';
}

// Config que leerá el JS (hockeygrid.js)
$cfg = [
  "league"    => $league,
  // IMPORTANT: absolute path for JSONs so it works from subfolders like /grid/ok-liga/
  "jsonDir"   => $basePath . "/hockeygrid/espana", 
  "teamsFile" => "oklliga_teams.json", 
  // PHP API base
  "apiBase"   => $basePath . "/api",
  // Where to send users back when they exit / finish the game
  "homeUrl"   => url('home'),
  "tr" => [
      'error_not_enough_data' => __('grid.js.error.not_enough_data'),
      'status_no_place_for' => __('grid.js.status.no_place_for'),
      'status_correct_count' => __('grid.js.status.correct_count'),
      'win_title' => __('grid.js.win.title'),
      'win_desc' => __('grid.js.win.desc'),
      'btn_home' => __('grid.js.btn.home'),
      'btn_play_again' => __('grid.js.btn.play_again'),
      'toast_limit' => __('grid.js.toast.limit'),
      'toast_limit_desc' => __('grid.js.toast.limit_desc'),
      'toast_no_valid_pos' => __('grid.js.toast.no_valid_pos'),
      'toast_no_place' => __('grid.js.toast.no_place'),
      'toast_invalid_player' => __('grid.js.toast.invalid_player'),
      'toast_not_exist' => __('grid.js.toast.not_exist'),
      'status_solution' => __('grid.js.status.solution'),
  ]
];

if (($league === 'placard') || ($league === 'internacional')) {
    $cfg["jsonDir"]   = $basePath . "/hockeygrid/portugal";
    $cfg["teamsFile"] = "placard_teams.json"; 
}
if ($league === 'internacional') {
    $cfg["jsonDir"] = $basePath . "/hockeygrid/internacional";
    $cfg["teamsFile"] = "";
}
?>

<?php
// SEO Dinámico
$seoTitle = __('grid.seo.title');
$seoDesc  = __('grid.seo.desc');

if ($league === 'oklliga') {
    $seoTitle = __('grid.seo.okliga.title');
    $seoDesc  = __('grid.seo.okliga.desc');
} elseif ($league === 'placard') {
    $seoTitle = __('grid.seo.placard.title');
    $seoDesc  = __('grid.seo.placard.desc');
} elseif ($league === 'internacional') {
    $seoTitle = __('grid.seo.int.title');
    $seoDesc  = __('grid.seo.int.desc');
}
?>
<!DOCTYPE html>
<html lang="es">
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title><?= htmlspecialchars($seoTitle) ?></title>
  <link rel="stylesheet" href="/assets/styles.css">
  <link rel="stylesheet" href="/hockeygrid/assets/style.css">
  <meta name="description" content="<?= htmlspecialchars($seoDesc) ?>">

  <?php include __DIR__ . '/../includes/analytics.php'; ?>
  <?php include __DIR__ . '/../includes/ads.php'; ?>

  <meta property="og:type" content="website">
  <meta property="og:title" content="<?= htmlspecialchars($seoTitle) ?>">
  <meta property="og:description" content="<?= htmlspecialchars($seoDesc) ?>">
  <meta property="og:image" content="https://www.hockey5.pro/assets/img/logo_grid.png">
  <meta name="twitter:card" content="summary_large_image">

  <style>
    /* Estilos internos para el juego */
    #game-wrap { padding-bottom: 0px !important; }
    .end-actions{
      display:none;
      flex-direction: column;
      align-items: center;
      margin-top: 4px;
    }
    .end-actions.show{ display:flex; }

    .primary-btn, .report-btn{
      padding: 10px 18px;
      border-radius: 12px;
      border: 0;
      background: rgba(23, 200, 234, .85);
      color: #001018;
      font-weight: 900;
      cursor: pointer;
      box-shadow: 0 16px 40px rgba(0, 0, 0, .35);
      transition: filter 0.2s;
    }
    .primary-btn:hover, .report-btn:hover{ filter: brightness(.97); }

    .report-row{
      margin-top: 16px;
      display:flex;
      gap: 12px;
      justify-content:center;
      align-items:center;
      color: rgba(255,255,255,.90);
      font-weight: 750;
      font-size: 14px;
    }

    /* --- Estilo del modal --- */
    .modal-overlay{
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,.55);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 1000;
      padding: 18px;
    }
    .modal-overlay.show{ display:flex; }

    .modal-card{
      width: min(560px, 92vw);
      background: #ffffff;
      color: #0b0f14;
      border-radius: 18px;
      box-shadow: 0 22px 60px rgba(0, 0, 0, .55);
      position: relative;
      padding: 30px; /* More padding as in screenshot */
      text-align: left;
      border: none;
    }

    .modal-close{
      position: absolute;
      right: -15px;
      top: -15px;
      width: 42px;
      height: 42px;
      border-radius: 999px;
      border: 3px solid #fff;
      background: #e11b2a;
      color: #fff;
      font-weight: 900;
      font-size: 20px;
      cursor: pointer;
      display:flex;
      align-items:center;
      justify-content:center;
      box-shadow: 0 10px 25px rgba(0,0,0,.35);
      transition: transform 0.2s;
    }
    .modal-close:hover { transform: scale(1.1); }

    .modal-title{
      margin: 0 0 10px;
      font-weight: 900;
      font-size: 22px;
      color: #0b0f14;
      line-height: 1.2;
    }
    .modal-sub{
      margin: 0 0 20px;
      opacity: .8;
      font-weight: 700;
      font-size: 14px;
      color: #0b0f14;
    }

    .modal-label{
      font-weight: 900;
      margin: 20px 0 10px;
      display:block;
      font-size: 18px;
      color: #0b0f14;
    }

    .modal-textarea{
      width: 100%;
      min-height: 150px;
      border-radius: 12px;
      border: 0;
      padding: 15px;
      background: #0b1a24;
      color: rgba(255,255,255,.95);
      outline: none;
      resize: none;
      font-weight: 600;
      font-size: 15px;
      margin-bottom: 20px;
    }
    .modal-textarea::placeholder { color: rgba(255,255,255,.3); }

    .modal-submit{
      padding: 12px 30px;
      border-radius: 12px;
      border: 0;
      background: #ffb400;
      color: #001018;
      font-weight: 900;
      font-size: 18px;
      cursor:pointer;
      box-shadow: 0 10px 20px rgba(255, 180, 0, 0.2);
      transition: transform 0.2s, background 0.2s;
    }
    .modal-submit:hover {
      transform: translateY(-2px);
      background: #ffc400;
    }

    .flash{
      margin: 10px auto 0;
      width: min(560px, 92vw);
      padding: 10px 12px;
      border-radius: 12px;
      text-align:center;
      font-weight: 800;
      transition: opacity .25s ease, transform .25s ease;
    }
    .flash.hide{ opacity: 0; transform: translateY(-6px); pointer-events:none; }
    .flash.ok{ background: rgba(0,255,120,.16); border: 1px solid rgba(0,255,120,.45); }
    .flash.err{ background: rgba(255,60,60,.16); border: 1px solid rgba(255,60,60,.45); }

    /* Cuando la solución está mostrada, ocultamos controles */
    .controls.hidden{ display:none; }

    /* Ajuste: dejamos aire bajo la barra para que no “pegue” el título */
    .page { padding-top: 18px; }
  </style>

<link rel="canonical" href="https://www.hockey5.pro/jugar/hockey5-grid">
<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" type="image/png" href="/assets/img/favicon-48.png" sizes="48x48">
<link rel="icon" type="image/png" href="/assets/img/favicon-192.png" sizes="192x192">
<link rel="apple-touch-icon" href="/assets/img/favicon-192.png">

</head>

<body>

  <!-- TOP BAR (global header) -->
  <?php include __DIR__ . '/../includes/header.php'; ?>

  <!-- MENÚ/JUEGO -->
  <div class="layout-main">

    <!-- CONTENIDO (menú + juego) -->
    <div class="content-col">
  <!-- MENÚ DE LIGA -->
  <div id="league-menu" class="page league-menu" style="<?= $show_game ? 'display:none;' : '' ?>">
    <?php
      $intActive = ($league === "internacional");
      $okActive  = ($league === "oklliga");
      $plActive  = ($league === "placard");

      $intClass = $intActive ? "toggle-btn active" : "toggle-btn";
      $okClass  = $okActive  ? "toggle-btn active" : "toggle-btn";
      $plClass  = $plActive  ? "toggle-btn active" : "toggle-btn";

      // Si venimos con ?league=... marcamos el botón START con esa liga
      $selected = $league ?: "internacional";
    ?>

    <section class="menu-shell" aria-label="Menú de inicio">
      <div class="menu-inner">
        <!-- Vista previa/imagen (izquierda) -->
        <div class="menu-right" aria-hidden="true">
          <div class="menu-preview">
            <div class="preview-badge">
              <div class="pb-top">3x3</div>
              <div class="pb-sub">GRID</div>
            </div>
            <div class="preview-lines"></div>
          </div>
        </div>

        <!-- Contenido (derecha) -->
        <div class="menu-left">
          <h1 class="menu-title"><span class="menu-accent">hockey</span>5 <span class="menu-white"><?= __('grid.menu.title') ?></span></h1>
          <p class="menu-desc">
            <?= __('grid.menu.desc') ?>
          </p>

          <div class="menu-section"><?= __('grid.menu.select') ?></div>
          <div class="toggle-row" role="group" aria-label="<?= __('grid.menu.select') ?>">
            <!-- Updated to link directly to pages -->
            <a class="<?= $intActive ? 'toggle-btn active' : 'toggle-btn' ?>" href="<?= url('grid/internacional') ?>">Internacional</a>
            <a class="<?= $okActive  ? 'toggle-btn active' : 'toggle-btn' ?>" href="<?= url('grid/ok-liga') ?>">OK Liga</a>
            <a class="<?= $plActive  ? 'toggle-btn active' : 'toggle-btn' ?>" href="<?= url('grid/liga-placard') ?>">Liga Placard</a>
          </div>

          <!-- Start button now just closes the menu if we are already on the correct page, 
               or is hidden/replaced? 
               Actually, since we navigate on click, we might not need the "Start Game" button OR 
               it can just link to the current page's game view (which is handled by $show_game).
               But the prompt says: "esos accesos deben pasar a navegar a las nuevas rutas".
          -->
          <a id="startLeague" class="start-btn" href="#"><?= __('grid.menu.start') ?></a>
        </div>
      </div>
    </section>
  </div>


    <main id="game-wrap" class="page" style="<?= $show_game ? '' : 'display:none;' ?>">
        <h1 class="hero-title"><span class="hero-accent">HOCKEY</span>5 GRID</h1>
    
        <section class="board-wrap">
          <div id="hockey-grid-root"></div>
        </section>
    
        <?php if ($report_sent): ?>
          <div class="flash ok">✅ <?php echo __('contact.success'); ?></div>
        <?php elseif ($report_error !== ""): ?>
          <div class="flash err">❌ <?= htmlspecialchars($report_error) ?></div>
        <?php endif; ?>

        <script>
          // Mostra el missatge uns segons i desapareix (sense redirigir).
          (function(){
            const flash = document.querySelector('.flash');
            if (!flash) return;
            const DURATION_MS = 4000; // 3-5s aprox.
            setTimeout(() => {
              flash.classList.add('hide');
              setTimeout(() => flash.remove(), 350);
            }, DURATION_MS);
          })();
        </script>
    
        <!-- CONTROLES -->
        <section id="controls" class="controls">
          <div class="answerbar">
            <div class="input-row">
              <input id="player-input" type="text" placeholder="<?= __('grid.controls.placeholder') ?>" autocomplete="off" />
              <button id="giveup" type="button" class="square-btn ga-track" aria-label="<?= __('grid.controls.give_up') ?>"
                      data-ga-action="give_up" data-ga-category="game" data-ga-label="grid">🏳️</button>
            </div>
    
            <div id="suggestions" class="suggestions" aria-label="Sugerencias"></div>
            <div id="status" class="status"></div>
          </div>
        </section>
    
  </main>

  <!-- End Actions (Moved here, very bottom) -->
  <div id="endActions" class="end-actions">
      <button class="primary-btn ga-track" id="playAgain" type="button"
              data-ga-action="play_again" data-ga-category="game" data-ga-label="grid"><?= __('grid.controls.play_again') ?></button>
      <div class="report-row">
          <span><?= __('grid.controls.report') ?></span>
          <button class="report-btn ga-track" id="openReport" type="button"
                  data-ga-action="report_open" data-ga-category="feedback" data-ga-label="grid"><?= __('grid.controls.report_btn') ?></button>
      </div>
  </div>

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

  <?php include __DIR__ . '/../includes/footer.php'; ?>

  <!-- MODAL RENDIRSE -->
  <div id="giveupModal" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="giveupTitle">
    <div class="modal-card">
      <button id="giveupClose" class="modal-close" type="button" aria-label="<?= __('grid.close') ?>">X</button>
      <div id="giveupTitle" class="modal-title"><?= __('grid.giveup.confirm') ?></div>
      <div class="modal-actions">
        <button id="giveupYes" class="modal-btn" type="button"><?= __('grid.giveup.yes') ?></button>
        <button id="giveupNo" class="modal-btn" type="button"><?= __('grid.giveup.no') ?></button>
      </div>
    </div>
  </div>

  <!-- MODAL REPORTAR -->
  <div id="reportModal" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="reportTitle">
    <div class="modal-card">
      <button id="reportClose" class="modal-close" type="button" aria-label="<?= __('grid.close') ?>">X</button>
      <div id="reportTitle" class="modal-title"><?= __('grid.report.title') ?></div>
      <p class="modal-sub"><?= __('grid.report.desc') ?></p>

      <form method="post">
        <label class="modal-label" for="report_message"><?= __('grid.report.label') ?></label>
        <textarea id="report_message" name="report_message" class="modal-textarea" placeholder="<?= __('grid.report.placeholder') ?>"></textarea>

        <input type="hidden" id="grid_cols" name="grid_cols" value="">
        <input type="hidden" id="grid_rows" name="grid_rows" value="">

        <button class="modal-submit" type="submit"><?= __('grid.report.submit') ?></button>
      </form>
    </div>
  </div>

  <!-- Inyectamos config antes de cargar el JS del juego -->
  <script>
    window.HOCKEYGRID_CONFIG = <?php echo json_encode($cfg, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
  </script>

  <!-- Menú (selector de liga) con estética tipo “Impostor” -->
  <script>
    (function(){
      // New logic: "Start Game" just hides the menu because we are already on the correct page
      // (The league buttons navigate to the page).
      const menu = document.getElementById('league-menu');
      const startBtn = document.getElementById('startLeague');
      const gameWrap = document.getElementById('game-wrap');
      
      if(startBtn && menu && gameWrap) {
        startBtn.addEventListener('click', (e) => {
           e.preventDefault();
           // Hide menu, show game
           menu.style.display = 'none';
           gameWrap.style.display = 'block';
           // Optionally, we could trigger a reload if needed, but existing logic might be fine
           // if the PHP already rendered the game config based on the URL.
        });
      }
    })();
  </script>

  <script src="/hockeygrid/assets/hockeygrid.js"></script>

  <script>
    (function(){
      const controls = document.getElementById("controls");
      const endActions = document.getElementById("endActions");

      const giveupModal = document.getElementById("giveupModal");
      const giveupBtn = document.getElementById("giveup");
      const giveupClose = document.getElementById("giveupClose");
      const giveupYes = document.getElementById("giveupYes");
      const giveupNo = document.getElementById("giveupNo");

      function open(el){ el.classList.add("show"); }
      function close(el){ el.classList.remove("show"); }

      giveupBtn.addEventListener("click", () => open(giveupModal));
      giveupClose.addEventListener("click", () => close(giveupModal));
      giveupNo.addEventListener("click", () => close(giveupModal));
      giveupModal.addEventListener("click", (e) => { if (e.target === giveupModal) close(giveupModal); });

      giveupYes.addEventListener("click", () => {
        close(giveupModal);
        if (typeof window.revealSolutions === "function") window.revealSolutions();
        controls.classList.add("hidden");
        endActions.classList.add("show");
      });

      document.getElementById("playAgain").addEventListener("click", () => {
        if (typeof window.clearGridState === "function") window.clearGridState();
        window.location.reload();
      });

      const reportModal = document.getElementById("reportModal");
      const openReport = document.getElementById("openReport");
      const reportClose = document.getElementById("reportClose");

      openReport.addEventListener("click", () => {
        const cols = (window.getGridCols && window.getGridCols()) || [];
        const rows = (window.getGridRows && window.getGridRows()) || [];
        document.getElementById("grid_cols").value = cols.join(" | ");
        document.getElementById("grid_rows").value = rows.join(" | ");

        open(reportModal);
      });

      reportClose.addEventListener("click", () => close(reportModal));
      reportModal.addEventListener("click", (e) => { if (e.target === reportModal) close(reportModal); });

      document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") { close(giveupModal); close(reportModal); }
      });

    })();
  </script>


</body>
</html>
