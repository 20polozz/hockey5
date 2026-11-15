<footer class="site-footer">
  <div class="footer-inner">
    <!-- Col 1: Brand -->
    <div class="footer-col branding-col">
       <a href="<?php echo get_url_for_lang(current_lang()); ?>" class="brand" style="text-decoration:none;">
          <span class="brand-text">HOCKEY<span class="brand-accent">5</span></span>
       </a>
       <p class="footer-desc">
         <?php echo __('footer.desc'); ?>
       </p>
       <p class="footer-copyright">
         <?php echo sprintf(__('footer.copyright'), date("Y")); ?>
       </p>
    </div>

    <!-- Col 2: Games -->
    <div class="footer-col links-col">
      <h4 class="footer-title"><?php echo __('footer.games'); ?></h4>
      <ul class="footer-links">
        <li><a href="<?php echo url('hockeygrid_menu'); ?>"><?php echo __('menu.grid'); ?></a></li>
        <li><a href="<?php echo url('wordle_menu'); ?>"><?php echo __('menu.wordle'); ?></a></li>
        <li><a href="<?php echo url('guess_menu'); ?>"><?php echo __('guess.title'); ?></a></li>

      </ul>
    </div>

    <!-- Col 3: Legal & Info -->
    <div class="footer-col links-col">
      <h4 class="footer-title"><?php echo __('footer.legal'); ?></h4>
      <ul class="footer-links">
        <li><a href="<?php echo url('about'); ?>"><?php echo __('menu.about'); ?></a></li>
        <li><a href="<?php echo url('contact'); ?>"><?php echo __('menu.contact'); ?></a></li>
        <li><a href="<?php echo url('privacy'); ?>"><?php echo __('menu.privacy'); ?></a></li>
        <li><a href="<?php echo url('cookies'); ?>"><?php echo __('menu.cookies'); ?></a></li>
        <li><a href="<?php echo url('ads'); ?>"><?php echo __('menu.ads'); ?></a></li>
        <li><a href="<?php echo url('terms'); ?>"><?php echo __('menu.terms'); ?></a></li>
        <li><a href="<?php echo url('faq'); ?>"><?php echo __('menu.faq'); ?></a></li>
        <li><a href="<?php echo url('legal'); ?>"><?php echo __('menu.disclaimer'); ?></a></li>
      </ul>
    </div>

    <!-- Col 4: Social -->
    <div class="footer-col social-col">
      <h4 class="footer-title"><?php echo __('footer.follow'); ?></h4>
      <div class="footer-socials">
         <a href="#" class="social-icon" aria-label="Instagram">
           <img src="<?php echo asset_url('assets/'); ?>img/logo_ig.png" alt="Instagram">
         </a>
         <a href="#" class="social-icon" aria-label="X">
           <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M18.8 2H22l-7.2 8.2L23.3 22h-6.7l-5.3-6.9L5 22H1.8l7.8-8.9L.7 2h6.9l4.8 6.2L18.8 2Zm-1.2 18h1.8L6.3 3.9H4.4L17.6 20Z" fill="currentColor"/></svg>
         </a>
      </div>
      <div class="footer-email">
        <a href="mailto:soporte@hockey5.pro">soporte@hockey5.pro</a>
      </div>
    </div>
  </div>


</footer>
