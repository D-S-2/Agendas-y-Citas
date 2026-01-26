<?php
// views/citas/editar.php
require_once '../../models/Cita.php';
require_once '../../models/Paciente.php';
require_once '../../models/Odontologo.php';

if (!isset($_GET['id'])) { header("Location: calendario.php"); exit; }

$citaModel = new Cita();
$cita = $citaModel->obtenerPorId($_GET['id']);

$pacienteModel = new Paciente();
$pacientes = $pacienteModel->listarTodos();

$odoModel = new Odontologo();
$doctores = $odoModel->listarTodos();

$fecha_actual = date('Y-m-d', strtotime($cita['fecha_hora_inicio']));
$hora_actual = date('H:i', strtotime($cita['fecha_hora_inicio']));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Cita</title>
    <link rel="stylesheet" href="../../assets/css/global.css">
    <link rel="stylesheet" href="../../assets/css/citas.css">
</head>
<body>
<main class="main-content">
    <div class="page-header">
        <h1>Editar Cita #<?php echo $cita['id_cita']; ?></h1>
        <a href="calendario.php" class="btn-primary">Cancelar</a>
    </div>

    <div style="max-width: 600px; margin: 20px auto; background: white; padding: 20px; border-radius: 8px;">
        <form action="../../controllers/citaController.php" method="POST">
            <input type="hidden" name="id_cita" value="<?php echo $cita['id_cita']; ?>">

            <div class="form-group">
                <label>Paciente:</label>
                <select name="id_paciente" class="form-control" required>
                    <?php foreach($pacientes as $p): ?>
                        <option value="<?php echo $p['id_paciente']; ?>" <?php echo ($p['id_paciente'] == $cita['id_paciente']) ? 'selected' : ''; ?>>
                            <?php echo $p['nombres'] . " " . $p['apellido_paterno']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Odontólogo:</label>
                <select name="id_odontologo" class="form-control" required>
                    <?php foreach($doctores as $d): ?>
                        <option value="<?php echo $d['id_odontologo']; ?>" <?php echo ($d['id_odontologo'] == $cita['id_odontologo']) ? 'selected' : ''; ?>>
                            Dr. <?php echo $d['nombres'] . " " . $d['apellidos']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Fecha:</label>
                <input type="date" name="fecha" class="form-control" value="<?php echo $fecha_actual; ?>" required>
            </div>

            <div class="form-group">
                <label>Hora:</label>
                <input type="time" name="hora" class="form-control" value="<?php echo $hora_actual; ?>" required>
            </div>

            <div class="form-group">
                <label>Motivo:</label>
                <textarea name="motivo" class="form-control" rows="3" required><?php echo $cita['motivo']; ?></textarea>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 15px;">Actualizar Cita</button>
        </form>
    </div>
</main>
</body>
</html>