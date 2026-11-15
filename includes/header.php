<header class="top">
  <div class="top-inner">
<nav class="main-nav">
      <a href="<?php echo url('home'); ?>"><?php echo __('menu.home'); ?></a>
      
      <div class="lang-dropdown">
        <span class="dropdown-toggle" style="font-weight:600; color:var(--text); cursor:pointer; padding:6px 0; font-size:15px; opacity:0.9;"><?php echo __('menu.games'); ?></span>
        <div class="lang-menu" style="top: 100%; right: unset; left: 0; padding:10px; width:max-content; border:1px solid #333; border-radius:8px;">
          <a href="<?php echo url('hockeygrid_menu'); ?>"><?php echo __('menu.grid'); ?></a>
          <a href="<?php echo url('wordle_menu'); ?>"><?php echo __('menu.wordle'); ?></a>
          <a href="<?php echo url('guess_menu'); ?>"><?php echo __('guess.title'); ?></a>
          <a href="<?php echo url('clubs_menu'); ?>"><?php echo __('clubs.title'); ?></a>
        </div>
      </div>

      <div class="lang-dropdown">
        <span class="dropdown-toggle" style="font-weight:600; color:var(--text); cursor:pointer; padding:6px 0; font-size:15px; opacity:0.9;"><?php echo __('menu.howto'); ?></span>
        <div class="lang-menu" style="top: 100%; right: unset; left: 0; padding:10px; width:max-content; border:1px solid #333; border-radius:8px;">
          <a href="<?php echo url('grid_howto'); ?>"><?php echo __('menu.grid'); ?></a>
          <a href="<?php echo url('wordle_howto'); ?>"><?php echo __('menu.wordle'); ?></a>
          <a href="<?php echo url('guess_howto'); ?>"><?php echo __('guess.title'); ?></a>
        </div>
      </div>
    </nav>

    <a class="top-brand" href="<?php echo url('home'); ?>" aria-label="<?php echo __('header.title'); ?>">
      <span class="top-brand-text">HOCKEY<span class="top-brand-accent">5</span></span>
    </a>

    <div class="top-social">
      <div class="lang-dropdown">
        <button class="icon-btn lang-btn" aria-label="<?php echo __('menu.language'); ?>">
          <?php echo strtoupper(current_lang()); ?>
        </button>
        <div class="lang-menu">
          <?php foreach(['es', 'ca', 'en', 'fr', 'pt'] as $l): ?>
            <?php if ($l === current_lang()) continue; ?>
             <a href="<?php echo get_url_for_lang($l); ?>">
                <?php echo strtoupper($l); ?>
             </a>
           <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</header>
