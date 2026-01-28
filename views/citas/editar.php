<?php
$page_title = "Gestionar Cita";
$page_css = "citas.css";
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../models/Cita.php';
require_once '../../models/Paciente.php';
require_once '../../models/Odontologo.php';

// Validar ID
if (!isset($_GET['id'])) {
    echo "<script>window.location.href='calendario.php';</script>";
    exit;
}
$id_cita = $_GET['id'];

// Obtener datos
$citaModel = new Cita();
$cita = $citaModel->obtenerPorId($id_cita);

if (!$cita) {
    echo "<h1>Cita no encontrada</h1>";
    exit;
}

// Listas para selects
$pacienteModel = new Paciente();
$pacientes = $pacienteModel->listarTodos();
$odoModel = new Odontologo();
$doctores = $odoModel->listarTodos();

// Separar fecha y horas para los inputs nuevos
$fecha_solo = date('Y-m-d', strtotime($cita['fecha_hora_inicio']));
$hora_inicio = date('H:i', strtotime($cita['fecha_hora_inicio']));
$hora_fin = date('H:i', strtotime($cita['fecha_hora_fin']));
?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Estilos propios del formulario */
    .clinical-form { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    .btn-group-actions { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
    .status-badge { padding: 5px 12px; border-radius: 15px; font-weight: bold; font-size: 0.9rem; text-transform: uppercase; color: white; }
    
    /* Colores de estado */
    .st-PROGRAMADA { background-color: #3498db; }
    .st-ATENDIDA { background-color: #2ecc71; }
    .st-CANCELADA { background-color: #e74c3c; }
    .st-NO_ASISTIO { background-color: #95a5a6; }
</style>

<main class="main-content">
    <div class="page-header">
        <div style="display: flex; align-items: center; gap: 15px;">
            <h1><i class="fas fa-edit"></i> Gestión de Cita #<?php echo $id_cita; ?></h1>
            <span class="status-badge st-<?php echo $cita['estado']; ?>"><?php echo $cita['estado']; ?></span>
        </div>
        
        <div class="btn-group-actions">
            <a href="calendario.php" class="btn-primary" style="background-color: #7f8c8d;">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div style="max-width: 1000px; margin: 0 auto;">
        
        <div style="background: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <div>
                <strong>Acciones Rápidas:</strong>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="#" class="btn-primary" style="background: #27ae60; cursor: default; opacity: 0.8;" title="Función no implementada">
                    <i class="fas fa-user-check"></i> Atender
                </a>
                <a href="#" class="btn-primary" style="background: #8e44ad; cursor: default; opacity: 0.8;" title="Función no implementada">
                    <i class="fas fa-file-medical"></i> Historial Médico
                </a>

                <?php if ($cita['estado'] == 'PROGRAMADA'): ?>
                    <a href="../../controllers/citaController.php?accion=no_asistio&id=<?php echo $id_cita; ?>" 
                       class="btn-primary" style="background: #95a5a6;"
                       onclick="return confirm('¿Marcar que el paciente NO ASISTIÓ?')">
                       <i class="fas fa-user-slash"></i> No Asistió
                    </a>
                    
                    <a href="../../controllers/citaController.php?accion=cancelar&id=<?php echo $id_cita; ?>" 
                       class="btn-primary" style="background: #c0392b;"
                       onclick="return confirm('¿Seguro que desea CANCELAR? El horario quedará libre.')">
                       <i class="fas fa-times-circle"></i> Cancelar Cita
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <form action="../../controllers/citaController.php" method="POST" class="clinical-form" id="formEditar">
            <input type="hidden" name="id_cita" value="<?php echo $cita['id_cita']; ?>">

            <h3 style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px; color: #2c3e50;">
                <i class="fas fa-info-circle"></i> Detalles de la Programación
            </h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                <div>
                    <div class="form-group">
                        <label style="font-weight: bold;">Paciente:</label>
                        <select name="id_paciente" class="form-control select2" required style="width: 100%;">
                            <?php foreach ($pacientes as $p): ?>
                                <option value="<?php echo $p['id_paciente']; ?>" <?php echo ($p['id_paciente'] == $cita['id_paciente']) ? 'selected' : ''; ?>>
                                    <?php echo $p['ci'] . ' - ' . $p['nombres'] . ' ' . $p['apellido_paterno']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" style="margin-top: 20px;">
                        <label style="font-weight: bold;">Odontólogo:</label>
                        <select name="id_odontologo" class="form-control select2" required style="width: 100%;">
                            <?php foreach ($doctores as $d): ?>
                                <option value="<?php echo $d['id_odontologo']; ?>" <?php echo ($d['id_odontologo'] == $cita['id_odontologo']) ? 'selected' : ''; ?>>
                                    Dr. <?php echo $d['nombres'] . ' ' . $d['apellidos']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" style="margin-top: 20px;">
                        <label style="font-weight: bold;">Motivo / Notas:</label>
                        <textarea name="motivo" class="form-control" rows="4" required><?php echo $cita['motivo']; ?></textarea>
                    </div>
                </div>

                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <div style="margin-bottom: 15px; color: #d35400; font-size: 0.9rem;">
                        <i class="fas fa-clock"></i> <b>Horario:</b> 8:30-12:30 | 15:30-18:30 (Lun-Sáb)
                    </div>

                    <div class="form-group">
                        <label style="font-weight: bold;">Fecha:</label>
                        <input type="date" name="fecha" id="fecha" required class="form-control" 
                               value="<?php echo $fecha_solo; ?>" min="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <div style="display: flex; gap: 15px; margin-top: 20px;">
                        <div style="flex: 1;">
                            <label style="font-weight: bold;">Hora Inicio:</label>
                            <input type="time" name="hora_inicio" id="h_ini" required class="form-control" 
                                   value="<?php echo $hora_inicio; ?>">
                        </div>
                        <div style="flex: 1;">
                            <label style="font-weight: bold;">Hora Fin:</label>
                            <input type="time" name="hora_fin" id="h_fin" required class="form-control" 
                                   value="<?php echo $hora_fin; ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div style="margin-top: 30px; text-align: right; border-top: 1px solid #eee; padding-top: 20px;">
                <?php if ($cita['estado'] == 'PROGRAMADA'): ?>
                    <button type="submit" class="btn-primary" style="padding: 12px 30px;">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                <?php else: ?>
                    <span style="color: #7f8c8d;">
                        <i class="fas fa-lock"></i> Esta cita no se puede editar porque ya fue finalizada o cancelada.
                    </span>
                <?php endif; ?>
            </div>
        </form>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Inicializar selectores bonitos
        $('.select2').select2({
            language: { noResults: () => "Sin resultados" }
        });

        // Validaciones al enviar formulario
        document.getElementById('formEditar').onsubmit = function(e) {
            const h1 = document.getElementById('h_ini').value;
            const h2 = document.getElementById('h_fin').value;
            const f = document.getElementById('fecha').value;
            
            // 1. Validar Domingo
            const dia = new Date(f + 'T00:00:00').getUTCDay();
            if (dia === 0) { 
                alert("Los domingos no hay atención."); 
                return false; 
            }

            // 2. Validar orden de horas
            if (h2 <= h1) { 
                alert("La hora de fin debe ser posterior a la de inicio."); 
                return false; 
            }
            
            // 3. Validar Rangos (8:30-12:30 y 15:30-18:30)
            const validarRango = (h) => (h >= "08:30" && h <= "12:30") || (h >= "15:30" && h <= "18:30");
            
            // Nota: validamos que AMBAS horas caigan dentro de algun rango permitido
            // O una lógica más flexible: que el inicio esté dentro del rango de apertura
            if (!validarRango(h1)) {
                alert("La hora de inicio está fuera del horario de atención (8:30-12:30 / 15:30-18:30).");
                return false;
            }
        };
    });
</script>

<?php require_once '../../includes/footer.php'; ?>