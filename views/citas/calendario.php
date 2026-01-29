<?php
$page_title = "Agenda de Citas";
$page_css = "citas.css";
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../models/Odontologo.php';
require_once '../../models/Paciente.php';

$odoModel = new Odontologo();
$pacModel = new Paciente();

$doctores = $odoModel->listarTodos();
$pacientes = $pacModel->listarTodos();
$fecha_hoy = date('Y-m-d');
?>

<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    .form-container-inline {
        background: white; padding: 25px; border-radius: 12px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.08); margin-bottom: 30px;
        border-top: 6px solid #3498db;
    }

    /* --- DISEÑO DE TRATAMIENTOS ORDENADO --- */
    .treatment-section {
        background: #fcfcfc; border: 1px solid #edf0f2; border-radius: 8px;
        padding: 15px; max-height: 400px; overflow-y: auto;
    }

    .category-block { margin-bottom: 15px; }

    .category-header {
        font-size: 0.7rem; font-weight: 800; color: #7f8c8d;
        text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;
        display: flex; align-items: center; gap: 10px;
    }

    .category-header::after { content: ""; flex: 1; height: 1px; background: #eee; }

    .treatment-grid {
        display: grid; grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)); gap: 8px;
    }

    .t-btn-mini {
        background: #fff; border: 1px solid #dce1e5; border-radius: 5px;
        padding: 8px 10px; cursor: pointer; display: flex;
        justify-content: space-between; align-items: center;
        font-size: 0.8rem; color: #34495e; transition: all 0.2s;
    }

    .t-btn-mini:hover { border-color: #3498db; background: #f0f7ff; }

    .t-btn-mini.active {
        background: #3498db; color: white; border-color: #2980b9;
        box-shadow: 0 3px 8px rgba(52, 152, 219, 0.3);
    }

    .t-time-badge {
        font-size: 0.65rem; background: #f1f3f5; padding: 1px 6px;
        border-radius: 10px; color: #7f8c8d; font-weight: bold;
    }

    .t-btn-mini.active .t-time-badge { background: rgba(255,255,255,0.25); color: white; }

    /* Scrollbar */
    .treatment-section::-webkit-scrollbar { width: 5px; }
    .treatment-section::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }
</style>

<main class="main-content">
    <div class="page-header">
        <h1><i class="far fa-calendar-alt"></i> Panel de Gestión Médica</h1>
    </div>

    <div class="form-container-inline">
        <form action="../../controllers/citaController.php" method="POST" id="formCita">
            <div style="display: grid; grid-template-columns: 1fr 1.8fr; gap: 30px;">
                
                <div>
                    <h4 style="margin-bottom: 15px; color: #2c3e50;"><i class="fas fa-user-edit"></i> Datos de la Cita</h4>
                    
                    <div class="form-group">
                        <label>Paciente:</label>
                        <select name="id_paciente" class="form-control select2" required style="width: 100%;">
                            <option value="">Buscar paciente...</option>
                            <?php foreach ($pacientes as $p): ?>
                                <option value="<?php echo $p['id_paciente']; ?>">
                                    <?php echo $p['ci'] . ' - ' . $p['nombres'] . ' ' . $p['apellido_paterno']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" style="margin-top: 15px;">
                        <label>Odontólogo:</label>
                        <select name="id_odontologo" id="form_id_odontologo" class="form-control select2" required style="width: 100%;">
                            <?php foreach ($doctores as $d): ?>
                                <option value="<?php echo $d['id_odontologo']; ?>">
                                    Dr. <?php echo $d['nombres'] . ' ' . $d['apellidos']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 15px; border: 1px solid #eee;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <div class="form-group">
                                <label>Fecha:</label>
                                <input type="date" name="fecha" id="form_fecha" class="form-control" value="<?php echo $fecha_hoy; ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Inicio:</label>
                                <input type="time" name="hora_inicio" id="form_inicio" class="form-control" value="08:30" required>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: 10px;">
                            <label>Fin estimado (Automático):</label>
                            <input type="time" name="hora_fin" id="form_fin" class="form-control" readonly style="background: #e9ecef;">
                        </div>
                        <div class="form-group" style="margin-top: 10px;">
                            <label>Motivo:</label>
                            <textarea name="motivo" id="form_motivo" class="form-control" rows="2" required readonly placeholder="Seleccione tratamiento..."></textarea>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 style="margin-bottom: 15px; color: #2c3e50;"><i class="fas fa-tooth"></i> Seleccione Tratamiento</h4>
                    <div class="treatment-section">
                        <div class="category-block">
                            <div class="category-header">1. Diagnóstico y Urgencias</div>
                            <div class="treatment-grid">
                                <div class="t-btn-mini" onclick="setTratamiento(this, 'Consulta / Valoración', 15)"><span>Consulta</span><span class="t-time-badge">15m</span></div>
                                <div class="t-btn-mini" onclick="setTratamiento(this, 'Urgencia / Dolor Agudo', 30)"><span>Urgencia</span><span class="t-time-badge">30m</span></div>
                            </div>
                        </div>

                        <div class="category-block">
                            <div class="category-header">2. Higiene y Estética</div>
                            <div class="treatment-grid">
                                <div class="t-btn-mini" onclick="setTratamiento(this, 'Limpieza Dental (Profilaxis)', 30)"><span>Limpieza</span><span class="t-time-badge">30m</span></div>
                                <div class="t-btn-mini" onclick="setTratamiento(this, 'Blanqueamiento Dental', 60)"><span>Blanqueamiento</span><span class="t-time-badge">60m</span></div>
                            </div>
                        </div>

                        <div class="category-block">
                            <div class="category-header">3. Operatoria / Curaciones</div>
                            <div class="treatment-grid">
                                <div class="t-btn-mini" onclick="setTratamiento(this, 'Curación Simple', 30)"><span>Simple</span><span class="t-time-badge">30m</span></div>
                                <div class="t-btn-mini" onclick="setTratamiento(this, 'Curación Media', 45)"><span>Media</span><span class="t-time-badge">45m</span></div>
                                <div class="t-btn-mini" onclick="setTratamiento(this, 'Curación Compleja', 60)"><span>Compleja</span><span class="t-time-badge">60m</span></div>
                            </div>
                        </div>

                        <div class="category-block">
                            <div class="category-header">4. Cirugía / Extracciones</div>
                            <div class="treatment-grid">
                                <div class="t-btn-mini" onclick="setTratamiento(this, 'Extracción de Incisivos', 30)"><span>Incisivos</span><span class="t-time-badge">30m</span></div>
                                <div class="t-btn-mini" onclick="setTratamiento(this, 'Extracción de Caninos', 45)"><span>Caninos</span><span class="t-time-badge">45m</span></div>
                                <div class="t-btn-mini" onclick="setTratamiento(this, 'Extracción de Premolares', 45)"><span>Premolares</span><span class="t-time-badge">45m</span></div>
                                <div class="t-btn-mini" onclick="setTratamiento(this, 'Extracción de Molares', 60)"><span>Molares</span><span class="t-time-badge">60m</span></div>
                            </div>
                        </div>

                        <div class="category-block">
                            <div class="category-header">5. Endodoncia</div>
                            <div class="treatment-grid">
                                <div class="t-btn-mini" onclick="setTratamiento(this, 'Tratamiento de Conducto', 60)"><span>Trat. Conducto</span><span class="t-time-badge">60m</span></div>
                            </div>
                        </div>

                        <div class="category-block">
                            <div class="category-header">6. Ortodoncia</div>
                            <div class="treatment-grid">
                                <div class="t-btn-mini" onclick="setTratamiento(this, 'Servicio de Brackets - Tipo 1', 20)"><span>Brackets T1</span><span class="t-time-badge">20m</span></div>
                                <div class="t-btn-mini" onclick="setTratamiento(this, 'Servicio de Brackets - Tipo 2', 20)"><span>Brackets T2</span><span class="t-time-badge">20m</span></div>
                                <div class="t-btn-mini" onclick="setTratamiento(this, 'Servicio de Brackets - Tipo 3', 20)"><span>Brackets T3</span><span class="t-time-badge">20m</span></div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary" style="margin-top: 20px; width: 100%; height: 45px;">
                        <i class="fas fa-calendar-plus"></i> AGENDAR CITA
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <label><b>Filtro:</b></label>
                <select id="filtroDoctor" class="form-control" style="width: 220px;" onchange="filtrarCalendario()">
                    <option value="">Todos los Doctores</option>
                    <?php foreach ($doctores as $d): ?>
                        <option value="<?php echo $d['id_odontologo']; ?>">Dr. <?php echo $d['nombres'] . ' ' . $d['apellidos']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <a href="agenda_dia.php" class="btn-primary" style="background:#7f8c8d;"><i class="fas fa-list"></i> Lista Diaria</a>
        </div>
        <div id="calendar"></div>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/es.js'></script>

