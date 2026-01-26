<?php
$page_title = "Agenda de Citas";
$page_css = "citas.css"; 

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../models/Paciente.php';
require_once '../../models/Odontologo.php';

$pacienteModel = new Paciente();
$pacientes = $pacienteModel->listarTodos();
$odoModel = new Odontologo();
$doctores = $odoModel->listarTodos();
?>

<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />

<main class="main-content">
    
    <?php if(isset($_GET['ok'])): ?>
        <div class="alert alert-success" style="padding: 10px; margin-bottom: 10px; background: #d4edda; color: #155724; border-radius: 5px;">
            <i class="fas fa-check-circle"></i> 
            <?php 
                if($_GET['ok'] == 'creado') echo 'Cita creada exitosamente.';
                elseif($_GET['ok'] == 'editado') echo 'Cita actualizada correctamente.';
                elseif($_GET['ok'] == 'cancelada') echo 'Cita cancelada.';
                elseif($_GET['ok'] == 'movida') echo 'Cita reagendada exitosamente.';
            ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-danger" style="padding: 10px; margin-bottom: 10px; background: #f8d7da; color: #721c24; border-radius: 5px;">
            <i class="fas fa-exclamation-triangle"></i> 
            <?php 
                if($_GET['error'] == 'ocupado') echo '<strong>Horario no disponible.</strong> El doctor ya tiene una cita en ese horario.';
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
                <?php foreach($doctores as $d): ?>
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

    <div style="display: flex; gap: 15px; margin-bottom: 10px; font-size: 0.85rem; color: #666;">
        <div style="display: flex; align-items: center; gap: 4px;">
            <div style="width: 14px; height: 14px; background: #3498db; border-radius: 2px;"></div> Programada
        </div>
        <div style="display: flex; align-items: center; gap: 4px;">
            <div style="width: 14px; height: 14px; background: #2ecc71; border-radius: 2px;"></div> Atendida
        </div>
        <div style="display: flex; align-items: center; gap: 4px;">
            <div style="width: 14px; height: 14px; background: #e74c3c; border-radius: 2px;"></div> Cancelada
        </div>
    </div>

    <div id="calendar-container">
        <div id='calendar'></div>
    </div>
</main>

