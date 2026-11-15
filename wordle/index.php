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

  if ($message === "") {
    $report_error = __('contact.error.required');
  } else {
    $safeMessage = strip_tags($message);
    $subject = 'HOCKEY5 WORDLE - Report d\'error';
    $body =
      "Indica aquí l'error:\n" . $safeMessage . "\n\n" .
      "Liga: " . ($_GET['league'] ?? 'unknown') . "\n" .
      "IP: " . $ip . "\n" .
      "User-Agent: " . $userAgent . "\n";

    $from = "noreply@hockey5.pro";
    $headers  = "From: Hockey5 Wordle <{$from}>\r\n";
    $headers .= "Reply-To: {$from}\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

    if (mail($to, $subject, $body, $headers, "-f {$from}")) {
      $report_sent = true;
    } else {
      $report_error = __('contact.error.send');
    }
  }
}

$league = (string)($_GET['league'] ?? 'oklliga');
$allowedLeagues = ['oklliga', 'placard', 'internacional'];
if (!in_array($league, $allowedLeagues, true)) $league = 'oklliga';

$leagueLabel = match ($league) {
    'oklliga'       => __('wordle.league.oklliga'),
    'placard'       => __('wordle.league.placard'),
    'internacional' => __('wordle.league.internacional'),
    default         => __('wordle.league.oklliga'),
};

// Absolute base URL for API calls from any nested route.
// Base path (supports hosting the project in a subfolder like /hockey5)
$basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$basePath = rtrim($basePath, '/');

// Fix: When accessing physical wrapper files (e.g. /wordle/ok-liga/index.php),
// SCRIPT_NAME includes the subdirectories. We need to strip them to find the project root.
$knownLeagues = ['/ok-liga', '/liga-placard', '/internacional'];
foreach ($knownLeagues as $slug) {
    if (substr($basePath, -strlen($slug)) === $slug) {
        $basePath = substr($basePath, 0, -strlen($slug));
    }
}
// Strip game directory if present to get to root (assets are in /assets)
if (substr($basePath, -7) === '/wordle') {
    $basePath = substr($basePath, 0, -7);
}

if ($basePath === '.' || $basePath === '/') {
  $basePath = '';
}

$apiBase = $basePath . '/api';
?>
<?php
$seoDesc = __('wordle.seo.desc');
if ($league === 'oklliga') {
    $seoDesc = __('wordle.seo.okliga');
} elseif ($league === 'placard') {
    $seoDesc = __('wordle.seo.placard');
} elseif ($league === 'internacional') {
    $seoDesc = __('wordle.seo.int');
}
?>
<!doctype html>
<html lang="<?= current_lang() ?>">
<head>
  <?php include __DIR__ . '/../includes/analytics.php'; ?>
  <meta charset="utf-8"/>
  <?php include __DIR__ . '/../includes/ads.php'; ?>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Hockey-5 Wordle | <?php echo htmlspecialchars($leagueLabel, ENT_QUOTES, 'UTF-8'); ?> - <?php echo __('wordle.daily_game'); ?></title>
  <meta name="description" content="<?php echo htmlspecialchars($seoDesc, ENT_QUOTES, 'UTF-8'); ?>">

  <meta property="og:type" content="website">
  <meta property="og:title" content="Hockey-5 Wordle | <?php echo htmlspecialchars($leagueLabel, ENT_QUOTES, 'UTF-8'); ?>">
  <meta property="og:description" content="<?php echo htmlspecialchars($seoDesc, ENT_QUOTES, 'UTF-8'); ?>">
  <meta property="og:image" content="https://www.hockey5.pro/assets/img/wordle.jpg"> <!-- Using wordle.jpg as it seems more appropriate for OG -->
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="Hockey-5 Wordle | <?php echo htmlspecialchars($leagueLabel, ENT_QUOTES, 'UTF-8'); ?>">
  <meta name="twitter:description" content="<?php echo htmlspecialchars($seoDesc, ENT_QUOTES, 'UTF-8'); ?>">
  <meta name="twitter:image" content="https://www.hockey5.pro/assets/img/wordle.jpg">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8'); ?>/assets/styles.css"/>
  <link rel="stylesheet" href="<?php echo htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8'); ?>/assets/wordle.css"/>
  <link rel="icon" href="<?php echo htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8'); ?>/favicon.ico" sizes="any">

  <style>
    @media (max-width: 768px){
      body.wordle-page .sn_ad_label{ display:none !important; }
      body.wordle-page .layout-ads .left,
      body.wordle-page .layout-ads .right{ display:none !important; }
    }
  </style>
</head>

<body class="wordle-page">

<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="layout-main">

  <!-- CONTENIDO -->
  <div class="content-col" style="min-width:0;">

<main class="w-wrap">
  <h1 class="hero-title"><span class="hero-accent">HOCKEY</span>5 WORDLE</h1>

  <div id="toast" class="toast" aria-live="polite"></div>

  <section class="w-board" id="board" aria-label="Tablero"></section>

  <section class="w-keyboard" id="keyboard" aria-label="Teclado"></section>

  <section class="w-footer">
    <button class="w-btn ga-track" id="giveUpBtn" type="button"
            data-ga-action="give_up" data-ga-category="game" data-ga-label="wordle"><?= __('wordle.controls.give_up') ?></button>
  </section>

  <!-- Result popup (win/lose) removed - now generated dynamically in JS -->
</main>

</div>

