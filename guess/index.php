<?php
declare(strict_types=1);

// --- REPORT FORM HANDLER ---
$report_sent = false;
$report_error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["report_message"])) {
  $to = "soporte@hockey5.pro";

  $message = trim((string)($_POST["report_message"] ?? ""));
  $userAgent = $_SERVER["HTTP_USER_AGENT"] ?? "";
  $ip = $_SERVER["REMOTE_ADDR"] ?? "";

  if ($message !== "") {
    $safeMessage = strip_tags($message);
    $subject = "Reporte Juego Adivina - Hockey5";
    $body =
      "Indica aquí el error:\n" . $safeMessage . "\n\n" .
      "IP: " . $ip . "\n" .
      "User-Agent: " . $userAgent . "\n";

    $from = "noreply@hockey5.pro";
    $headers  = "From: Hockey5 Adivina <{$from}>\r\n";
    $headers .= "Reply-To: {$from}\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

    if (mail($to, $subject, $body, $headers, "-f {$from}")) {
      $report_sent = true;
    }
  }
}

$currentLeague = $GLOBALS['CURRENT_ROUTE_PARAMS'][0] ?? 'ok-liga';
?>
<!doctype html>
<html lang="<?php echo current_lang(); ?>">
<head>
  <?php include __DIR__ . '/../includes/analytics.php'; ?>
  <meta charset="utf-8"/>
  <?php include __DIR__ . '/../includes/ads.php'; ?>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Hockey5 | <?php echo __('guess.title'); ?></title>
  <meta name="description" content="<?php echo __('guess.seo.desc'); ?>">
  
  <!-- STYLES -->
  <link rel="stylesheet" href="<?php echo asset_url('assets/'); ?>styles.css"/>
  <link rel="stylesheet" href="<?php echo asset_url('hockeygrid/assets/'); ?>style.css"/>
  <!-- Game Specific Styles -->
  <link rel="stylesheet" href="<?php echo asset_url('guess/css/'); ?>styles.css"/>

  <style>
    /* Base Layout Fixes */
    .guess-layout {
        display: flex;
        flex-direction: column;
        background-color: #121212; 
        color: #fff;
        min-height: 100vh;
    }
    
    .guess-content {
        display: flex;
        flex-direction: column;
        padding-top: 24px;
        padding-bottom: 4px;
        align-items: center;
        width: 100%;
        box-sizing: border-box; 
        min-height: 100vh;
    }

    .game-area {
        width: 100%;
        max-width: 900px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 20px;
        padding: 0 10px;
    }

    .controls {
        width: 100%;
        max-width: 600px;
        position: relative;
        z-index: 50;
    }
    
    .answerbar {
        position: relative;
        width: 100%;
    }

    /* Styles moved to css/styles.css */

    /* End Actions style removed because it conflicts with styles.css */

    /* Center Inputs */
    /* Handled in styles.css now */
  </style>
</head>
<body class="guess-layout">

<?php include __DIR__ . '/../includes/header.php'; ?>

