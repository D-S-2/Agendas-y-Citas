<?php
session_start();
require_once __DIR__ . '/../models/Paciente.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pacienteModel = new Paciente();
    
    // Recolectamos datos del formulario
    $datos = [
        'ci' => $_POST['ci'],
        'nombres' => $_POST['nombres'],
        'apellido_paterno' => $_POST['apellido_paterno'],
        'apellido_materno' => $_POST['apellido_materno'],
        'fecha_nacimiento' => $_POST['fecha_nacimiento'],
        'sexo' => $_POST['sexo'],
        'direccion' => $_POST['direccion'],
        'telefono' => $_POST['telefono']
    ];

    // Verificamos si es una EDICIÓN (tiene ID) o uno NUEVO
    if (isset($_POST['id_paciente']) && !empty($_POST['id_paciente'])) {
        $datos['id_paciente'] = $_POST['id_paciente'];
        if ($pacienteModel->actualizar($datos)) {
            $_SESSION['mensaje'] = "Paciente actualizado correctamente.";
            $_SESSION['tipo_mensaje'] = "success";
        } else {
            $_SESSION['mensaje'] = "Error al actualizar paciente.";
            $_SESSION['tipo_mensaje'] = "danger";
        }
    } else {
        // Es NUEVO
        if ($pacienteModel->crear($datos)) {
            $_SESSION['mensaje'] = "Paciente registrado correctamente.";
            $_SESSION['tipo_mensaje'] = "success";
        } else {
            $_SESSION['mensaje'] = "Error al registrar paciente.";
            $_SESSION['tipo_mensaje'] = "danger";
        }
    }

    // Redirigir a la lista
    header("Location: ../views/pacientes/lista.php");
    exit;
}
?>