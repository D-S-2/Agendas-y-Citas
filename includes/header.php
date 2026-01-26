<?php
// Evitar que entren sin loguearse
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['id_usuario'])) {
    header("Location: /DR_MUELITA/views/auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dr. Muelitas - Panel</title>
    
    <link rel="stylesheet" href="/DR_MUELITA/assets/css/global.css">
    
    <link rel="stylesheet" href="/DR_MUELITA/assets/css/dashboard.css">

    <?php if(isset($page_css)): ?>
        <link rel="stylesheet" href="/DR_MUELITA/assets/css/<?php echo $page_css; ?>">
    <?php endif; ?>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="app-container">
        <header class="top-bar">
            <div class="logo-area">
                <h3><i class="fas fa-tooth"></i> Dr. Muelitas</h3>
            </div>
            <div class="user-area">
                <span>Hola, <b><?php echo $_SESSION['nombre_completo']; ?></b> (<?php echo $_SESSION['rol']; ?>)</span>
                <a href="../../logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Salir</a>
            </div>
        </header>
        
        <div class="main-layout">