</div>

  <!-- REPORT ROW + MODAL -->
  <?php if ($report_sent): ?>
    <div class="flash ok" style="text-align:center;margin:10px auto;max-width:520px;">✅ <?= htmlspecialchars(__('contact.success')) ?></div>
  <?php elseif ($report_error !== ""): ?>
    <div class="flash err" style="text-align:center;margin:10px auto;max-width:520px;">❌ <?= htmlspecialchars($report_error) ?></div>
  <?php endif; ?>

  <div id="wReportModal" style="position:fixed;inset:0;background:rgba(0,0,0,.55);display:none;align-items:center;justify-content:center;z-index:10001;padding:18px;">
    <div style="width:min(560px,92vw);background:#ffffff;color:#0b0f14;border-radius:18px;box-shadow:0 22px 60px rgba(0,0,0,.55);position:relative;padding:22px 22px 18px;text-align:left;">
      <button id="wReportClose" type="button" style="position:absolute;right:14px;top:12px;width:38px;height:38px;border-radius:999px;border:0;background:#e11b2a;color:#fff;font-weight:900;font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:0 10px 20px rgba(0,0,0,.25);">X</button>
      <div style="margin:6px 44px 8px 0;font-weight:900;font-size:18px;"><?= __('grid.report.title') ?></div>
      <p style="margin:0 44px 14px 0;opacity:.8;font-weight:650;font-size:13px;"><?= __('grid.report.desc') ?></p>
      <form method="post">
        <label style="font-weight:900;margin:10px 0 6px;display:block;"><?= __('grid.report.label') ?></label>
        <textarea name="report_message" style="width:100%;min-height:120px;border-radius:12px;border:0;padding:12px;background:#0b1a24;color:rgba(255,255,255,.92);outline:none;resize:vertical;font-weight:700;" placeholder="<?= __('grid.report.placeholder') ?>"></textarea>
        <button type="submit" style="margin-top:12px;padding:10px 16px;border-radius:12px;border:0;background:#ffb400;color:#001018;font-weight:950;cursor:pointer;"><?= __('grid.report.submit') ?></button>
      </form>
    </div>
  </div>

<script>
    window.H5_WORDLE = {
      league: "<?php echo $league; ?>",
      apiBase: "<?php echo $basePath; ?>/api",
      baseUrl: "<?php echo $basePath; ?>",
      tr: {
        toast_complete: "<?php echo __('wordle.js.toast.complete'); ?>",
        toast_invalid: "<?php echo __('wordle.js.toast.invalid'); ?>",
        toast_correct: "<?php echo __('wordle.js.toast.correct'); ?>",
        toast_error: "<?php echo __('wordle.js.toast.error'); ?>",
        win_title_win: "<?php echo __('wordle.js.win.title_win'); ?>",
        win_title_lose: "<?php echo __('wordle.js.win.title_lose'); ?>",
        win_desc_win: "<?php echo __('wordle.js.win.desc_win'); ?>",
        win_desc_lose: "<?php echo __('wordle.js.win.desc_lose'); ?>",
        win_desc_lose_unknown: "<?php echo __('wordle.js.win.desc_lose_unknown'); ?>",
        btn_home: "<?php echo __('wordle.js.btn.home'); ?>",
        btn_play_again: "<?php echo __('wordle.js.btn.play_again'); ?>",
        toast_copied: "<?php echo __('wordle.js.toast.copied'); ?>",
        toast_init_error: "<?php echo __('wordle.js.toast.init_error'); ?>",
        giveup_confirm: "<?php echo __('wordle.giveup.confirm'); ?>",
        giveup_yes: "<?php echo __('wordle.giveup.yes'); ?>",
        giveup_no: "<?php echo __('wordle.giveup.no'); ?>",
        report_label: "<?php echo __('grid.controls.report'); ?>",
        report_btn: "<?php echo __('grid.controls.report_btn'); ?>",
      }
    };
  </script>
<script src="<?php echo htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8'); ?>/assets/wordle.js" defer></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('wReportModal');
    var closeBtn = document.getElementById('wReportClose');
    if (closeBtn) closeBtn.addEventListener('click', function() { modal.style.display = 'none'; });
    if (modal) modal.addEventListener('click', function(e) { if (e.target === modal) modal.style.display = 'none'; });
  });
</script>

  <!-- INSTRUCCIONES ESPECÍFICAS POR LIGA (NUEVO DISEÑO) -->
  <?php
    $instructions = [
      'oklliga' => [
        'title' => __('wordle.instructions.okliga.title'),
        'intro' => __('wordle.instructions.okliga.intro'),
        'steps' => [
          __('wordle.instructions.okliga.step1'),
          __('wordle.instructions.okliga.step2'),
          __('wordle.instructions.okliga.step3'),
          __('wordle.instructions.okliga.step4'),
          __('wordle.instructions.okliga.step5')
        ]
      ],
      'placard' => [
        'title' => __('wordle.instructions.placard.title'),
        'intro' => __('wordle.instructions.placard.intro'),
        'steps' => [
          __('wordle.instructions.placard.step1'),
          __('wordle.instructions.placard.step2'),
          __('wordle.instructions.placard.step3')
        ]
      ],
      'internacional' => [
        'title' => __('wordle.instructions.int.title'),
        'intro' => __('wordle.instructions.int.intro'),
        'steps' => [
          __('wordle.instructions.int.step1'),
          __('wordle.instructions.int.step2'),
          __('wordle.instructions.int.step3')
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
</body>
</html>
