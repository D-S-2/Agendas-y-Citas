<?php
$page_title = "Nueva Cita Médica";
$page_css = "citas.css";
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../models/Paciente.php';
require_once '../../models/Odontologo.php';

$pacientes = (new Paciente())->listarTodos();
$doctores = (new Odontologo())->listarTodos();

$fecha_predeterminada = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');
$hora_inicio = isset($_GET['hora']) ? $_GET['hora'] : '08:30';
?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .clinical-form { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); border-top: 6px solid #3498db; }
    .section-title { color: #2c3e50; border-bottom: 2px solid #f0f2f5; padding-bottom: 10px; margin-bottom: 25px; font-size: 1.1rem; text-transform: uppercase; }
    .time-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 20px; }
    
    /* --- ESTILOS COMPACTOS (SOLO TEXTO) --- */
    .category-label { 
        font-size: 0.7rem; font-weight: 800; color: #95a5a6; 
        margin-top: 12px; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.5px; 
    }
    
    .treatment-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); 
        gap: 8px; 
    }

    .t-btn {
        background: #fff; border: 1px solid #dcdcdc; border-radius: 4px; padding: 8px 10px;
        cursor: pointer; text-align: left; transition: all 0.2s;
        display: flex; justify-content: space-between; align-items: center;
        font-size: 0.85rem; color: #2c3e50;
    }

    .t-btn:hover { background: #f1f8ff; border-color: #3498db; }
    
    /* Estado Seleccionado */
    .t-btn.active { 
        background-color: #3498db; color: white; border-color: #2980b9; 
        font-weight: 600; box-shadow: 0 2px 5px rgba(52, 152, 219, 0.3);
    }
    
    .t-time-badge { 
        font-size: 0.7rem; background: #eee; padding: 2px 6px; border-radius: 10px; color: #555; 
    }
    .t-btn.active .t-time-badge { background: rgba(255,255,255,0.2); color: white; }

    /* Input oculto para validación */
    #tratamientoVal { opacity: 0; position: absolute; height: 0; }
</style>

<main class="main-content">
    <div class="page-header">
        <h1><i class="fas fa-calendar-plus"></i> Agendar Cita</h1>
        <a href="calendario.php" class="btn-primary" style="background-color: #7f8c8d;"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>

    <div style="max-width: 1100px; margin: 0 auto;">
        <form action="../../controllers/citaController.php" method="POST" class="clinical-form" id="formCita">
            
            <div style="display: grid; grid-template-columns: 1fr 1.3fr; gap: 40px;">
                <div>
                    <h3 class="section-title"><i class="fas fa-user-injured"></i> Datos</h3>
                    <div class="form-group">
                        <label>Seleccionar Paciente:</label>
                        <select name="id_paciente" class="form-control select2" required style="width: 100%;">
                            <option value="">Buscar paciente...</option>
                            <?php foreach ($pacientes as $p): ?>
                                <option value="<?php echo $p['id_paciente']; ?>">
                                    <?php 
                                        $dep = !empty($p['departamento']) ? " (" . $p['departamento'] . ")" : "";
                                        echo $p['ci'] . $dep . ' - ' . $p['nombres'] . ' ' . $p['apellido_paterno']; 
                                    ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" style="margin-top: 20px;">
                        <label>Odontólogo:</label>
                        <select name="id_odontologo" class="form-control select2" required style="width: 100%;">
                            <?php foreach ($doctores as $d): ?>
                                <option value="<?php echo $d['id_odontologo']; ?>">Dr. <?php echo $d['nombres'] . ' ' . $d['apellidos']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-top: 20px; border: 1px solid #eee;">
                        <div class="form-group">
                            <label>Fecha:</label>
                            <input type="date" name="fecha" id="fecha" required class="form-control" 
                                   value="<?php echo $fecha_predeterminada; ?>" min="<?php echo date('Y-m-d'); ?>">
                        </div>

                        <div class="time-grid">
                            <div class="form-group">
                                <label>Inicio:</label>
                                <input type="time" name="hora_inicio" id="hora_inicio" required class="form-control" 
                                       value="<?php echo $hora_inicio; ?>" step="300">
                            </div>
                            <div class="form-group">
                                <label>Fin (Auto):</label>
                                <input type="time" name="hora_fin" id="hora_fin" required class="form-control" 
                                       value="" readonly style="background-color: #e9ecef;">
                            </div>
                        </div>
                        <small id="error-time" style="color: #e74c3c; display: none; margin-top: 10px; font-weight: bold; font-size: 0.85rem;"></small>
                        
                        <div class="form-group" style="margin-top: 20px; border-top: 1px solid #ddd; padding-top: 15px;">
                            <label>Motivo:</label>
                            <textarea name="motivo" id="motivo" class="form-control" rows="2" required placeholder="Seleccione un tratamiento..."></textarea>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="section-title"><i class="fas fa-list-ul"></i> Tratamientos</h3>
                    
                    <div class="category-label">1. Diagnóstico y Urgencias</div>
                    <div class="treatment-grid">
                        <div class="t-btn" onclick="seleccionarTratamiento(this, 'Consulta / Valoración', 15)">
                            <span>Consulta</span> <span class="t-time-badge">15m</span>
                        </div>
                        <div class="t-btn" onclick="seleccionarTratamiento(this, 'Urgencia / Dolor Agudo', 30)">
                            <span>Urgencia</span> <span class="t-time-badge">30m</span>
                        </div>
                    </div>

                    <div class="category-label">2. Higiene y Estética</div>
                    <div class="treatment-grid">
                        <div class="t-btn" onclick="seleccionarTratamiento(this, 'Limpieza Dental (Profilaxis)', 30)">
                            <span>Limpieza</span> <span class="t-time-badge">30m</span>
                        </div>
                        <div class="t-btn" onclick="seleccionarTratamiento(this, 'Blanqueamiento Dental', 60)">
                            <span>Blanqueamiento</span> <span class="t-time-badge">60m</span>
                        </div>
                    </div>

                    <div class="category-label">3. Operatoria / Curaciones</div>
                    <div class="treatment-grid">
                        <div class="t-btn" onclick="seleccionarTratamiento(this, 'Curación Simple', 30)">
                            <span>Curación Simple</span> <span class="t-time-badge">30m</span>
                        </div>
                        <div class="t-btn" onclick="seleccionarTratamiento(this, 'Curación Media', 45)">
                            <span>Curación Media</span> <span class="t-time-badge">45m</span>
                        </div>
                        <div class="t-btn" onclick="seleccionarTratamiento(this, 'Curación Compleja', 60)">
                            <span>Curación Comp.</span> <span class="t-time-badge">60m</span>
                        </div>
                    </div>

                    <div class="category-label">4. Cirugía / Extracciones</div>
                    <div class="treatment-grid">
                        <div class="t-btn" onclick="seleccionarTratamiento(this, 'Extracción de Incisivos', 30)">
                            <span>Incisivos</span> <span class="t-time-badge">30m</span>
                        </div>
                        <div class="t-btn" onclick="seleccionarTratamiento(this, 'Extracción de Caninos', 45)">
                            <span>Caninos</span> <span class="t-time-badge">45m</span>
                        </div>
                        <div class="t-btn" onclick="seleccionarTratamiento(this, 'Extracción de Premolares', 45)">
                            <span>Premolares</span> <span class="t-time-badge">45m</span>
                        </div>
                        <div class="t-btn" onclick="seleccionarTratamiento(this, 'Extracción de Molares', 60)">
                            <span>Molares</span> <span class="t-time-badge">60m</span>
                        </div>
                    </div>

                    <div class="category-label">5. Endodoncia</div>
                    <div class="treatment-grid">
                         <div class="t-btn" onclick="seleccionarTratamiento(this, 'Tratamiento de Conducto', 60)">
                            <span>Trat. Conducto</span> <span class="t-time-badge">60m</span>
                        </div>
                    </div>

                    <div class="category-label">6. Ortodoncia</div>
                    <div class="treatment-grid">
                        <div class="t-btn" onclick="seleccionarTratamiento(this, 'Servicio de Brackets - Tipo 1', 20)">
                            <span>Brackets T1</span> <span class="t-time-badge">20m</span>
                        </div>
                        <div class="t-btn" onclick="seleccionarTratamiento(this, 'Servicio de Brackets - Tipo 2', 20)">
                            <span>Brackets T2</span> <span class="t-time-badge">20m</span>
                        </div>
                        <div class="t-btn" onclick="seleccionarTratamiento(this, 'Servicio de Brackets - Tipo 3', 20)">
                            <span>Brackets T3</span> <span class="t-time-badge">20m</span>
                        </div>
                    </div>

                    <input type="text" id="tratamientoVal" required style="position:absolute; opacity:0; pointer-events:none;">

                    <div style="margin-top: 30px;">
                        <button type="submit" class="btn-primary" style="padding: 15px; width: 100%; justify-content: center; font-size: 1rem;">
                            <i class="fas fa-check"></i> AGENDAR CITA
                        </button>
                    </div>
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
    });

    const hInicio = document.getElementById('hora_inicio');
    const hFin = document.getElementById('hora_fin');
    const motivoTxt = document.getElementById('motivo');
    const validacionInput = document.getElementById('tratamientoVal'); 
    const errorMsg = document.getElementById('error-time');

    let minutosSeleccionados = 0;

    function seleccionarTratamiento(elemento, nombreTratamiento, minutos) {
        document.querySelectorAll('.t-btn').forEach(el => el.classList.remove('active'));
        elemento.classList.add('active');
        minutosSeleccionados = minutos;
        motivoTxt.value = nombreTratamiento;
        validacionInput.value = "ok"; 
        calcularHoraFin();
    }

    function calcularHoraFin() {
        if (minutosSeleccionados === 0 || !hInicio.value) return;
        let fechaBase = new Date("2000-01-01T" + hInicio.value + ":00");
        fechaBase.setMinutes(fechaBase.getMinutes() + minutosSeleccionados);
        let horas = fechaBase.getHours().toString().padStart(2, '0');
        let minutos = fechaBase.getMinutes().toString().padStart(2, '0');
        hFin.value = horas + ":" + minutos;
        validarRangoAtencion();
    }

    function validarRangoAtencion() {
        if(!hFin.value) return; 
        const aMinutos = (h) => { const [hr, mn] = h.split(':').map(Number); return hr * 60 + mn; };
        let minInicio = aMinutos(hInicio.value);
        let minFin = aMinutos(hFin.value);
        const enRango = (m) => (m >= 510 && m <= 750) || (m >= 930 && m <= 1110);
        if (!enRango(minInicio) || !enRango(minFin)) {
            errorMsg.innerText = "⚠️ Fuera de horario de atención.";
            errorMsg.style.display = "block";
        } else {
            errorMsg.style.display = "none";
        }
    }
    hInicio.addEventListener('change', calcularHoraFin);
</script>
<?php require_once '../../includes/footer.php'; ?>