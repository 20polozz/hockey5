<?php
// --- REPORT FORM HANDLER ---
$report_sent = false;
$report_error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["report_message"])) {
  $to = "soporte@hockey5.pro";

  $message = trim((string)($_POST["report_message"] ?? ""));
  $gridCols = trim((string)($_POST["grid_cols"] ?? ""));
  $gridRows = trim((string)($_POST["grid_rows"] ?? ""));
  $userAgent = $_SERVER["HTTP_USER_AGENT"] ?? "";
  $ip = $_SERVER["REMOTE_ADDR"] ?? "";

  if ($message !== "") {
    $safeMessage = strip_tags($message);
    $safeCols = strip_tags($gridCols);
    $safeRows = strip_tags($gridRows);

    $subject = "Reporte Juego Clubs - Hockey5";
    $body =
      "Indica aquí el error:\n" . $safeMessage . "\n\n" .
      "IP: " . $ip . "\n" .
      "User-Agent: " . $userAgent . "\n";

    $from = "noreply@hockey5.pro";
    $headers  = "From: Hockey5 Clubs <{$from}>\r\n";
    $headers .= "Reply-To: {$from}\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

    if (mail($to, $subject, $body, $headers, "-f {$from}")) {
      $report_sent = true;
    }
  }
}
// --- SELECTOR DE LIGA ---
$league = $_GET["league"] ?? "";
// The game is active if a valid league is selected
$show_game = in_array($league, ["ok-liga", "liga-placard", "internacional"], true);

// --- BASE PATH CALCULATION (Like HockeyGrid/Wordle) ---
$basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$basePath = rtrim($basePath, '/');

// Fix: When accessing physical wrapper files (e.g. /clubs/ok-liga/index.php),
// SCRIPT_NAME includes the subdirectories. We need to strip them.
$knownLeagues = ['/ok-liga', '/liga-placard', '/internacional'];
foreach ($knownLeagues as $slug) {
    if (substr($basePath, -strlen($slug)) === $slug) {
        $basePath = substr($basePath, 0, -strlen($slug));
    }
}
// Strip game directory (clubs is 5 chars) if present
if (substr($basePath, -6) === '/clubs') { // "/clubs" is 6 chars
    $basePath = substr($basePath, 0, -6);
}

if ($basePath === '.' || $basePath === '/') {
    $basePath = '';
}

// Config for JS
$cfg = [
    "league"    => $league,
    "apiBase"   => $basePath . "/api", // Points to /api
    "baseUrl"   => $basePath,
    "tr" => [
        'errorLoad' => __('clubs.js.error_load'),
        'errorSlot' => __('clubs.js.error_slot'),
        'roleGK' => __('clubs.role.por'),
        'roleDEF' => __('clubs.role.def'),
        'roleFWD' => __('clubs.role.del'),
        'winTitle' => __('clubs.win.title'),
        'winDesc' => __('clubs.win.desc'),
        'confirmGiveup' => __('clubs.confirm_giveup'),
        'alreadySelected' => __('clubs.js.already_selected'),
        'notInSquad' => __('clubs.js.not_in_squad'),
        'btnHome' => __('grid.js.btn.home'),
        'btnPlayAgain' => __('grid.js.btn.play_again'),
    ]
];

// Instructions logic
$instructions = [
    'ok-liga' => [
        'title' => __('clubs.instructions.okliga.title'),
        'intro' => __('clubs.instructions.okliga.intro'),
        'steps' => [
            __('clubs.instructions.okliga.step1'),
            __('clubs.instructions.okliga.step2'),
            __('clubs.instructions.okliga.step3'),
            __('clubs.instructions.okliga.step4'),
        ]
    ],
    'liga-placard' => [
        'title' => __('clubs.instructions.placard.title'),
        'intro' => __('clubs.instructions.placard.intro'),
        'steps' => [
            __('clubs.instructions.placard.step1'),
            __('clubs.instructions.placard.step2'),
            __('clubs.instructions.placard.step3'),
            __('clubs.instructions.placard.step4'),
        ]
    ],
    'internacional' => [
        'title' => __('clubs.instructions.int.title'),
        'intro' => __('clubs.instructions.int.intro'),
        'steps' => [
            __('clubs.instructions.int.step1'),
            __('clubs.instructions.int.step2'),
            __('clubs.instructions.int.step3'),
            __('clubs.instructions.int.step4'),
        ]
    ]
];
$currentInstr = $instructions[$league] ?? $instructions['internacional'];

// SEO
$seoTitle = __('clubs.seo.title');
$seoDesc  = __('clubs.seo.desc');

if ($league === 'ok-liga') {
    $seoTitle = __('clubs.seo.okliga.title');
    $seoDesc  = __('clubs.seo.okliga.desc');
} elseif ($league === 'liga-placard') {
    $seoTitle = __('clubs.seo.placard.title');
    $seoDesc  = __('clubs.seo.placard.desc');
} elseif ($league === 'internacional') {
    $seoTitle = __('clubs.seo.int.title');
    $seoDesc  = __('clubs.seo.int.desc');
}
?>
<!DOCTYPE html>
<html lang="<?= current_lang() ?>">
<head>
    <?php include __DIR__ . '/../includes/ads.php'; ?>
    <?php include __DIR__ . '/../includes/analytics.php'; ?>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($seoTitle) ?></title>
    <meta name="description" content="<?= htmlspecialchars($seoDesc) ?>">

    <link rel="stylesheet" href="<?= $basePath ?>/assets/styles.css">
    <link rel="stylesheet" href="<?= $basePath ?>/clubs/assets/style.css">
    
    <!-- Favicon -->
    <link rel="icon" href="<?= $basePath ?>/favicon.ico" sizes="any">
    
    <!-- OG Tags -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= htmlspecialchars($seoTitle) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($seoDesc) ?>">
    <meta property="og:image" content="https://www.hockey5.pro/assets/img/logo_clubs.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($seoTitle) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($seoDesc) ?>">
    <meta name="twitter:image" content="https://www.hockey5.pro/assets/img/logo_clubs.png">