<!-- MODAL PARA CREAR/EDITAR CITA -->
<div id="modalCita" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitulo">Nueva Cita</h2>
            <span class="close" onclick="cerrarModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form action="../../controllers/citaController.php" method="POST" id="formCita">
                <input type="hidden" name="id_cita" id="id_cita">

                <div class="form-group">
                    <label>Paciente: <span style="color: red;">*</span></label>
                    <select name="id_paciente" id="id_paciente" class="form-control" required>
                        <option value="">Seleccione un paciente...</option>
                        <?php foreach($pacientes as $p): ?>
                            <option value="<?php echo $p['id_paciente']; ?>">
                                <?php echo $p['nombres'] . ' ' . $p['apellido_paterno'] . ' - CI: ' . $p['ci']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Odontólogo: <span style="color: red;">*</span></label>
                    <select name="id_odontologo" id="id_odontologo" class="form-control" required>
                        <option value="">Seleccione un doctor...</option>
                        <?php foreach($doctores as $d): ?>
                            <option value="<?php echo $d['id_odontologo']; ?>">
                                Dr. <?php echo $d['nombres'] . ' ' . $d['apellidos']; ?>
                                <?php if($d['especialidad']): ?> - <?php echo $d['especialidad']; ?><?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div class="form-group">
                        <label>Fecha: <span style="color: red;">*</span></label>
                        <input type="date" name="fecha" id="fecha" required class="form-control" min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group">
                        <label>Hora: <span style="color: red;">*</span></label>
                        <input type="time" name="hora" id="hora" required class="form-control" step="900">
                    </div>
                </div>

                <div class="form-group">
                    <label>Motivo de consulta: <span style="color: red;">*</span></label>
                    <textarea name="motivo" id="motivo" class="form-control" rows="3" placeholder="Ej: Dolor en muela, revisión general..." required style="resize: vertical;"></textarea>
                </div>

                <div id="infoEstado" style="display: none; background: #f8f9fa; padding: 10px; border-radius: 5px; margin-top: 10px;">
                    <strong>Estado actual:</strong> <span id="estadoTexto"></span>
                </div>

                <div style="margin-top: 20px; display: flex; flex-wrap: wrap; gap: 10px; align-items: center; border-top: 1px solid #eee; padding-top: 15px;">
                    
                    <button type="button" id="btnCancelar" onclick="confirmarCancelacion()" style="display:none; background: #e74c3c; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-size: 0.9rem;">
                        <i class="fas fa-ban"></i> Cancelar Cita
                    </button>

                    <div style="display: flex; gap: 8px; margin-left: auto;">
                        <a id="btnHistorial" href="#" target="_blank" style="display: none; background: #34495e; color: white; padding: 8px 15px; border-radius: 4px; text-decoration: none; font-size: 0.9rem;">
                            <i class="fas fa-file-medical"></i> Historial
                        </a>
                        <a id="btnAtender" href="#" style="display: none; background: #2ecc71; color: white; padding: 8px 15px; border-radius: 4px; text-decoration: none; font-size: 0.9rem;">
                            <i class="fas fa-user-md"></i> Atender
                        </a>
                        <button type="submit" class="btn-primary" id="btnGuardar" style="padding: 8px 20px;">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

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
            aspectRatio: 1.8,
            
            slotMinTime: '07:00:00',
            slotMaxTime: '21:00:00',
            allDaySlot: false,
            slotDuration: '00:30:00',

            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            
            businessHours: {
                daysOfWeek: [1, 2, 3, 4, 5, 6],
                startTime: '08:00',
                endTime: '20:00'
            },

            editable: true,
            droppable: false,
            
            events: function(fetchInfo, successCallback, failureCallback) {
                fetch('../../controllers/citaController.php?accion=listar&id_odontologo=' + doctorFiltrado)
                    .then(response => response.json())
                    .then(data => successCallback(data))
                    .catch(error => {
                        console.error('Error al cargar citas:', error);
                        failureCallback(error);
                    });
            },
            
            dateClick: function(info) {
                abrirModalCrear(info.dateStr);
            },

            eventClick: function(info) {
                // 1. Evitamos que el navegador haga cosas raras por defecto
                info.jsEvent.preventDefault();

                // 2. Obtenemos el ID de la cita en la que hiciste clic
                var id = info.event.id;
                
                // 3. EN LUGAR DE BUSCAR DATOS Y ABRIR UNA VENTANA FLOTANTE...
                // ...Simplemente redirigimos a tu nueva página profesional de edición.
                window.location.href = 'editar.php?id=' + id;
            },

            eventDrop: function(info) {
                if(!confirm('¿Confirmar reagendación de la cita?')) {
                    info.revert();
                    return;
                }

                var formData = new FormData();
                formData.append('accion', 'mover');
                formData.append('id_cita', info.event.id);
                formData.append('start', formatearFechaISO(info.event.start));
                formData.append('end', formatearFechaISO(info.event.end || info.event.start));

                fetch('../../controllers/citaController.php', { 
                    method: 'POST', 
                    body: formData 
                })
                .then(response => response.json())
                .then(data => {
                    if(data.status === 'error') {
                        alert('Error: ' + data.message);
                        info.revert();
                    } else {
                        window.location.href = 'calendario.php?ok=movida';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al mover la cita');
                    info.revert();
                });
            },

            eventResize: function(info) {
                info.revert();
                alert('No se puede cambiar la duración. Las citas tienen duración fija de 30 minutos.');
            }
        });
        
        calendar.render();
    });

    function filtrarCalendario() {
        doctorFiltrado = document.getElementById('filtroDoctor').value;
        calendar.refetchEvents();
    }

    var modal = document.getElementById("modalCita");
    var form = document.getElementById("formCita");

    function abrirModalCrear(fechaPreseleccionada) {
        form.reset();
        document.getElementById('id_cita').value = "";
        document.getElementById('modalTitulo').innerText = "Nueva Cita";
        
        document.getElementById('btnCancelar').style.display = "none";
        document.getElementById('btnAtender').style.display = "none";
        document.getElementById('btnHistorial').style.display = "none";
        document.getElementById('infoEstado').style.display = "none";
        document.getElementById('btnGuardar').style.display = "inline-block";
        
        if(fechaPreseleccionada) {
            if(fechaPreseleccionada.indexOf('T') === -1) {
                document.getElementById('fecha').value = fechaPreseleccionada;
                document.getElementById('hora').value = "09:00";
            } else {
                let partes = fechaPreseleccionada.split('T');
                document.getElementById('fecha').value = partes[0];
                document.getElementById('hora').value = partes[1].substring(0, 5);
            }
        } else {
            document.getElementById('fecha').value = '';
            document.getElementById('hora').value = '09:00';
        }
        
        modal.style.display = "block";
    }

    function abrirModalEditar(data) {
        document.getElementById('id_cita').value = data.id_cita;
        document.getElementById('id_paciente').value = data.id_paciente;
        document.getElementById('id_odontologo').value = data.id_odontologo;
        document.getElementById('motivo').value = data.motivo;
        
        var partes = data.fecha_hora_inicio.split(' ');
        document.getElementById('fecha').value = partes[0];
        document.getElementById('hora').value = partes[1].substring(0, 5);

        document.getElementById('modalTitulo').innerText = "Modificar Cita";
        
        // Mostrar estado
        document.getElementById('infoEstado').style.display = 'block';
        document.getElementById('estadoTexto').innerText = data.estado;
        document.getElementById('estadoTexto').style.color = 
            data.estado == 'PROGRAMADA' ? '#3498db' :
            data.estado == 'ATENDIDA' ? '#2ecc71' :
            data.estado == 'CANCELADA' ? '#e74c3c' : '#666';

        // Mostrar botones según estado
        if(data.estado == 'PROGRAMADA') {
            document.getElementById('btnCancelar').style.display = "inline-block";
            document.getElementById('btnAtender').style.display = "inline-block";
            document.getElementById('btnAtender').href = "atender.php?id_cita=" + data.id_cita;
            document.getElementById('btnGuardar').style.display = "inline-block";
        } else if(data.estado == 'ATENDIDA') {
            document.getElementById('btnCancelar').style.display = "none";
            document.getElementById('btnAtender').style.display = "none";
            document.getElementById('btnGuardar').style.display = "none";
        } else if(data.estado == 'CANCELADA') {
            document.getElementById('btnCancelar').style.display = "none";
            document.getElementById('btnAtender').style.display = "none";
            document.getElementById('btnGuardar').style.display = "none";
        }
        
        document.getElementById('btnHistorial').style.display = "inline-block";
        document.getElementById('btnHistorial').href = "../pacientes/historia.php?buscar_ci=" + data.ci;

        modal.style.display = "block";
    }

    function confirmarCancelacion() {
        if(!confirm('¿Está seguro de cancelar esta cita?\n\nEsta acción quedará registrada en el sistema.')) {
            return;
        }
        
        var id = document.getElementById('id_cita').value;
        var formData = new FormData();
        formData.append('accion', 'cancelar');
        formData.append('id_cita', id);

        fetch('../../controllers/citaController.php', { 
            method: 'POST', 
            body: formData 
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                window.location.href = 'calendario.php?ok=cancelada';
            } else {
                alert('Error al cancelar la cita.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al procesar la cancelación');
        });
    }

    function cerrarModal() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            cerrarModal();
        }
    }

    function formatearFechaISO(date) {
        var year = date.getFullYear();
        var month = ('0' + (date.getMonth() + 1)).slice(-2);
        var day = ('0' + date.getDate()).slice(-2);
        var hours = ('0' + date.getHours()).slice(-2);
        var minutes = ('0' + date.getMinutes()).slice(-2);
        var seconds = ('0' + date.getSeconds()).slice(-2);
        return year + '-' + month + '-' + day + ' ' + hours + ':' + minutes + ':' + seconds;
    }
</script>

<?php require_once '../../includes/footer.php'; ?>