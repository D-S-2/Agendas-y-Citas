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
    <title>Nueva Cita - Gestión</title>
    <link rel="stylesheet" href="../../assets/css/global.css">
    <link rel="stylesheet" href="../../assets/css/citas.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body style="background:#f4f6f9;">
<main class="main-content" style="padding:20px;">
    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h1><i class="fas fa-calendar-plus"></i> Gestión de Citas</h1>
        <a href="calendario.php" class="btn-primary" style="background:#7f8c8d; text-decoration:none; padding:10px 20px; border-radius:5px; color:white;">
            <i class="fas fa-arrow-left"></i> VOLVER AL CALENDARIO
        </a>
    </div>

    <div style="max-width: 850px; margin: 0 auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); border-top: 6px solid #3498db;">
        <form action="../../controllers/citaController.php" method="POST">
            <h3 style="color:#2c3e50; border-bottom:2px solid #f0f2f5; padding-bottom:10px; margin-bottom:25px; font-size:1.1rem; text-transform:uppercase;">
                <i class="fas fa-user-injured"></i> INFORMACIÓN DEL PACIENTE
            </h3>
            
            <div class="form-group" style="margin-bottom:20px;">
                <label style="font-weight:bold; display:block; margin-bottom:5px;">Buscar Paciente (Nombre o Cédula):</label>
                <select name="id_paciente" class="form-control select2" required style="width: 100%;">
                    <option value="">Buscar paciente...</option>
                    <?php foreach($pacientes as $p): ?>
                        <option value="<?php echo $p['id_paciente']; ?>">
                            <?php echo $p['ci'] . " - " . $p['nombres'] . " " . $p['apellido_paterno']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <h3 style="color:#2c3e50; border-bottom:2px solid #f0f2f5; padding-bottom:10px; margin-top:30px; margin-bottom:25px; font-size:1.1rem; text-transform:uppercase;">
                <i class="fas fa-stethoscope"></i> DATOS DE LA CONSULTA
            </h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                <div>
                    <div class="form-group" style="margin-bottom:15px;">
                        <label style="font-weight:bold;">Odontólogo Asignado:</label>
                        <select name="id_odontologo" class="form-control select2" required style="width: 100%;">
                            <option value="">Seleccione profesional...</option>
                            <?php foreach($doctores as $d): ?>
                                <option value="<?php echo $d['id_odontologo']; ?>">Dr. <?php echo $d['nombres'] . " " . $d['apellidos']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label style="font-weight:bold;">Motivo de la visita:</label>
                        <textarea name="motivo" class="form-control" rows="4" required style="width:100%; border:1px solid #ddd; border-radius:5px; padding:10px; resize:none; background:#f9f9f9;" placeholder="Ej: Dolor agudo..."></textarea>
                    </div>
                </div>

                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <div class="form-group" style="margin-bottom:15px;">
                        <label style="font-weight:bold; color:#3498db;">Fecha de Cita:</label>
                        <input type="date" name="fecha" class="form-control" value="<?php echo date('Y-m-d'); ?>" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                    </div>

                    <div class="form-group">
                        <label style="font-weight:bold; color:#3498db;">Hora Estimada:</label>
                        <input type="time" name="hora" class="form-control" value="09:00" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                    </div>
                    <p style="font-size:0.85rem; color:#666; margin-top:15px;"><i class="fas fa-info-circle"></i> Duración estándar: 30 min.</p>
                </div>
            </div>

            <div style="margin-top: 30px; text-align: right; border-top: 1px solid #eee; padding-top: 20px;">
                <button type="submit" class="btn-primary" style="background:#3498db; color:white; border:none; padding:12px 40px; font-size:1.1rem; border-radius:50px; cursor:pointer; font-weight:bold;">
                    <i class="fas fa-check"></i> CONFIRMAR AGENDAMIENTO
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
    });
</script>
</body>
</html>