</head>
<body class="clubs-page">

    <?php include __DIR__ . '/../includes/header.php'; ?>

    <div class="layout-main">
        
        <!-- GAME AREA (Visible by default now) -->
        <div class="content-col" style="min-width:0; min-height: 100vh;">
            <?php if ($report_sent): ?>
                <div class="toast show" style="position: static; margin: 20px auto; opacity: 1; pointer-events: auto;">
                    <div class="t-title"><?= __('guess.js.report_sent_title') ?></div>
                    <div class="t-msg"><?= __('guess.js.report_sent_msg') ?></div>
                </div>
            <?php endif; ?>
            <h1 class="hero-title"><span class="hero-accent">HOCKEY</span>5 CLUBS</h1>

            <div id="game-wrap" class="game-wrap">
            
                <div id="game-container" class="game-container">
                    

                                    
                    <!-- PITCH AREA (Slots) -->
                    <div class="pitch-area">
                        <!-- 1 GK (Bottom Center) -->
                        <div class="player-slot" data-index="0" data-role="<?= __('clubs.role.por') ?>" style="bottom: 8%; left: 50%;">
                            <div class="slot-label"><?= __('clubs.role.por') ?></div>
                        </div>

                        <!-- 2 DEF (Middle-Low) -->
                        <div class="player-slot" data-index="1" data-role="<?= __('clubs.role.def') ?>" style="bottom: 30%; left: 25%;">
                            <div class="slot-label"><?= __('clubs.role.def') ?></div>
                        </div>
                        <div class="player-slot" data-index="2" data-role="<?= __('clubs.role.def') ?>" style="bottom: 30%; left: 75%;">
                            <div class="slot-label"><?= __('clubs.role.def') ?></div>
                        </div>

                        <!-- 2 FW (Top) -->
                        <div class="player-slot" data-index="3" data-role="<?= __('clubs.role.del') ?>" style="top: 20%; left: 25%;">
                            <div class="slot-label"><?= __('clubs.role.del') ?></div>
                        </div>
                        <div class="player-slot" data-index="4" data-role="<?= __('clubs.role.del') ?>" style="top: 20%; left: 75%;">
                            <div class="slot-label"><?= __('clubs.role.del') ?></div>
                        </div>
                    </div>

                    <!-- BOTTOM CONTROLS -->
                    <div class="bottom-bar">
                        <!-- Team Card (Left) -->
                        <div id="team-card" class="team-card">
                            <div class="team-logo-wrapper">
                                <img id="team-logo" src="" alt="Team Logo" style="display:none;">
                                <div class="team-logo-placeholder" style="display:block;">?</div>
                            </div>
                            <div class="team-details">
                                <h2 id="team-name"><?= __('clubs.select_team') ?></h2>
                            </div>
                        </div>

                        <!-- Input Area (Right) -->
                        <div class="controls-area">
                            <div class="input-wrapper">
                                <input type="text" id="player-input" placeholder="<?= __('clubs.placeholder') ?>" autocomplete="off">
                                <div id="suggestions" class="suggestions"></div>
                            </div>
                            <div id="giveup" class="flag-btn" title="<?= __('clubs.give_up') ?>">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path>
                                    <line x1="4" y1="22" x2="4" y2="15"></line>
                                </svg>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- End Actions (Moved here, bottom of content-col) -->
            <div id="endActions" class="end-actions">
                <button class="play-again-btn" id="playAgainBtn"><?= __('clubs.btn.play_again') ?></button>
                <div class="report-row">
                    <span><?= __('grid.controls.report') ?></span>
                    <button class="report-button" id="openReportBtn"><?= __('grid.controls.report_btn') ?></button>
                </div>
            </div>

        </div> <!-- end content-col -->
    </div> <!-- end layout-main -->



    <!-- Instructions (Footer) -->
    <section class="howto-container">
        <div class="howto-inner">
            <h2 class="howto-title"><?= $currentInstr['title'] ?></h2>
            <p class="howto-text"><?= $currentInstr['intro'] ?></p>

            <?php if (!empty($currentInstr['steps'])): ?>
                <div class="howto-steps">
                    <?php foreach ($currentInstr['steps'] as $index => $step): ?>
                        <div class="howto-step">
                            <div class="howto-step-icon"><?= $index + 1 ?></div>
                            <div class="howto-step-content"><?= $step ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>


    <!-- MODAL RENDIRSE (Standardized) -->
    <div id="giveupModal" class="modal-overlay" role="dialog" aria-modal="true">
        <div class="modal-card">
            <button id="giveupClose" class="modal-close" type="button">X</button>
            <div class="modal-title"><?= __('clubs.giveup.confirm') ?></div>
            <div class="modal-actions">
                <button id="giveupYes" class="modal-btn" type="button"><?= __('clubs.giveup.yes') ?></button>
                <button id="giveupNo" class="modal-btn" type="button"><?= __('clubs.giveup.no') ?></button>
            </div>
        </div>
    </div>

    <!-- MODAL REPORTAR (Standardized structure) -->
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

                <button id="reportSubmit" class="modal-submit" type="submit"><?= __('grid.report.submit') ?></button>
            </form>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

    <!-- INJECT CONFIG -->
    <script>
        window.H5_CLUBS = <?= json_encode($cfg, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    </script>
    <!-- APP SCRIPT -->
    <script src="<?= $basePath ?>/clubs/assets/script.js"></script>

</body>
</html>
