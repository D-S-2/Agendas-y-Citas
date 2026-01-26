<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dr. Muelitas</title>
    <link rel="stylesheet" href="../../assets/css/global.css">
    <link rel="stylesheet" href="../../assets/css/login.css">
</head>
<body>

    <div class="login-container">
        <h2>Dr. Muelitas</h2>
        <p style="margin-bottom: 20px; color: #7f8c8d;">Control de Acceso</p>

        <?php if(isset($_GET['error']) && $_GET['error'] == 'credenciales'): ?>
            <div class="alert">Usuario o contraseña incorrectos.</div>
        <?php endif; ?>

        <form action="../../controllers/authController.php" method="POST">
            <div class="form-group">
                <label for="usuario">Usuario</label>
                <input type="text" id="usuario" name="usuario" class="form-control" placeholder="Ej: admin" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="******" required>
            </div>

            <button type="submit" class="btn-login">Ingresar</button>
        </form>
    </div>

</body>
</html>