<main class="guess-content page">
  
  <!-- Toast Container -->
  <div id="toast" class="toast">
      <div class="t-title"></div>
      <div class="t-msg"></div>
  </div>

  <!-- Custom Give Up Modal -->
  <div id="giveup-modal" class="modal-overlay">
      <div class="modal-card">
          <button id="modal-close" class="modal-close">X</button>
          <div class="modal-title"><?php echo __('guess.giveup.confirm'); ?></div>
          <div class="modal-actions">
              <button id="modal-yes" class="modal-btn"><?php echo __('guess.giveup.yes'); ?></button>
              <button id="modal-no" class="modal-btn"><?php echo __('guess.giveup.no'); ?></button>
          </div>
      </div>
  </div>

  <!-- Report Modal -->
  <!-- MODAL REPORTAR (Standardized structure) -->
  <div id="reportModal" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="reportTitle">
      <div class="modal-card">
          <button id="reportClose" class="modal-close" type="button" aria-label="<?= __('grid.close') ?>">X</button>
          <div id="reportTitle" class="modal-title"><?= __('guess.report.title') ?></div>
          <p class="modal-sub"><?= __('guess.report.desc') ?></p>

          <form method="post">
              <label class="modal-label" for="report_message"><?= __('grid.report.label') ?></label>
              <textarea id="report_message" name="report_message" class="modal-textarea" placeholder="<?= __('grid.report.placeholder') ?>"></textarea>

              <input type="hidden" id="grid_cols" name="grid_cols" value="">
              <input type="hidden" id="grid_rows" name="grid_rows" value="">

              <button id="reportSubmit" class="modal-submit" type="submit"><?= __('guess.report.submit') ?></button>
          </form>
      </div>
  </div>

  <h1 class="hero-title"><span class="hero-accent">HOCKEY</span>5 <?php echo str_replace('HOCKEY5 ', '', __('guess.hero_title')); ?></h1>

  <!-- Messages -->
  <div id="game-status" style="position: sticky; top: 0; z-index: 100; margin-bottom: 20px; font-weight: 900; color: #fff; text-shadow: 0 2px 4px rgba(0,0,0,0.8); text-align: center; width: 100%;"></div>

  <div class="game-area">
      <!-- Input Container -->
      <section class="controls">
        <div class="answerbar">
          <div class="input-row">
            <input id="player-input" type="text" placeholder="<?php echo __('guess.placeholder'); ?>" autocomplete="off" />
            <button id="btn-giveup" type="button" class="square-btn" title="<?php echo __('guess.btn.giveup'); ?>">🏳️</button>
          </div>
          <div id="suggestions" class="suggestions"></div>
        </div>
      </section>

      <!-- Headers -->
      <div class="headers-row">
          <div><?php echo __('guess.col.player'); ?></div>
          <div><?php echo __('guess.col.country'); ?></div>
          <div><?php echo __('guess.col.year'); ?></div>
          <div><?php echo __('guess.col.team'); ?></div>
          <div><?php echo __('guess.col.number'); ?></div>
      </div>

      <!-- Guesses Grid -->
      <div id="guesses-container" class="guesses-container">
          <!-- Rows injected here -->
      </div>
      
      <!-- End Actions (Moved here, inside game-area) -->
      <section id="end-actions" class="end-actions" style="display:none;">
          <button id="btn-restart" class="primary-btn"><?php echo __('guess.btn.restart'); ?></button>
          
          <div class="report-row">
              <span><?php echo __('guess.report.question'); ?></span>
              <button id="btn-open-report" class="report-btn"><?php echo __('guess.report.btn'); ?></button>
          </div>
      </section>

      </div>
    </main>




  <!-- INSTRUCCIONES ESPECÍFICAS POR LIGA -->
  <?php
    $leagueMap = [
      'ok-liga' => 'okliga',
      'placard' => 'placard',
      'internacional' => 'int',
    ];
    $instrKey = $leagueMap[$currentLeague] ?? 'okliga';

    $instructions = [
      'okliga' => [
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
      'int' => [
        'title' => __('guess.instructions.int.title'),
        'intro' => __('guess.instructions.int.intro'),
        'steps' => [
          __('guess.instructions.int.step1'),
          __('guess.instructions.int.step2'),
          __('guess.instructions.int.step3')
        ]
      ]
    ];
    $currentInstr = $instructions[$instrKey] ?? $instructions['okliga'];
  ?>

  <section class="howto-container">
    <div class="howto-inner">
      <h2 class="howto-title"><?= $currentInstr['title'] ?></h2>
      
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

<!-- Config Injection -->
<script>
  window.GUESS_CONFIG = {
    league: "<?php echo $currentLeague; ?>",
    // Fetch players from the database API
    jsonUrl: "<?php echo asset_url('api/guess_players.php'); ?>",
    tr: {
      placeholder: "<?php echo __('guess.placeholder'); ?>",
      giveup: "<?php echo __('guess.btn.giveup'); ?>",
      restart: "<?php echo __('guess.btn.restart'); ?>",
      confirm_giveup: "<?php echo __('guess.msg.confirm_giveup'); ?>",
      error_loading: "<?php echo __('guess.js.error_loading'); ?>",
      no_players: "<?php echo __('guess.js.no_players'); ?>",
      error_init: "<?php echo __('guess.js.error_init'); ?>",
      already_selected: "<?php echo __('guess.js.already_selected'); ?>",
      already_tried: "<?php echo __('guess.js.already_tried'); ?>",
      correct: "<?php echo __('guess.js.correct'); ?>",
      no_results: "<?php echo __('guess.js.no_results'); ?>",
      report_error_empty: "<?php echo __('guess.js.report_error_empty'); ?>",
      report_subject: "<?php echo __('guess.js.report_subject'); ?>",
      report_sent_title: "<?php echo __('guess.js.report_sent_title'); ?>",
      report_sent_msg: "<?php echo __('guess.js.report_sent_msg'); ?>",
      msg_win: "<?php echo __('guess.msg.win'); ?>",
      msg_lose: "<?php echo __('guess.msg.lose'); ?>",
      btn_home: "<?php echo __('grid.js.btn.home'); ?>",
      btn_play_again: "<?php echo __('grid.js.btn.play_again'); ?>",
      win_title: "<?php echo __('grid.js.win.title'); ?>",
      win_desc: "<?php echo __('guess.msg.win'); ?>" // Reuse existing win msg or add specific
    }
  };
</script>

<!-- New Modular Source -->
<script type="module" src="<?php echo asset_url('guess/js/main.js'); ?>"></script>

</body>
</html>
