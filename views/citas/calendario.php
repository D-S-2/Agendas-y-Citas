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

<style>
    /* Estilos para la leyenda de colores */
    .leyenda-colores {
        display: flex;
        gap: 20px;
        margin-bottom: 15px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 8px;
        font-size: 0.9rem;
        flex-wrap: wrap;
    }
    .item-leyenda { display: flex; align-items: center; gap: 6px; }
    .bolita { width: 12px; height: 12px; border-radius: 50%; display: inline-block; }
</style>

<main class="main-content">
    <?php if (isset($_GET['ok'])): ?>
        <div class="alert alert-success" style="padding: 10px; margin-bottom: 10px; background: #d4edda; color: #155724; border-radius: 5px;">
            <i class="fas fa-check-circle"></i>
            <?php
            if ($_GET['ok'] == 'creado') echo 'Cita creada exitosamente.';
            elseif ($_GET['ok'] == 'editado') echo 'Cita actualizada correctamente.';
            elseif ($_GET['ok'] == 'cancelada') echo 'Cita cancelada.';
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger" style="padding: 10px; margin-bottom: 10px; background: #f8d7da; color: #721c24; border-radius: 5px;">
            <i class="fas fa-exclamation-triangle"></i>
            <?php
            if ($_GET['error'] == 'ocupado') echo '<strong>Horario no disponible.</strong> Verifique que sea dentro del horario de atención y que el doctor/paciente estén libres.';
            elseif ($_GET['error'] == 'hora_invalida') echo 'La hora de fin debe ser posterior a la de inicio.';
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
        <a href="agenda_dia.php" class="btn-primary" style="text-decoration: none;"><i class="fas fa-calendar-day"></i> Agenda del Día</a>
        <a href="nueva.php" class="btn-primary" style="text-decoration: none;"><i class="fas fa-plus"></i> Nueva Cita</a>
    </div>

    <div class="leyenda-colores">
        <div class="item-leyenda"><span class="bolita" style="background:#3498db;"></span> Programada</div>
        <div class="item-leyenda"><span class="bolita" style="background:#2ecc71;"></span> Atendida</div>
        <div class="item-leyenda"><span class="bolita" style="background:#e74c3c;"></span> Cancelada</div>
        <div class="item-leyenda"><span class="bolita" style="background:#95a5a6;"></span> No Asistió</div>
    </div>

    <div id="calendar-container"><div id='calendar'></div></div>
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
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            
            // CONFIGURACIÓN DE HORARIOS (Lunes a Sábado, 8:30 a 18:30)
            hiddenDays: [0], // Ocultar Domingos
            slotMinTime: '08:30:00',
            slotMaxTime: '18:30:00',
            allDaySlot: false,
            
            // Configurar eventos
            events: function(fetchInfo, successCallback, failureCallback) {
                fetch('../../controllers/citaController.php?accion=listar&id_odontologo=' + doctorFiltrado)
                    .then(response => response.json())
                    .then(data => successCallback(data))
                    .catch(error => failureCallback(error));
            },

            // Acciones
            dateClick: function(info) {
                if (info.view.type === 'dayGridMonth') {
                    window.location.href = 'nueva.php?fecha=' + info.dateStr;
                } else {
                    // Validar hora de almuerzo en la vista de semana/día
                    let horaStr = info.dateStr.split('T')[1].substring(0, 5);
                    if (horaStr >= '12:30' && horaStr < '15:30') {
                        alert("Horario de almuerzo (12:30 - 15:30). Elija otra hora.");
                    } else {
                        window.location.href = 'nueva.php?fecha=' + info.dateStr.split('T')[0] + '&hora=' + horaStr;
                    }
                }
            },

            eventClick: function(info) {
                info.jsEvent.preventDefault();
                window.location.href = 'editar.php?id=' + info.event.id;
            },

            // Permitir arrastrar y soltar (Drag & Drop)
            editable: true,
            eventDrop: function(info) {
                if (!confirm("¿Desea mover esta cita a " + info.event.start.toLocaleString() + "?")) {
                    info.revert();
                    return;
                }

                // Convertir fecha JS a formato MySQL
                var start = info.event.start;
                var end = info.event.end;
                
                // Ajuste de zona horaria manual simple (FullCalendar a veces usa UTC)
                var offset = start.getTimezoneOffset() * 60000;
                var startLocal = new Date(start.getTime() - offset).toISOString().slice(0, 19).replace('T', ' ');
                var endLocal = new Date(end.getTime() - offset).toISOString().slice(0, 19).replace('T', ' ');

                var formData = new FormData();
                formData.append('accion', 'mover');
                formData.append('id_cita', info.event.id);
                formData.append('start', startLocal);
                formData.append('end', endLocal);

                fetch('../../controllers/citaController.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status !== 'success') {
                        alert(data.message || 'Error al mover la cita');
                        info.revert();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error de conexión');
                    info.revert();
                });
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