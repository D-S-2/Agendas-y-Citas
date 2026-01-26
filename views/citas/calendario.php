<?php
// views/citas/calendario.php
require_once '../../models/Paciente.php';
require_once '../../models/Odontologo.php';

$pacienteModel = new Paciente();
$pacientes = $pacienteModel->listarTodos();

$odoModel = new Odontologo();
$doctores = $odoModel->listarTodos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agenda de Citas</title>
    <link rel="stylesheet" href="../../assets/css/global.css">
    <link rel="stylesheet" href="../../assets/css/citas.css">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
</head>
<body>
    <div class="main-content">
        <div class="page-header">
            <h1><i class="far fa-calendar-alt"></i> Agenda de Citas</h1>
            <a href="nueva.php" class="btn-primary"><i class="fas fa-plus"></i> Nueva Cita</a>
        </div>

        <div id="calendar-container">
            <div id='calendar'></div>
        </div>
    </div>

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/es.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                events: '../../controllers/citaController.php?accion=listar',
                eventClick: function(info) {
                    window.location.href = 'editar.php?id=' + info.event.id;
                }
            });
            calendar.render();
        });
    </script>
</body>
</html>