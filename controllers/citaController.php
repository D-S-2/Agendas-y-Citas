<?php
session_start();
require_once __DIR__ . '/../models/Cita.php';

// Ajusta esto a tu zona horaria para que la protección funcione correctamente
date_default_timezone_set('America/La_Paz'); 

$citaModel = new Cita();

// ==========================================
// 1. API JSON (Calendario)
// ==========================================

if (isset($_GET['accion']) && $_GET['accion'] == 'listar') {
    $id_filtro = isset($_GET['id_odontologo']) && !empty($_GET['id_odontologo']) ? $_GET['id_odontologo'] : null;
    $citas = $citaModel->listarParaCalendario($id_filtro);

    $eventos = [];
    foreach ($citas as $cita) {
        $color = '#3498db'; 
        if ($cita['estado'] == 'ATENDIDA') $color = '#2ecc71'; 
        if ($cita['estado'] == 'CANCELADA') $color = '#e74c3c'; 
        if ($cita['estado'] == 'NO_ASISTIO') $color = '#95a5a6'; 

        $eventos[] = [
            'id' => $cita['id_cita'],
            'title' => $cita['title'], 
            'start' => $cita['start'],
            'end' => $cita['end'],
            'color' => $color,
            'extendedProps' => [
                'estado' => $cita['estado'],
                'motivo' => $cita['motivo']
            ]
        ];
    }
    header('Content-Type: application/json');
    echo json_encode($eventos);
    exit;
}

// ==========================================
// 2. MOVER CITA (Drag & Drop)
// ==========================================
if (isset($_POST['accion']) && $_POST['accion'] == 'mover') {
    $id = $_POST['id_cita'];
    $inicio = $_POST['start'];
    $fin = $_POST['end'];

    // PROTECCIÓN CONTRA EL PASADO
    if (strtotime($inicio) < time()) {
        echo json_encode(['status' => 'error', 'message' => 'No puedes mover una cita al pasado.']);
        exit;
    }

    $citaOriginal = $citaModel->obtenerPorId($id);
    if ($citaModel->verificarDisponibilidad($citaOriginal['id_odontologo'], $citaOriginal['id_paciente'], $inicio, $fin, $id)) {
        if ($citaModel->mover($id, $inicio, $fin)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Solo citas programadas pueden moverse.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Horario ocupado.']);
    }
    exit;
}

// ==========================================
// 3. CAMBIAR ESTADO (Botones)
// ==========================================

if (isset($_GET['accion'])) {
    $id_cita = $_GET['id'];
    $resultado = false;
    $msg = "error";

    if ($_GET['accion'] == 'cancelar') {
        $resultado = $citaModel->cancelar($id_cita, $_SESSION['id_usuario']);
        $msg = "cancelada";
    } elseif ($_GET['accion'] == 'no_asistio') {
        $resultado = $citaModel->marcarNoAsistio($id_cita);
        $msg = "editado";
    }

    if ($resultado) {
        header("Location: ../views/citas/calendario.php?ok=" . $msg);
    } else {
        header("Location: ../views/citas/calendario.php?error=1");
    }
    exit;
}

// ==========================================
// 4. GUARDAR / EDITAR CITA
// ==========================================

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['accion'])) {

    $inicio = $_POST['fecha'] . ' ' . $_POST['hora_inicio'];
    $fin = $_POST['fecha'] . ' ' . $_POST['hora_fin'];
    
    // PROTECCIÓN CONTRA EL PASADO (BACKEND)
    if (strtotime($inicio) < time()) {
        header("Location: ../views/citas/calendario.php?error=fecha_pasada");
        exit;
    }
    
    $id_odontologo = $_POST['id_odontologo'];
    $id_paciente = $_POST['id_paciente'];
    $id_cita = isset($_POST['id_cita']) && !empty($_POST['id_cita']) ? $_POST['id_cita'] : null;

    // Validación: Fin debe ser mayor a inicio
    if (strtotime($fin) <= strtotime($inicio)) {
        header("Location: ../views/citas/calendario.php?error=hora_invalida");
        exit;
    }

    // Validación Disponibilidad
    if (!$citaModel->verificarDisponibilidad($id_odontologo, $id_paciente, $inicio, $fin, $id_cita)) {
        header("Location: ../views/citas/calendario.php?error=ocupado");
        exit;
    }

    $datos = [
        'id_paciente' => $id_paciente,
        'id_odontologo' => $id_odontologo,
        'inicio' => $inicio,
        'fin' => $fin,
        'motivo' => trim($_POST['motivo']),
        'id_usuario' => $_SESSION['id_usuario']
    ];

    if ($id_cita) {
        $datos['id_cita'] = $id_cita;
        if ($citaModel->actualizar($datos)) {
            header("Location: ../views/citas/calendario.php?ok=editado");
        } else {
            header("Location: ../views/citas/calendario.php?error=1");
        }
    } else {
        if ($citaModel->crear($datos)) {
            header("Location: ../views/citas/calendario.php?ok=creado");
        } else {
            header("Location: ../views/citas/calendario.php?error=1");
        }
    }
}
?>