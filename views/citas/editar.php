<?php
$page_title = "Modificar Cita";
$page_css = "citas.css";

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../models/Paciente.php';
require_once '../../models/Odontologo.php';
require_once '../../models/Cita.php';

// Validar ID de la cita
if (!isset($_GET['id'])) {
    echo "<script>window.location.href='calendario.php';</script>";
    exit;
}
$id_cita = $_GET['id'];

// Obtener datos actuales de la cita
$citaModel = new Cita();
$cita = $citaModel->obtenerPorId($id_cita);

if (!$cita) {
    echo "<h1>Cita no encontrada</h1>";
    exit;
}

// Separar Fecha y Hora
$fecha_hora = strtotime($cita['fecha_hora_inicio']);
$fecha_actual = date('Y-m-d', $fecha_hora);
$hora_actual = date('H:i', $fecha_hora);

// Cargar listas para los selects
$pacienteModel = new Paciente();
$pacientes = $pacienteModel->listarTodos();

$odoModel = new Odontologo();
$doctores = $odoModel->listarTodos();
?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single {
        height: 45px !important;
        border: 1px solid #ddd !important;
        display: flex;
        align-items: center;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
    }

    .clinical-form {
        background: white;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        border-top: 6px solid #f39c12;
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
</style>

<main class="main-content">
    <div class="page-header">
        <h1><i class="fas fa-edit"></i> Modificar Cita #<?php echo $id_cita; ?></h1>
        <a href="calendario.php" class="btn-primary" style="background-color: #7f8c8d;">
            <i class="fas fa-arrow-left"></i> Cancelar y Volver
        </a>
    </div>

    <div style="max-width: 900px; margin: 0 auto;">

        <form action="../../controllers/citaController.php" method="POST" class="clinical-form">
            <input type="hidden" name="id_cita" value="<?php echo $cita['id_cita']; ?>">

            <?php
            $estado = $cita['estado'];
            $color_fondo = '#3498db';

            if ($estado == 'ATENDIDA') {
                $color_fondo = '#2ecc71';
            } elseif ($estado == 'CANCELADA') {
                $color_fondo = '#e74c3c';
            } elseif ($estado == 'NO_ASISTIO') {
                $color_fondo = '#95a5a6';
            }
            ?>
            <div style="background-color: <?php echo $color_fondo; ?>; color: white; padding: 15px; border-radius: 8px; margin-bottom: 25px; text-align: center; font-size: 1.2rem; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.1); letter-spacing: 1px;">
                <i class="fas fa-info-circle"></i> ESTADO DE LA CITA: <?php echo $estado; ?>
            </div>

            <h3 class="section-title"><i class="fas fa-user-injured"></i> Paciente</h3>

            <div class="form-group">
                <label style="font-weight: bold;">Paciente:</label>
                <select name="id_paciente" class="form-control select2" required style="width: 100%;">
                    <?php foreach ($pacientes as $p): ?>
                        <option value="<?php echo $p['id_paciente']; ?>"
                            <?php echo ($p['id_paciente'] == $cita['id_paciente']) ? 'selected' : ''; ?>>
                            <?php echo $p['ci'] . ' - ' . $p['nombres'] . ' ' . $p['apellido_paterno'] . ' ' . $p['apellido_materno']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <br>

            <h3 class="section-title"><i class="fas fa-stethoscope"></i> Detalles de la Consulta</h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                <div>
                    <div class="form-group">
                        <label style="font-weight: bold;">Odontólogo:</label>
                        <select name="id_odontologo" class="form-control select2" required style="width: 100%;">
                            <?php foreach ($doctores as $d): ?>
                                <option value="<?php echo $d['id_odontologo']; ?>"
                                    <?php echo ($d['id_odontologo'] == $cita['id_odontologo']) ? 'selected' : ''; ?>>
                                    Dr. <?php echo $d['nombres'] . ' ' . $d['apellidos']; ?>
                                    (<?php echo $d['especialidad'] ? $d['especialidad'] : 'General'; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" style="margin-top: 20px;">
                        <label style="font-weight: bold;">Motivo:</label>
                        <textarea name="motivo" class="form-control" rows="4" required style="resize: none; background: #f9f9f9;"><?php echo $cita['motivo']; ?></textarea>
                    </div>
                </div>

                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <div class="form-group">
                        <label style="font-weight: bold; color: #f39c12;">Fecha:</label>
                        <input type="date" name="fecha" required class="form-control"
                            value="<?php echo $fecha_actual; ?>"
                            style="height: 45px;">
                    </div>

                    <div class="form-group" style="margin-top: 20px;">
                        <label style="font-weight: bold; color: #f39c12;">Hora:</label>
                        <input type="time" name="hora" required class="form-control"
                            value="<?php echo $hora_actual; ?>" step="1800"
                            style="height: 45px;">
                    </div>
                </div>
            </div>

            <div style="margin-top: 40px; border-top: 2px solid #eee; padding-top: 25px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">

                <div>
                    <?php if ($cita['estado'] == 'PROGRAMADA'): ?>
                        <button type="button" onclick="cancelarCita(<?php echo $id_cita; ?>)" style="background-color: #e74c3c; color: white; border: none; padding: 12px 25px; border-radius: 6px; cursor: pointer; font-weight: bold; display: inline-flex; align-items: center; gap: 8px; transition: 0.3s;">
                            <i class="fas fa-ban"></i> Cancelar Cita
                        </button>
                    <?php endif; ?>
                </div>

                <div style="display: flex; gap: 10px; align-items: center;">
                    <?php if ($cita['estado'] == 'PROGRAMADA'): ?>
                        <button type="submit" class="btn-primary" style="background-color: #f39c12; border:none; padding: 12px 30px; font-size: 1.1rem; border-radius: 6px; display: inline-flex; align-items: center; gap: 8px;">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
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
        $('.select2').select2({
            language: {
                noResults: () => "No se encontraron resultados"
            }
        });
    });

    function cancelarCita(id) {
        if (confirm('¿Está seguro de CANCELAR esta cita? Esta acción no se puede deshacer.')) {
            var formData = new FormData();
            formData.append('accion', 'cancelar');
            formData.append('id_cita', id);

            fetch('../../controllers/citaController.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        window.location.href = 'calendario.php?ok=cancelada';
                    } else {
                        alert('Error al cancelar: ' + (data.message || 'Error desconocido'));
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Error de conexión al intentar cancelar.');
                });
        }
    }
</script>

<?php require_once '../../includes/footer.php'; ?>