<?php
declare(strict_types=1);
// Expects $currentLeague to be set by the including file
$currentLeague = $currentLeague ?? 'ok-liga'; 
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
    /* Base Layout Fixes (keep here or move to styles.css, keeping here for critical render) */
    .guess-layout {
        display: flex;
        flex-direction: column;
        background-color: #121212; 
        color: #fff;
    }
    
    .guess-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        justify-content: flex-start;
        padding-top: 80px; 
        padding-bottom: 40px;
        align-items: center;
        width: 100%;
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

    /* Headers & Rows Grid */
    .headers-row {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 2fr 1fr;
        gap: 10px;
        width: 100%;
        max-width: 800px;
        margin-bottom: 5px;
        padding: 0 10px;
        text-align: center;
        color: rgba(255,255,255,0.5);
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .guess-row {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 2fr 1fr;
        gap: 10px;
    }

    /* Cells */
    .guess-cell {
        background: #2a2a2a;
        border-radius: 50px; 
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
        padding: 10px 5px;
        text-align: center;
        border: 2px solid #3a3a3a;
        box-shadow: 0 4px 6px rgba(0,0,0,0.3);
        position: relative;
        overflow: hidden;
        min-height: 50px;
        word-break: break-word; /* Important for long names */
        line-height: 1.2;
    }

    .cell-pill {
        border-radius: 12px;
        justify-content: flex-start;
        padding-left: 15px;
        text-align: left;
    }
    
    /* Feedback Colors */
    .status-correct { background-color: #1f8f3a !important; border-color: #2ecc71 !important; }
    .status-close { background-color: #e67e22 !important; border-color: #f39c12 !important; }
    .status-wrong { background-color: #2a2a2a; border-color: #3a3a3a; }

    /* Mobile */
    @media (max-width: 600px) {
        .headers-row, .guess-row {
            gap: 5px;
            grid-template-columns: 1.5fr 1fr 1fr 1.5fr 0.8fr; 
        }
        .guess-cell {
            font-size: 0.7rem;
            min-height: 40px;
            padding: 5px;
        }
        .cell-pill { padding-left: 8px; }
    }

    /* Modals */
    .modal-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,.75); display: none;
        align-items: center; justify-content: center; z-index: 2000;
        backdrop-filter: blur(4px);
    }
    .modal-overlay.show { display: flex; }
    .modal-card {
        background: #1e1e1e; color: #fff; border-radius: 16px; 
        padding: 24px; text-align: center; max-width: 90%; border: 1px solid #444;
    }
    .modal-close {
        background: #e53935; color: white; border:none; border-radius:50%; width:30px; height:30px;
        position: absolute; top:10px; right:10px; font-weight:bold; cursor:pointer;
    }
  </style>
</head>
<body class="guess-layout">

<?php include __DIR__ . '/../includes/header.php'; ?>

<main class="guess-content page">
  
  <h1 class="hero-title"><span class="hero-accent">HOCKEY</span>5 ADIVINA EL JUGADOR</h1>

  <!-- Messages -->
  <div id="game-status" style="position: sticky; top: 0; z-index: 100; margin-bottom: 20px; font-weight: 900; color: #fff; text-shadow: 0 2px 4px rgba(0,0,0,0.8);"></div>

  <div class="game-area">
      <!-- Input Container -->
      <section class="controls">
        <div class="answerbar">
          <div class="input-row">
            <input id="player-input" type="text" placeholder="<?php echo __('guess.placeholder'); ?>" autocomplete="off" />
            <button id="btn-giveup" type="button" class="square-btn ga-track" 
                    style="background: #e53935; border-color: #ef5350;" 
                    title="<?php echo __('guess.btn.giveup'); ?>"
                    data-ga-action="give_up" data-ga-category="game" data-ga-label="guess" data-ga-league="<?php echo $currentLeague; ?>">🏳️</button>
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
          <div>#</div>
      </div>

      <!-- Guesses Grid -->
      <div id="guesses-container" class="guesses-container">
          <!-- Rows injected here -->
      </div>
      
      <!-- End Actions -->
      <section id="end-actions" class="end-actions" style="display:none; text-align:center; padding: 20px;">
        <button id="btn-restart" class="primary-btn ga-track"
                data-ga-action="restart" data-ga-category="game" data-ga-label="guess" data-ga-league="<?php echo $currentLeague; ?>"><?php echo __('guess.btn.restart'); ?></button>
      </section>
  </div>

</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<!-- Config Injection -->
<script>
  window.GUESS_CONFIG = {
    league: "<?php echo $currentLeague; ?>",
    // Link to the static JSON file
    jsonUrl: "<?php echo asset_url('guess/data/players.json'); ?>",
    tr: {
      placeholder: "<?php echo __('guess.placeholder'); ?>",
      giveup: "<?php echo __('guess.btn.giveup'); ?>",
      restart: "<?php echo __('guess.btn.restart'); ?>",
      confirm_giveup: "<?php echo __('guess.msg.confirm_giveup'); ?>"
    }
  };
</script>

<!-- New Modular Source -->
<script type="module" src="<?php echo asset_url('guess/js/main.js'); ?>"></script>

</body>
</html>
