<?php
session_start();
require_once __DIR__ . '/../models/Cita.php';

$citaModel = new Cita();

// --- API JSON ---

// 1. Listar (Con filtro de doctor opcional)
if (isset($_GET['accion']) && $_GET['accion'] == 'listar') {

    // Filtrar por doctor si se especifica
    $id_filtro = isset($_GET['id_odontologo']) && !empty($_GET['id_odontologo'])
        ? $_GET['id_odontologo']
        : null;

    $citas = $citaModel->listarParaCalendario($id_filtro);

    $eventos = [];
    foreach ($citas as $cita) {
        // Colores según estado
        $color = '#3498db'; // Azul (PROGRAMADA)
        if ($cita['estado'] == 'ATENDIDA') $color = '#2ecc71'; // Verde
        if ($cita['estado'] == 'CANCELADA') $color = '#e74c3c'; // Rojo
        if ($cita['estado'] == 'NO_ASISTIO') $color = '#95a5a6'; // Gris

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

// 2. Obtener UNA cita
if (isset($_GET['accion']) && $_GET['accion'] == 'obtener') {
    $id = $_GET['id'];
    $cita = $citaModel->obtenerPorId($id);

    header('Content-Type: application/json');
    echo json_encode($cita);
    exit;
}

// 3. Mover Cita (Drag & Drop)
if (isset($_POST['accion']) && $_POST['accion'] == 'mover') {
    $id = $_POST['id_cita'];
    $inicio = $_POST['start'];
    $fin = $_POST['end'];

    // Obtener datos de la cita para verificar disponibilidad
    $citaOriginal = $citaModel->obtenerPorId($id);

    // Verificar que el horario esté disponible
    if ($citaModel->verificarDisponibilidad($citaOriginal['id_odontologo'], $inicio, $fin, $id)) {
        if ($citaModel->mover($id, $inicio, $fin)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al actualizar']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Horario ocupado']);
    }
    exit;
}

// 4. Cancelar Cita (MEJORADO - Ahora registra quién cancela)
if (isset($_POST['accion']) && $_POST['accion'] == 'cancelar') {
    $id = $_POST['id_cita'];
    $id_usuario = $_SESSION['id_usuario']; // Usuario que cancela

    if ($citaModel->cancelar($id, $id_usuario)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al cancelar']);
    }
    exit;
}

// --- FORMULARIO POST (Crear/Editar) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['accion'])) {

    // Validar datos obligatorios
    if (
        empty($_POST['id_paciente']) || empty($_POST['id_odontologo']) ||
        empty($_POST['fecha']) || empty($_POST['hora']) || empty($_POST['motivo'])
    ) {
        header("Location: ../views/citas/calendario.php?error=datos_incompletos");
        exit;
    }

    $inicio = $_POST['fecha'] . ' ' . $_POST['hora'];
    $duracion = 30; // Duración fija de 30 minutos
    $fin = date('Y-m-d H:i:s', strtotime($inicio . " +$duracion minutes"));
    $id_odontologo = $_POST['id_odontologo'];

    $id_cita = isset($_POST['id_cita']) && !empty($_POST['id_cita']) ? $_POST['id_cita'] : null;

    // Verificar disponibilidad del horario
    if (!$citaModel->verificarDisponibilidad($id_odontologo, $id_paciente, $inicio, $fin, $id_cita)) {
        header("Location: ../views/citas/calendario.php?error=ocupado");
        exit;
    }

    $datos = [
        'id_paciente' => $_POST['id_paciente'],
        'id_odontologo' => $id_odontologo,
        'inicio' => $inicio,
        'fin' => $fin,
        'motivo' => trim($_POST['motivo']),
        'id_usuario' => $_SESSION['id_usuario']
    ];

    // Editar cita existente
    if ($id_cita) {
        $datos['id_cita'] = $id_cita;

        if ($citaModel->actualizar($datos)) {
            header("Location: ../views/citas/calendario.php?ok=editado");
        } else {
            header("Location: ../views/citas/calendario.php?error=1");
        }
    }
    // Crear nueva cita
    else {
        if ($citaModel->crear($datos)) {
            header("Location: ../views/citas/calendario.php?ok=creado");
        } else {
            header("Location: ../views/citas/calendario.php?error=1");
        }
    }
}
