<?php
// includes/breadcrumbs.php
$crumbs = $GLOBALS['breadcrumbs'] ?? [];
if (!empty($crumbs)):
?>
<nav aria-label="breadcrumb" style="padding: 10px 16px; max-width: 900px; margin: 0 auto; margin-top: 10px; background: rgba(0,0,0,0.2); border-radius: 8px;">
  <ol style="list-style: none; padding: 5px 10px; margin: 0; display: flex; flex-wrap: wrap; font-size: 0.9em; opacity: 0.9; align-items: center;">
    <li style="margin-right: 8px;">
      <a href="<?php echo url('home'); ?>" style="text-decoration: none; color: inherit; opacity: 0.7; transition: opacity 0.2s;"><?php echo __('menu.home'); ?></a>
    </li>
    <?php foreach ($crumbs as $i => $crumb): ?>
        <li style="margin-right: 8px; opacity: 0.5;"> &rsaquo; </li>
        <li style="margin-right: 8px;">
            <?php if (isset($crumb['url']) && $i < count($crumbs) - 1): ?>
                <a href="<?php echo htmlspecialchars($crumb['url']); ?>" style="text-decoration: none; color: inherit; opacity: 0.7; transition: opacity 0.2s;"><?php echo htmlspecialchars($crumb['label']); ?></a>
            <?php else: ?>
                <span aria-current="page" style="font-weight: 600; color: #fff;"><?php echo htmlspecialchars($crumb['label']); ?></span>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
  </ol>
</nav>
<?php endif; ?>
