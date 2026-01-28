<?php
$page_title = "Gestionar Cita";
$page_css = "citas.css";
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../models/Cita.php';
require_once '../../models/Paciente.php';
require_once '../../models/Odontologo.php';

if (!isset($_GET['id'])) { echo "<script>window.location.href='calendario.php';</script>"; exit; }
$id_cita = $_GET['id'];

$citaModel = new Cita();
$cita = $citaModel->obtenerPorId($id_cita);

if (!$cita) { echo "<h1>Cita no encontrada</h1>"; exit; }

$pacientes = (new Paciente())->listarTodos();
$doctores = (new Odontologo())->listarTodos();

$fecha_solo = date('Y-m-d', strtotime($cita['fecha_hora_inicio']));
$hora_inicio = date('H:i', strtotime($cita['fecha_hora_inicio']));
$hora_fin = date('H:i', strtotime($cita['fecha_hora_fin']));
?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .clinical-form { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    .btn-group-actions { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
    .status-badge { padding: 5px 12px; border-radius: 15px; font-weight: bold; font-size: 0.9rem; text-transform: uppercase; color: white; }
    
    .st-PROGRAMADA { background-color: #3498db; }
    .st-ATENDIDA { background-color: #2ecc71; }
    .st-CANCELADA { background-color: #e74c3c; }
    .st-NO_ASISTIO { background-color: #95a5a6; }

    /* --- ESTILOS COMPACTOS --- */
    .category-label { font-size: 0.7rem; font-weight: 800; color: #95a5a6; margin-top: 10px; margin-bottom: 4px; text-transform: uppercase; border-bottom: 1px solid #eee; }
    .treatment-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 6px; }
    
    .t-btn {
        background: #fff; border: 1px solid #dcdcdc; border-radius: 4px; padding: 6px 8px;
        cursor: pointer; text-align: left; transition: all 0.2s;
        display: flex; justify-content: space-between; align-items: center;
        font-size: 0.8rem; color: #2c3e50;
    }
    .t-btn:hover { background: #f1f8ff; border-color: #3498db; }
    .t-btn.active { background-color: #3498db; color: white; border-color: #2980b9; font-weight: 600; }
    .t-time-badge { font-size: 0.65rem; background: #eee; padding: 1px 5px; border-radius: 10px; color: #555; }
    .t-btn.active .t-time-badge { background: rgba(255,255,255,0.2); color: white; }
</style>

<main class="main-content">
    <div class="page-header">
        <div style="display: flex; align-items: center; gap: 15px;">
            <h1><i class="fas fa-edit"></i> Gestión de Cita #<?php echo $id_cita; ?></h1>
            <span class="status-badge st-<?php echo $cita['estado']; ?>"><?php echo $cita['estado']; ?></span>
        </div>
        <div class="btn-group-actions">
            <a href="calendario.php" class="btn-primary" style="background-color: #7f8c8d;"><i class="fas fa-arrow-left"></i> Volver</a>
        </div>
    </div>

    <div style="max-width: 1100px; margin: 0 auto;">
        
        <div style="background: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <div><strong>Acciones Rápidas:</strong></div>
            <div style="display: flex; gap: 10px;">
                <a href="#" class="btn-primary" style="background: #27ae60; cursor: default; opacity: 0.8;"><i class="fas fa-user-check"></i> Atender</a>
                <?php if ($cita['estado'] == 'PROGRAMADA'): ?>
                    <a href="../../controllers/citaController.php?accion=no_asistio&id=<?php echo $id_cita; ?>" class="btn-primary" style="background: #95a5a6;" onclick="return confirm('¿Marcar que NO ASISTIÓ?')"><i class="fas fa-user-slash"></i> No Asistió</a>
                    <a href="../../controllers/citaController.php?accion=cancelar&id=<?php echo $id_cita; ?>" class="btn-primary" style="background: #c0392b;" onclick="return confirm('¿Seguro que desea CANCELAR?')"><i class="fas fa-times-circle"></i> Cancelar</a>
                <?php endif; ?>
            </div>
        </div>

        <form action="../../controllers/citaController.php" method="POST" class="clinical-form" id="formEditar">
            <input type="hidden" name="id_cita" value="<?php echo $cita['id_cita']; ?>">

            <div style="display: grid; grid-template-columns: 1fr 1.3fr; gap: 30px;">
                <div>
                    <h3 style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px; color: #2c3e50;"><i class="fas fa-info-circle"></i> Detalles</h3>
                    <div class="form-group">
                        <label>Paciente:</label>
                        <select name="id_paciente" class="form-control select2" required style="width: 100%;">
                            <?php foreach ($pacientes as $p): ?>
                                <option value="<?php echo $p['id_paciente']; ?>" <?php echo ($p['id_paciente'] == $cita['id_paciente']) ? 'selected' : ''; ?>>
                                    <?php echo $p['ci'] . ' - ' . $p['nombres'] . ' ' . $p['apellido_paterno']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" style="margin-top: 15px;">
                        <label>Odontólogo:</label>
                        <select name="id_odontologo" class="form-control select2" required style="width: 100%;">
                            <?php foreach ($doctores as $d): ?>
                                <option value="<?php echo $d['id_odontologo']; ?>" <?php echo ($d['id_odontologo'] == $cita['id_odontologo']) ? 'selected' : ''; ?>>
                                    Dr. <?php echo $d['nombres'] . ' ' . $d['apellidos']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 20px;">
                        <div style="margin-bottom: 10px; color: #d35400; font-size: 0.85rem;"><i class="fas fa-clock"></i> <b>Horario</b></div>
                        <input type="date" name="fecha" id="fecha" required class="form-control" value="<?php echo $fecha_solo; ?>" min="<?php echo date('Y-m-d'); ?>">
                        <div style="display: flex; gap: 10px; margin-top: 10px;">
                            <div style="flex: 1;">
                                <label>Inicio:</label>
                                <input type="time" name="hora_inicio" id="h_ini" required class="form-control" value="<?php echo $hora_inicio; ?>">
                            </div>
                            <div style="flex: 1;">
                                <label>Fin:</label>
                                <input type="time" name="hora_fin" id="h_fin" required class="form-control" value="<?php echo $hora_fin; ?>" readonly>
                            </div>
                        </div>
                        <small id="error-time" style="color: #e74c3c; display: none; margin-top: 5px; font-weight: bold; font-size: 0.8rem;"></small>

                         <div class="form-group" style="margin-top: 20px; border-top: 1px solid #ddd; padding-top: 15px;">
                            <label>Motivo Actual:</label>
                            <textarea name="motivo" id="motivo" class="form-control" rows="2" required><?php echo $cita['motivo']; ?></textarea>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px; color: #2c3e50;"><i class="fas fa-list-ul"></i> Tratamiento</h3>
                    
                    <div class="category-label">1. Diagnóstico</div>
                    <div class="treatment-grid">
                        <div class="t-btn" data-nombre="Consulta / Valoración" onclick="seleccionarTratamiento(this, 'Consulta / Valoración', 15)">
                            <span>Consulta</span><span class="t-time-badge">15m</span>
                        </div>
                        <div class="t-btn" data-nombre="Urgencia / Dolor Agudo" onclick="seleccionarTratamiento(this, 'Urgencia / Dolor Agudo', 30)">
                            <span>Urgencia</span><span class="t-time-badge">30m</span>
                        </div>
                    </div>

                    <div class="category-label">2. Higiene</div>
                    <div class="treatment-grid">
                        <div class="t-btn" data-nombre="Limpieza Dental (Profilaxis)" onclick="seleccionarTratamiento(this, 'Limpieza Dental (Profilaxis)', 30)">
                            <span>Limpieza</span><span class="t-time-badge">30m</span>
                        </div>
                         <div class="t-btn" data-nombre="Blanqueamiento Dental" onclick="seleccionarTratamiento(this, 'Blanqueamiento Dental', 60)">
                            <span>Blanqueamiento</span><span class="t-time-badge">60m</span>
                        </div>
                    </div>

                    <div class="category-label">3. Operatoria</div>
                    <div class="treatment-grid">
                        <div class="t-btn" data-nombre="Curación Simple" onclick="seleccionarTratamiento(this, 'Curación Simple', 30)">
                            <span>Curación S.</span><span class="t-time-badge">30m</span>
                        </div>
                         <div class="t-btn" data-nombre="Curación Media" onclick="seleccionarTratamiento(this, 'Curación Media', 45)">
                            <span>Curación M.</span><span class="t-time-badge">45m</span>
                        </div>
                        <div class="t-btn" data-nombre="Curación Compleja" onclick="seleccionarTratamiento(this, 'Curación Compleja', 60)">
                            <span>Curación C.</span><span class="t-time-badge">60m</span>
                        </div>
                    </div>

                     <div class="category-label">4. Extracciones</div>
                    <div class="treatment-grid">
                        <div class="t-btn" data-nombre="Extracción de Incisivos" onclick="seleccionarTratamiento(this, 'Extracción de Incisivos', 30)">
                            <span>Incisivos</span><span class="t-time-badge">30m</span>
                        </div>
                        <div class="t-btn" data-nombre="Extracción de Caninos" onclick="seleccionarTratamiento(this, 'Extracción de Caninos', 45)">
                            <span>Caninos</span><span class="t-time-badge">45m</span>
                        </div>
                        <div class="t-btn" data-nombre="Extracción de Premolares" onclick="seleccionarTratamiento(this, 'Extracción de Premolares', 45)">
                            <span>Premolares</span><span class="t-time-badge">45m</span>
                        </div>
                        <div class="t-btn" data-nombre="Extracción de Molares" onclick="seleccionarTratamiento(this, 'Extracción de Molares', 60)">
                            <span>Molares</span><span class="t-time-badge">60m</span>
                        </div>
                    </div>

                     <div class="category-label">5. Endodoncia</div>
                    <div class="treatment-grid">
                        <div class="t-btn" data-nombre="Tratamiento de Conducto" onclick="seleccionarTratamiento(this, 'Tratamiento de Conducto', 60)">
                            <span>Trat. Conducto</span><span class="t-time-badge">60m</span>
                        </div>
                    </div>
                    
                    <div class="category-label">6. Ortodoncia</div>
                    <div class="treatment-grid">
                        <div class="t-btn" data-nombre="Servicio de Brackets - Tipo 1" onclick="seleccionarTratamiento(this, 'Servicio de Brackets - Tipo 1', 20)">
                            <span>Brackets T1</span><span class="t-time-badge">20m</span>
                        </div>
                         <div class="t-btn" data-nombre="Servicio de Brackets - Tipo 2" onclick="seleccionarTratamiento(this, 'Servicio de Brackets - Tipo 2', 20)">
                            <span>Brackets T2</span><span class="t-time-badge">20m</span>
                        </div>
                         <div class="t-btn" data-nombre="Servicio de Brackets - Tipo 3" onclick="seleccionarTratamiento(this, 'Servicio de Brackets - Tipo 3', 20)">
                            <span>Brackets T3</span><span class="t-time-badge">20m</span>
                        </div>
                    </div>

                    <?php if ($cita['estado'] == 'PROGRAMADA'): ?>
                        <div style="margin-top: 30px;">
                            <button type="submit" class="btn-primary" style="padding: 15px 30px; width: 100%; justify-content: center; font-size: 1rem;">
                                <i class="fas fa-save"></i> GUARDAR CAMBIOS
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2();

        let motivoActual = "<?php echo $cita['motivo']; ?>";
        let tarjeta = $(`.t-btn[data-nombre='${motivoActual}']`);
        if (tarjeta.length > 0) {
            tarjeta.addClass('active');
        }
    });

    const hInicio = document.getElementById('h_ini');
    const hFin = document.getElementById('h_fin');
    const motivoTxt = document.getElementById('motivo');
    const errorMsg = document.getElementById('error-time');
    let minutosSeleccionados = 0;

    function seleccionarTratamiento(elemento, nombre, minutos) {
        document.querySelectorAll('.t-btn').forEach(el => el.classList.remove('active'));
        elemento.classList.add('active');
        
        minutosSeleccionados = minutos;
        motivoTxt.value = nombre;
        calcularHoraFin();
    }

    function calcularHoraFin() {
        if (minutosSeleccionados === 0 || !hInicio.value) return;
        let fechaBase = new Date("2000-01-01T" + hInicio.value + ":00");
        fechaBase.setMinutes(fechaBase.getMinutes() + minutosSeleccionados);
        let horas = fechaBase.getHours().toString().padStart(2, '0');
        let mn = fechaBase.getMinutes().toString().padStart(2, '0');
        hFin.value = horas + ":" + mn;
        validarRangoAtencion();
    }
    
    function validarRangoAtencion() {
        if(!hFin.value) return; 
        const aMinutos = (h) => { const [hr, mn] = h.split(':').map(Number); return hr * 60 + mn; };
        let minInicio = aMinutos(hInicio.value);
        let minFin = aMinutos(hFin.value);
        const enRango = (m) => (m >= 510 && m <= 750) || (m >= 930 && m <= 1110);
        if (!enRango(minInicio) || !enRango(minFin)) {
            errorMsg.innerText = "Fuera de horario."; errorMsg.style.display = "block";
        } else { errorMsg.style.display = "none"; }
    }

    hInicio.addEventListener('change', () => {
        if (document.querySelector('.t-btn.active')) {
            calcularHoraFin();
        }
    });
</script>
<?php require_once '../../includes/footer.php'; ?>