<?php
session_start();
require_once __DIR__ . '/../models/Usuario.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userModel = new Usuario();

    // Validar contraseña
    $password = $_POST['password'];
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $datos = [
        'nombres' => $_POST['nombres'],
        'apellidos' => $_POST['apellidos'],
        'usuario' => $_POST['usuario'],
        'password' => $password_hash,
        'email' => $_POST['email'],
        'telefono' => $_POST['telefono'],
        'id_rol' => $_POST['id_rol'],
        // Datos opcionales de doctor
        'matricula' => isset($_POST['matricula']) ? $_POST['matricula'] : null,
        'especialidad' => isset($_POST['especialidad']) ? $_POST['especialidad'] : null
    ];

    if ($userModel->crear($datos)) {
        header("Location: ../views/admin/usuarios.php?mensaje=creado");
    } else {
        header("Location: ../views/admin/usuarios.php?error=fallo");
    }
}
?>