<?php
$year = date('Y');
?>
<!doctype html>
<html lang="es">
<head>
    <?php include __DIR__ . '/../includes/ads.php'; ?>
  <?php include __DIR__ . '/../includes/analytics.php'; ?>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>404 | Hockey-5</title>
  <link rel="stylesheet" href="/assets/styles.css"/>
</head>
<body>
  <main class="container" style="text-align:center; padding: 60px 20px;">
    <h1 style="font-size: 3rem; margin-bottom: 20px; color: #e11b2a;">404</h1>
    <h2 style="font-size: 1.5rem; margin-bottom: 20px;">¡Vaya! Te has ido fuera de pista.</h2>
    <p style="max-width: 600px; margin: 0 auto 30px; line-height: 1.6; opacity: 0.9;">
      Parece que la página que buscas no existe o ha sido movida. Al igual que en un partido, a veces un pase largo no llega a su destino.
      Pero no te preocupes, puedes volver al juego rápidamente.
    </p>

    <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
      <a href="<?php echo url('home'); ?>" class="btn" style="background: #f5c400; color: #000; font-weight: bold; text-decoration: none; padding: 10px 20px; border-radius: 8px;">Ir al Inicio</a>
      <a href="<?php echo url('hockeygrid_menu'); ?>" class="btn" style="background: #17c8ea; color: #000; font-weight: bold; text-decoration: none; padding: 10px 20px; border-radius: 8px;">Jugar a Grid</a>
      <a href="<?php echo url('contact'); ?>" class="btn" style="border: 1px solid #fff; color: #fff; text-decoration: none; padding: 10px 20px; border-radius: 8px;">Contactar</a>
    </div>

    <div style="margin-top: 50px; opacity: 0.6; font-size: 0.9rem;">
      <p>Si crees que esto es un error, por favor avísanos.</p>
    </div>
  </main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
