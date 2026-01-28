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
</style>

<main class="main-content">
    <div class="page-header">
        <h1><i class="fas fa-calendar-plus"></i> Agendar Cita Personalizada</h1>
        <a href="calendario.php" class="btn-primary" style="background-color: #7f8c8d;"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>

    <div style="max-width: 900px; margin: 0 auto;">
        <form action="../../controllers/citaController.php" method="POST" class="clinical-form" id="formCita">
            <h3 class="section-title"><i class="fas fa-user-injured"></i> Información del Paciente</h3>
            <div class="form-group">
                <label>Paciente:</label>
                <select name="id_paciente" class="form-control select2" required style="width: 100%;">
                    <option value="">Seleccione un paciente...</option>
                    <?php foreach ($pacientes as $p): ?>
                        <option value="<?php echo $p['id_paciente']; ?>">
                            <?php echo $p['ci'] . ' - ' . $p['nombres'] . ' ' . $p['apellido_paterno']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div><br>

            <h3 class="section-title"><i class="fas fa-clock"></i> Horario de la Cita</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <div class="form-group">
                        <label>Fecha de la Cita:</label>
                        <input type="date" name="fecha" id="fecha" required class="form-control" 
                               value="<?php echo $fecha_predeterminada; ?>" min="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <div class="time-grid">
                        <div class="form-group">
                            <label><i class="far fa-clock"></i> Hora Inicio:</label>
                            <input type="time" name="hora_inicio" id="hora_inicio" required class="form-control" 
                                   value="<?php echo $hora_inicio; ?>" step="300">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-history"></i> Hora Fin:</label>
                            <input type="time" name="hora_fin" id="hora_fin" required class="form-control" 
                                   value="09:00" step="300">
                        </div>
                    </div>
                    <small id="error-time" style="color: #e74c3c; display: none; margin-top: 10px; font-weight: bold;"></small>
                </div>

                <div>
                    <div class="form-group">
                        <label>Odontólogo Responsable:</label>
                        <select name="id_odontologo" class="form-control select2" required style="width: 100%;">
                            <?php foreach ($doctores as $d): ?>
                                <option value="<?php echo $d['id_odontologo']; ?>">Dr. <?php echo $d['nombres'] . ' ' . $d['apellidos']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group" style="margin-top: 15px;">
                        <label>Motivo del Tratamiento:</label>
                        <textarea name="motivo" class="form-control" rows="3" required placeholder="Ej: Limpieza, Extracción..."></textarea>
                    </div>
                </div>
            </div>

            <div style="margin-top: 30px; text-align: right; border-top: 1px solid #eee; padding-top: 20px;">
                <button type="submit" class="btn-primary" style="padding: 12px 40px; border-radius: 5px;">
                    <i class="fas fa-check"></i> Finalizar Agendamiento
                </button>
            </div>
        </form>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() { 
        $('.select2').select2(); 

        const form = document.getElementById('formCita');
        const hInicio = document.getElementById('hora_inicio');
        const hFin = document.getElementById('hora_fin');
        const errorMsg = document.getElementById('error-time');

        form.onsubmit = function(e) {
            // Validar que la hora de fin sea posterior a la de inicio
            if (hFin.value <= hInicio.value) {
                e.preventDefault();
                errorMsg.innerText = "La hora de finalización debe ser posterior a la de inicio.";
                errorMsg.style.display = "block";
                hFin.focus();
                return false;
            }
            
            // Validar rangos de atención (8:30-12:30 | 15:30-18:30)
            const validarRango = (h) => {
                return (h >= "08:30" && h <= "12:30") || (h >= "15:30" && h <= "18:30");
            };

            if (!validarRango(hInicio.value) || !validarRango(hFin.value)) {
                e.preventDefault();
                errorMsg.innerText = "Recuerde el horario de atención: 8:30-12:30 y 15:30-18:30.";
                errorMsg.style.display = "block";
                return false;
            }
        };
    });
</script>
<?php require_once '../../includes/footer.php'; ?>