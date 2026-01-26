<?php
require_once '../../models/Paciente.php';
require_once '../../models/Odontologo.php';
$pacientes = (new Paciente())->listarTodos();
$doctores = (new Odontologo())->listarTodos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Cita</title>
    <link rel="stylesheet" href="../../assets/css/global.css">
    <link rel="stylesheet" href="../../assets/css/citas.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body>
<main class="main-content">
    <div class="page-header">
        <h1><i class="fas fa-calendar-plus"></i> Gestión de Citas</h1>
        <a href="calendario.php" class="btn-primary" style="background:#7f8c8d;">&larr; Volver</a>
    </div>

    <div style="max-width: 800px; margin: 20px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
        <form action="../../controllers/citaController.php" method="POST">
            <h3 style="border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">INFORMACIÓN DEL PACIENTE</h3>
            <div class="form-group">
                <label>Buscar Paciente (Nombre o CI):</label>
                <select name="id_paciente" class="form-control select2" required style="width: 100%;">
                    <option value="">Seleccione...</option>
                    <?php foreach($pacientes as $p): ?>
                        <option value="<?php echo $p['id_paciente']; ?>"><?php echo $p['ci'] . " - " . $p['nombres'] . " " . $p['apellido_paterno']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <h3 style="border-bottom: 2px solid #eee; padding-bottom: 10px; margin-top: 30px; margin-bottom: 20px;">DATOS DE LA CONSULTA</h3>
            <div class="form-group">
                <label>Odontólogo:</label>
                <select name="id_odontologo" class="form-control select2" required style="width: 100%;">
                    <?php foreach($doctores as $d): ?>
                        <option value="<?php echo $d['id_odontologo']; ?>">Dr. <?php echo $d['nombres']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: flex; gap: 20px;">
                <div class="form-group" style="flex: 1;">
                    <label>Fecha:</label>
                    <input type="date" name="fecha" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Hora:</label>
                    <input type="time" name="hora" class="form-control" value="09:00" required>
                </div>
            </div>

            <div class="form-group">
                <label>Motivo:</label>
                <textarea name="motivo" class="form-control" rows="3" required></textarea>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; padding: 15px; border-radius: 50px; margin-top: 20px;">
                <i class="fas fa-check"></i> CONFIRMAR AGENDAMIENTO
            </button>
        </form>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>
</body>
</html>