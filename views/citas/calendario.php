<?php
$page_title = "Agenda de Citas";
$page_css = "citas.css";

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../models/Odontologo.php';

$odoModel = new Odontologo();
$doctores = $odoModel->listarTodos();
?>

<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />

<main class="main-content">

    <?php if (isset($_GET['ok'])): ?>
        <div class="alert alert-success" style="padding: 10px; margin-bottom: 10px; background: #d4edda; color: #155724; border-radius: 5px;">
            <i class="fas fa-check-circle"></i>
            <?php
            if ($_GET['ok'] == 'creado') echo 'Cita creada exitosamente.';
            elseif ($_GET['ok'] == 'editado') echo 'Cita actualizada correctamente.';
            elseif ($_GET['ok'] == 'cancelada') echo 'Cita cancelada.';
            elseif ($_GET['ok'] == 'movida') echo 'Cita reagendada exitosamente.';
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger" style="padding: 10px; margin-bottom: 10px; background: #f8d7da; color: #721c24; border-radius: 5px;">
            <i class="fas fa-exclamation-triangle"></i>
            <?php
            if ($_GET['error'] == 'ocupado') echo '<strong>Horario no disponible.</strong> El horario o el paciente ya tienen una cita asignada.';
            else echo 'Error al procesar la solicitud.';
            ?>
        </div>
    <?php endif; ?>

    <div class="page-header" style="flex-wrap: wrap; gap: 10px;">
        <h1 style="margin-right: auto;"><i class="far fa-calendar-alt"></i> Agenda de Citas</h1>

        <div style="display: flex; align-items: center; gap: 8px;">
            <label style="font-weight: bold; color: #555; font-size: 0.9rem;">Filtrar por Doctor:</label>
            <select id="filtroDoctor" class="form-control" style="width: 200px; padding: 6px;" onchange="filtrarCalendario()">
                <option value="">Todos los Doctores</option>
                <?php foreach ($doctores as $d): ?>
                    <option value="<?php echo $d['id_odontologo']; ?>">Dr. <?php echo $d['nombres'] . ' ' . $d['apellidos']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <a href="agenda_dia.php" class="btn-primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-calendar-day"></i> Agenda del Día
        </a>

        <a href="nueva.php" class="btn-primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-plus"></i> Nueva Cita
        </a>
    </div>

    <div id="calendar-container">
        <div id='calendar'></div>
    </div>
</main>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/es.js'></script>

<script>
    var calendar;
    var doctorFiltrado = '';

    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');

        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
            height: '100%',
            contentHeight: 'auto',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            editable: true,
            selectable: true,

            // CARGA DE CITAS DESDE EL CONTROLADOR
            events: function(fetchInfo, successCallback, failureCallback) {
                fetch('../../controllers/citaController.php?accion=listar&id_odontologo=' + doctorFiltrado)
                    .then(response => response.json())
                    .then(data => successCallback(data))
                    .catch(error => failureCallback(error));
            },

            // AL HACER CLIC EN UN CUADRO VACÍO: Redirigir a la página completa
            dateClick: function(info) {
                // info.dateStr contiene la fecha del cuadro donde hiciste clic (ej: 2026-01-26)
                window.location.href = 'nueva.php?fecha=' + info.dateStr;
            },

            // AL HACER CLIC EN UNA CITA: Redirigir a la página de edición
            eventClick: function(info) {
                info.jsEvent.preventDefault();
                window.location.href = 'editar.php?id=' + info.event.id;
            }
        });

        calendar.render();
    });

    function filtrarCalendario() {
        doctorFiltrado = document.getElementById('filtroDoctor').value;
        calendar.refetchEvents();
    }
</script>

<?php require_once '../../includes/footer.php'; ?>