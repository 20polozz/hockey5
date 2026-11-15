<?php
declare(strict_types=1);

// pages/contacto.php
require_once __DIR__ . '/../includes/i18n.php';

$mensajeStatus = "";
// Process form logic...
$nombre = $email = $asunto = $mensaje = "";
$errorMsg = "";

function clean($value) {
  return trim(strip_tags($value));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

  $nombre  = clean($_POST["nombre"] ?? "");
  $email   = clean($_POST["email"] ?? "");
  $asunto  = clean($_POST["asunto"] ?? "");
  $mensaje = clean($_POST["mensaje"] ?? "");
  $privacy = isset($_POST["privacy"]);

    if ($nombre === "" || $email === "" || $asunto === "" || $mensaje === "") {
    $errorMsg = __('contact.error.required');
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errorMsg = __('contact.error.email');
  } elseif (!$privacy) {
    $errorMsg = __('contact.error.privacy');
  } else {

    $nombre = str_replace(["\r", "\n"], " ", $nombre);
    $email  = str_replace(["\r", "\n"], " ", $email);
    $asunto = str_replace(["\r", "\n"], " ", $asunto);

    $subject = "Contacto web: " . $asunto;

    $body  = "Nuevo mensaje desde el formulario de contacto ($siteName)\n\n";
    $body .= "Nombre: $nombre\n";
    $body .= "Email: $email\n";
    $body .= "Asunto: $asunto\n\n";
    $body .= "Mensaje:\n$mensaje\n";

    $headers   = [];
    $headers[] = "MIME-Version: 1.0";
    $headers[] = "Content-Type: text/plain; charset=UTF-8";
    $headers[] = "From: $fromName <no-reply@" . ($_SERVER["SERVER_NAME"] ?? "localhost") . ">";
    $headers[] = "Reply-To: $nombre <$email>";

    if (mail($toEmail, $subject, $body, implode("\r\n", $headers))) {
      $success = true;
    } else {
      $errorMsg = __('contact.error.send');
    }
  }
}
?>
<!doctype html>
<html lang="<?= current_lang() ?>">
<head>
    <?php include __DIR__ . '/../includes/ads.php'; ?>
  <?php include __DIR__ . '/../includes/analytics.php'; ?>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= __('contact.title') ?></title>
  <link rel="stylesheet" href="<?php echo asset_url('assets/'); ?>styles.css">
  <meta name="description" content="<?= __('contact.desc') ?>">

<link rel="canonical" href="https://www.hockey5.pro/pages/contacto.php">
<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" type="image/png" href="/assets/img/favicon-48.png" sizes="48x48">
<link rel="icon" type="image/png" href="/assets/img/favicon-192.png" sizes="192x192">
<link rel="apple-touch-icon" href="/assets/img/favicon-192.png">

<meta property="og:type" content="website">
<meta property="og:title" content="<?= __('contact.title') ?>">
<meta property="og:description" content="<?= __('contact.desc') ?>">
<meta property="og:image" content="https://www.hockey5.pro/assets/img/logo_h5.png">
<meta property="og:image:width" content="512">
<meta property="og:image:height" content="512">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= __('contact.title') ?>">
<meta name="twitter:description" content="<?= __('contact.desc') ?>">
<meta name="twitter:image" content="https://www.hockey5.pro/assets/img/logo_h5.png">

</head>

<body class="page-contact">


<?php include __DIR__ . '/../includes/header.php'; ?>

<main class="container contact-page">

  <section class="contact-hero">
    <h1><?= __('contact.h1') ?></h1>
  </section>

  <section class="contact-grid">

    <div class="contact-card">

      <h2><?= __('contact.subtitle') ?></h2>

      <?php if ($success): ?>
        <div class="alert success">
          <?= __('contact.success') ?>
        </div>
      <?php elseif ($errorMsg !== ""): ?>
        <div class="alert error">
          <?= htmlspecialchars($errorMsg, ENT_QUOTES, "UTF-8"); ?>
        </div>
      <?php endif; ?>

      <?php if (!$success): ?>
      <form class="contact-form" method="post">

        <div class="form-row">
          <label for="nombre"><?= __('contact.form.name') ?></label>
          <input id="nombre" name="nombre" type="text" required
            value="<?= htmlspecialchars($_POST["nombre"] ?? "", ENT_QUOTES); ?>">
        </div>

        <div class="form-row">
          <label for="email"><?= __('contact.form.email') ?></label>
          <input id="email" name="email" type="email" required
            value="<?= htmlspecialchars($_POST["email"] ?? "", ENT_QUOTES); ?>">
        </div>

        <div class="form-row">
          <label for="asunto"><?= __('contact.form.subject') ?></label>
          <input id="asunto" name="asunto" type="text" required
            value="<?= htmlspecialchars($_POST["asunto"] ?? "", ENT_QUOTES); ?>">
        </div>

        <div class="form-row">
          <label for="mensaje"><?= __('contact.form.message') ?></label>
          <textarea id="mensaje" name="mensaje" rows="6" required><?= htmlspecialchars($_POST["mensaje"] ?? "", ENT_QUOTES); ?></textarea>
        </div>

        <label class="form-check">
          <input type="checkbox" name="privacy" required <?= isset($_POST["privacy"]) ? "checked" : ""; ?>>
          <span><?= sprintf(__('contact.form.privacy'), url('privacy')) ?></span>
        </label>

        <button class="btn ga-track" type="submit" style="background-color:#fff; color:#000; border:1px solid #fff;"
                data-ga-action="submit_contact" data-ga-category="contact" data-ga-label="form_submission"><?= __('contact.form.submit') ?></button>

        <p class="form-note">
          <?= __('contact.form.note') ?>
        </p>

      </form>
      <?php endif; ?>

    </div>

    <aside class="contact-card contact-info">

      <h2><?= __('contact.sidebar.title') ?></h2>

      <div class="info-block">
        <h3><?= __('contact.sidebar.email') ?></h3>
        <p><a href="mailto:soporte@hockey5.pro">soporte@hockey5.pro</a></p>
      </div>

      <div class="info-block">
        <h3><?= __('contact.sidebar.social') ?></h3>
        <div class="social-row">
          <a class="social" href="#">
            <img src="<?php echo asset_url('assets/'); ?>img/logo_ig.png" alt="">
            Instagram
          </a>
          <a class="social" href="#">
            <span class="social-x">X</span>
            X
          </a>
        </div>
      </div>

    </aside>

  </section>
</main>




<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
