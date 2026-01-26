<?php
session_start();
require_once __DIR__ . '/../models/Usuario.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuarioModel = new Usuario();
    
    // Recibir y limpiar datos
    $usuario = trim($_POST['usuario']);
    $password = $_POST['password'];

    // Buscar usuario en BD
    $datosUsuario = $usuarioModel->obtenerPorUsuario($usuario);

    if ($datosUsuario) {
        // Verificar contraseña encriptada
        // En la BD el campo es 'password_hash'
        if (password_verify($password, $datosUsuario['password_hash'])) {
            
            // ¡Login Exitoso! Guardamos datos en sesión
            $_SESSION['id_usuario'] = $datosUsuario['id_usuario'];
            $_SESSION['usuario'] = $datosUsuario['usuario'];
            $_SESSION['nombre_completo'] = $datosUsuario['nombres'] . ' ' . $datosUsuario['apellidos'];
            $_SESSION['rol'] = $datosUsuario['rol_nombre']; // 'ADMIN', 'DOCTOR', etc.
            
            // Redirigir al Dashboard (lo crearemos luego)
            header("Location: ../views/admin/dashboard.php");
            exit;
        } else {
            // Contraseña incorrecta
            header("Location: ../views/auth/login.php?error=credenciales");
            exit;
        }
    } else {
        // Usuario no existe o inactivo
        header("Location: ../views/auth/login.php?error=credenciales");
        exit;
    }
} else {
    // Si intentan entrar directo al controlador sin POST
    header("Location: ../views/auth/login.php");
}
?>