<?php
// controllers/citaController.php
require_once __DIR__ . '/../models/Cita.php';

$citaModel = new Cita();

// Acción: Listar citas para el calendario (JSON)
if (isset($_GET['accion']) && $_GET['accion'] == 'listar') {
    $citas = $citaModel->listarParaCalendario();
    echo json_encode($citas);
    exit;
}

// Acción: Guardar o Editar
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inicio = $_POST['fecha'] . ' ' . $_POST['hora'];
    $fin = date('Y-m-d H:i:s', strtotime($inicio . " +30 minutes"));
    
    $datos = [
        'id_paciente' => $_POST['id_paciente'],
        'id_odontologo' => $_POST['id_odontologo'],
        'inicio' => $inicio,
        'fin' => $fin,
        'motivo' => $_POST['motivo'],
        'id_usuario' => 1 // ID por defecto ya que eliminamos el módulo de usuarios
    ];

    if (isset($_POST['id_cita']) && !empty($_POST['id_cita'])) {
        $datos['id_cita'] = $_POST['id_cita'];
        $citaModel->actualizar($datos);
    } else {
        $citaModel->crear($datos);
    }
    header("Location: ../views/citas/calendario.php?ok=1");
}
?>