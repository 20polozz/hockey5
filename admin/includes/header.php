<?php require_once __DIR__ . '/../auth.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Admin'; ?> | Hockey5</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-wrapper">
        <aside class="sidebar">
            <div class="sidebar-logo">
                <img src="../assets/img/logo_h5.png" alt="Logo" style="width: 40px; height: 40px; object-fit: contain;">
                <span>H5 ADMIN</span>
            </div>
            
            <nav>
                <a href="index.php" class="nav-link <?php echo ($currentPage == 'dashboard') ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="players.php" class="nav-link <?php echo ($currentPage == 'players') ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> Jugadores
                </a>
                <a href="teams.php" class="nav-link <?php echo ($currentPage == 'teams') ? 'active' : ''; ?>">
                    <i class="fas fa-shield-alt"></i> Clubes / Equipos
                </a>
                <a href="leagues.php" class="nav-link <?php echo ($currentPage == 'leagues') ? 'active' : ''; ?>">
                    <i class="fas fa-trophy"></i> Ligas
                </a>
                <a href="seleccions.php" class="nav-link <?php echo ($currentPage == 'seleccions') ? 'active' : ''; ?>">
                    <i class="fas fa-flag"></i> Selecciones
                </a>
                <a href="users.php" class="nav-link <?php echo ($currentPage == 'users') ? 'active' : ''; ?>">
                    <i class="fas fa-user-cog"></i> Usuarios
                </a>
                <a href="donations.php" class="nav-link <?php echo ($currentPage == 'donations') ? 'active' : ''; ?>">
                    <i class="fas fa-heart"></i> Donaciones
                </a>
                
                <div style="margin-top: 2rem; padding: 0 1rem; font-size: 0.75rem; color: rgba(255,255,255,0.5); text-transform: uppercase; letter-spacing: 0.05em;">
                    Configuración
                </div>
                <a href="logout.php" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="top-bar">
                <div class="breadcrumb">
                    <span style="color: var(--text-muted);">Admin</span> 
                    <i class="fas fa-chevron-right" style="font-size: 0.7rem; margin: 0 0.5rem; color: #cbd5e1;"></i>
                    <span style="font-weight: 600;"><?php echo $pageTitle ?? 'Dashboard'; ?></span>
                </div>
                
                <div class="user-info" style="display: flex; align-items: center; gap: 10px;">
                    <div style="text-align: right;">
                        <div style="font-size: 0.875rem; font-weight: 600;"><?php echo $_SESSION['admin_user'] ?? 'Admin'; ?></div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">Administrador</div>
                    </div>
                    <div style="width: 40px; height: 40px; background: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--primary);">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            </header>
            
            <div class="page-content">
