<?php
session_start();
require_once __DIR__ . '/../api/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        $pdo = hoquei5_pdo();
        $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `username` = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user'] = $user['username'];
            header("Location: index.php");
            exit;
        } else {
            $error = "Nombre de usuario o contraseña incorrectos.";
        }
    } catch (Exception $e) {
        $error = "Error de conexión con la base de datos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Hockey5 Admin</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1 style="font-size: 1.5rem; color: var(--primary); font-weight: 800;">HOCKEY 5</h1>
            <p style="color: var(--text-muted);">Panel de Gestión Administrativa</p>
        </div>

        <?php if ($error): ?>
            <div style="background: #fee2e2; color: #b91c1c; padding: 0.75rem; border-radius: 8px; margin-bottom: 1rem; font-size: 0.875rem;">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="username">Usuario</label>
                <input type="text" id="username" name="username" class="form-input" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" class="form-input" required>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-sign-in-alt" style="margin-right: 8px;"></i> Acceder
            </button>
        </form>
        
        <div style="margin-top: 1.5rem; text-align: center; font-size: 0.75rem; color: var(--text-muted);">
            &copy; <?php echo date('Y'); ?> Hockey5. Todos los derechos reservados.
        </div>
    </div>
</body>
</html>
