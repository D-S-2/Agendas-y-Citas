<?php
// views/citas/nueva.php
require_once '../../models/Paciente.php';
require_once '../../models/Odontologo.php';

$pacienteModel = new Paciente();
$pacientes = $pacienteModel->listarTodos();

$odoModel = new Odontologo();
$doctores = $odoModel->listarTodos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Cita</title>
    <link rel="stylesheet" href="../../assets/css/global.css">
    <link rel="stylesheet" href="../../assets/css/citas.css">
</head>
<body>
<main class="main-content">
    <div class="page-header">
        <h1><i class="fas fa-calendar-plus"></i> Nueva Cita</h1>
        <a href="calendario.php" class="btn-primary">Volver al Calendario</a>
    </div>

    <div style="max-width: 600px; margin: 20px auto; background: white; padding: 20px; border-radius: 8px;">
        <form action="../../controllers/citaController.php" method="POST">
            <div class="form-group">
                <label>Paciente:</label>
                <select name="id_paciente" class="form-control" required>
                    <option value="">Seleccione un paciente...</option>
                    <?php foreach($pacientes as $p): ?>
                        <option value="<?php echo $p['id_paciente']; ?>"><?php echo $p['ci'] . " - " . $p['nombres'] . " " . $p['apellido_paterno']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Odontólogo:</label>
                <select name="id_odontologo" class="form-control" required>
                    <option value="">Seleccione un doctor...</option>
                    <?php foreach($doctores as $d): ?>
                        <option value="<?php echo $d['id_odontologo']; ?>">Dr. <?php echo $d['nombres'] . " " . $d['apellidos']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Fecha:</label>
                <input type="date" name="fecha" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div class="form-group">
                <label>Hora:</label>
                <input type="time" name="hora" class="form-control" value="09:00" required>
            </div>

            <div class="form-group">
                <label>Motivo:</label>
                <textarea name="motivo" class="form-control" rows="3" required></textarea>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 15px;">Guardar Cita</button>
        </form>
    </div>
</main>
</body>
</html>