<?php
session_start();
require_once __DIR__ . '/../models/Cita.php';

$citaModel = new Cita();

// ==========================================
// 1. API JSON (Para que funcione el Calendario)
// ==========================================

// Listar eventos para FullCalendar
if (isset($_GET['accion']) && $_GET['accion'] == 'listar') {
    $id_filtro = isset($_GET['id_odontologo']) && !empty($_GET['id_odontologo']) ? $_GET['id_odontologo'] : null;
    $citas = $citaModel->listarParaCalendario($id_filtro);

    $eventos = [];
    foreach ($citas as $cita) {
        $color = '#3498db'; // Azul (Programada)
        if ($cita['estado'] == 'ATENDIDA') $color = '#2ecc71'; // Verde
        if ($cita['estado'] == 'CANCELADA') $color = '#e74c3c'; // Rojo
        if ($cita['estado'] == 'NO_ASISTIO') $color = '#95a5a6'; // Gris

        $eventos[] = [
            'id' => $cita['id_cita'],
            'title' => $cita['title'] . " (" . $cita['estado'] . ")", // Muestra estado en el calendario
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

// Mover cita (Drag & Drop en el calendario)
if (isset($_POST['accion']) && $_POST['accion'] == 'mover') {
    $id = $_POST['id_cita'];
    $inicio = $_POST['start'];
    $fin = $_POST['end'];

    // Verificar disponibilidad antes de mover
    $citaOriginal = $citaModel->obtenerPorId($id);
    if ($citaModel->verificarDisponibilidad($citaOriginal['id_odontologo'], $citaOriginal['id_paciente'], $inicio, $fin, $id)) {
        if ($citaModel->mover($id, $inicio, $fin)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se puede mover una cita que no está PROGRAMADA.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Horario ocupado.']);
    }
    exit;
}

// ==========================================
// 2. ACCIONES DE ESTADO (Botones)
// ==========================================

if (isset($_GET['accion'])) {
    $id_cita = $_GET['id'];
    $resultado = false;
    $msg = "error";

    // Cancelar Cita
    if ($_GET['accion'] == 'cancelar') {
        $resultado = $citaModel->cancelar($id_cita, $_SESSION['id_usuario']);
        $msg = "cancelada"; // Mensaje para la alerta verde
    } 
    // Marcar como No Asistió
    elseif ($_GET['accion'] == 'no_asistio') {
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
// 3. GUARDAR CITA (Crear o Editar con Horas Personalizadas)
// ==========================================

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['accion'])) {

    // Validar datos básicos
    if (empty($_POST['fecha']) || empty($_POST['hora_inicio']) || empty($_POST['hora_fin'])) {
        header("Location: ../views/citas/calendario.php?error=datos_incompletos");
        exit;
    }

    // Construir fechas completas (Fecha + Hora)
    $inicio = $_POST['fecha'] . ' ' . $_POST['hora_inicio']; // Ej: 2023-10-20 09:30
    $fin = $_POST['fecha'] . ' ' . $_POST['hora_fin'];       // Ej: 2023-10-20 10:20
    
    $id_odontologo = $_POST['id_odontologo'];
    $id_paciente = $_POST['id_paciente'];
    $id_cita = isset($_POST['id_cita']) && !empty($_POST['id_cita']) ? $_POST['id_cita'] : null;

    // Validación 1: La hora fin debe ser mayor a inicio
    if (strtotime($fin) <= strtotime($inicio)) {
        header("Location: ../views/citas/calendario.php?error=hora_invalida");
        exit;
    }

    // Validación 2: Disponibilidad en base de datos (ignora canceladas)
    if (!$citaModel->verificarDisponibilidad($id_odontologo, $id_paciente, $inicio, $fin, $id_cita)) {
        header("Location: ../views/citas/calendario.php?error=ocupado");
        exit;
    }

    // Preparar datos para el modelo
    $datos = [
        'id_paciente' => $id_paciente,
        'id_odontologo' => $id_odontologo,
        'inicio' => $inicio,
        'fin' => $fin,
        'motivo' => trim($_POST['motivo']),
        'id_usuario' => $_SESSION['id_usuario']
    ];

    // Editar o Crear
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