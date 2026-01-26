<?php
require_once __DIR__ . '/../models/Cita.php';
$citaModel = new Cita();

// API para cargar eventos en el calendario con colores
if (isset($_GET['accion']) && $_GET['accion'] == 'listar') {
    $id_filtro = isset($_GET['id_odontologo']) && !empty($_GET['id_odontologo']) ? $_GET['id_odontologo'] : null;
    $citas = $citaModel->listarParaCalendario($id_filtro);
    
    $eventos = [];
    foreach($citas as $cita) {
        $color = '#3498db'; // Azul (PROGRAMADA)
        if($cita['estado'] == 'ATENDIDA') $color = '#2ecc71'; // Verde
        if($cita['estado'] == 'CANCELADA') $color = '#e74c3c'; // Rojo
        if($cita['estado'] == 'NO_ASISTIO') $color = '#95a5a6'; // Gris

        $eventos[] = [
            'id' => $cita['id_cita'],
            'title' => $cita['title'],
            'start' => $cita['start'],
            'end' => $cita['end'],
            'color' => $color
        ];
    }
    header('Content-Type: application/json');
    echo json_encode($eventos);
    exit;
}

// Guardar nueva cita
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inicio = $_POST['fecha'] . ' ' . $_POST['hora'];
    $fin = date('Y-m-d H:i:s', strtotime($inicio . " +30 minutes"));
    $datos = [
        'id_paciente' => $_POST['id_paciente'],
        'id_odontologo' => $_POST['id_odontologo'],
        'inicio' => $inicio,
        'fin' => $fin,
        'motivo' => $_POST['motivo'],
        'id_usuario' => 1 // ID por defecto
    ];
    $citaModel->crear($datos);
    header("Location: ../views/citas/calendario.php?ok=creado");
}