<script>
    var calendar;
    let minutosSeleccionados = 0;

    $(document).ready(function() {
        $('.select2').select2();
        initCalendar();
    });

    function setTratamiento(elemento, nombre, minutos) {
        $('.t-btn-mini').removeClass('active');
        $(elemento).addClass('active');
        minutosSeleccionados = minutos;
        $('#form_motivo').val(nombre);
        calcularHoraFin();
    }

    function calcularHoraFin() {
        let hInicio = $('#form_inicio').val();
        if (minutosSeleccionados === 0 || !hInicio) return;
        let d = new Date("2000-01-01T" + hInicio + ":00");
        d.setMinutes(d.getMinutes() + minutosSeleccionados);
        $('#form_fin').val(d.getHours().toString().padStart(2, '0') + ":" + d.getMinutes().toString().padStart(2, '0'));
    }

    $('#form_inicio').on('change', calcularHoraFin);

    function initCalendar() {
        var calendarEl = document.getElementById('calendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth', 
            locale: 'es',
            slotMinTime: '08:30:00',
            slotMaxTime: '18:30:00',
            allDaySlot: false,
            hiddenDays: [0], // Domingo oculto
            height: 700,
            // BARRA DE HERRAMIENTAS RESTAURADA
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: function(info, success, failure) {
                let doctorId = $('#filtroDoctor').val() || '';
                fetch('../../controllers/citaController.php?accion=listar&id_odontologo=' + doctorId)
                    .then(res => res.json()).then(data => success(data));
            },
            dateClick: function(info) {
                let fecha = info.dateStr.split('T')[0];
                let hora = info.dateStr.split('T')[1] ? info.dateStr.split('T')[1].substring(0, 5) : "08:30";
                $('#form_fecha').val(fecha);
                $('#form_inicio').val(hora);
                window.scrollTo({ top: 0, behavior: 'smooth' });
                calcularHoraFin();
            },
            eventClick: function(info) {
                window.location.href = 'editar.php?id=' + info.event.id;
            }
        });
        calendar.render();
    }

    function filtrarCalendario() {
        calendar.refetchEvents();
        // Sincronizar doctor en el formulario
        let val = $('#filtroDoctor').val();
        if(val) $('#form_id_odontologo').val(val).trigger('change');
    }
</script>

<?php require_once '../../includes/footer.php'; ?>