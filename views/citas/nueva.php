<?php
// views/citas/nueva.php
$page_title = "Nueva Cita Médica";
$page_css = "citas.css";

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../models/Paciente.php';
require_once '../../models/Odontologo.php';

// 1. Cargamos datos
$pacienteModel = new Paciente();
$pacientes = $pacienteModel->listarTodos();

$odoModel = new Odontologo();
$doctores = $odoModel->listarTodos();
?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Ajustes visuales para Select2 para que combine con tu diseño */
    .select2-container .select2-selection--single {
        height: 45px !important;
        border: 1px solid #ddd !important;
        display: flex;
        align-items: center;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
    }

    /* Estilo del formulario */
    .clinical-form {
        background: white;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        border-top: 6px solid #3498db;
    }

    .section-title {
        color: #2c3e50;
        border-bottom: 2px solid #f0f2f5;
        padding-bottom: 10px;
        margin-bottom: 25px;
        font-size: 1.1rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .quick-actions {
        margin-top: 5px;
        font-size: 0.85rem;
        text-align: right;
    }

    .quick-actions a {
        color: #3498db;
        text-decoration: none;
    }

    .quick-actions a:hover {
        text-decoration: underline;
    }
</style>

<main class="main-content">
    <div class="page-header">
        <h1><i class="fas fa-calendar-plus"></i> Gestión de Citas</h1>
        <a href="calendario.php" class="btn-primary" style="background-color: #7f8c8d;">
            <i class="fas fa-arrow-left"></i> Volver al Calendario
        </a>
    </div>

    <div style="max-width: 900px; margin: 0 auto;">

        <form action="../../controllers/citaController.php" method="POST" class="clinical-form">

            <h3 class="section-title"><i class="fas fa-user-injured"></i> Información del Paciente</h3>

            <div class="form-group">
                <label style="font-weight: bold;">Buscar Paciente (Nombre o Cédula):</label>
                <select name="id_paciente" class="form-control select2" required style="width: 100%;">
                    <option value="">Buscar paciente...</option>
                    <?php foreach ($pacientes as $p): ?>
                        <option value="<?php echo $p['id_paciente']; ?>">
                            <?php echo $p['ci'] . ' - ' . $p['nombres'] . ' ' . $p['apellido_paterno'] . ' ' . $p['apellido_materno']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="quick-actions">
                    ¿No encuentras al paciente? <a href="../pacientes/formulario.php" target="_blank"><i class="fas fa-plus-circle"></i> Registrar Nuevo Paciente</a>
                </div>
            </div>

            <br>

            <h3 class="section-title"><i class="fas fa-stethoscope"></i> Datos de la Consulta</h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                <div>
                    <div class="form-group">
                        <label style="font-weight: bold;">Odontólogo Asignado:</label>
                        <select name="id_odontologo" class="form-control select2" required style="width: 100%;">
                            <option value="">Seleccione profesional...</option>
                            <?php foreach ($doctores as $d): ?>
                                <option value="<?php echo $d['id_odontologo']; ?>">
                                    Dr. <?php echo $d['nombres'] . ' ' . $d['apellidos']; ?>
                                    (<?php echo $d['especialidad'] ? $d['especialidad'] : 'General'; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" style="margin-top: 20px;">
                        <label style="font-weight: bold;">Motivo de la visita:</label>
                        <textarea name="motivo" class="form-control" rows="4" required
                            placeholder="Ej: Dolor agudo en premolar superior derecho..."
                            style="resize: none; background: #f9f9f9;"></textarea>
                    </div>
                </div>

                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <div class="form-group">
                        <label style="font-weight: bold; color: #3498db;">Fecha de Cita:</label>
                        <input type="date" name="fecha" required class="form-control"
                            min="<?php echo date('Y-m-d'); ?>"
                            value="<?php echo isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d'); ?>"
                            style="height: 45px;">
                    </div>

                    <div class="form-group" style="margin-top: 20px;">
                        <label style="font-weight: bold; color: #3498db;">Hora Estimada:</label>
                        <input type="time" name="hora" required class="form-control"
                            value="09:00" step="1800"
                            style="height: 45px;">
                    </div>

                    <div style="margin-top: 20px; font-size: 0.85rem; color: #666; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-info-circle"></i>
                        <span>Las citas tienen una duración estándar de 30 minutos por defecto.</span>
                    </div>
                </div>
            </div>

            <div style="margin-top: 30px; text-align: right; border-top: 1px solid #eee; padding-top: 20px;">
                <button type="submit" class="btn-primary" style="padding: 12px 40px; font-size: 1.1rem; border-radius: 50px;">
                    <i class="fas fa-check"></i> Confirmar Agendamiento
                </button>
            </div>

        </form>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Inicializar el buscador en los selects
        $('.select2').select2({
            language: {
                noResults: function() {
                    return "No se encontraron resultados";
                }
            }
        });
    });
</script>

<?php require_once '../../includes/footer.php'; ?>