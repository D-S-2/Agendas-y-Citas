<?php
// views/citas/agenda_dia.php
require_once '../../models/Cita.php';
require_once '../../models/Odontologo.php';

$citaModel = new Cita();
$odoModel = new Odontologo();

// Obtener fecha y filtro de doctor si existen
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');
$id_odontologo = isset($_GET['id_odontologo']) && !empty($_GET['id_odontologo']) ? $_GET['id_odontologo'] : null;

$citas = $citaModel->obtenerCitasDelDia($fecha, $id_odontologo);
$doctores = $odoModel->listarTodos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agenda del Día</title>
    <link rel="stylesheet" href="../../assets/css/global.css">
    <link rel="stylesheet" href="../../assets/css/citas.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body style="background:#f4f6f9;">
<main class="main-content" style="padding:20px;">
    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h1><i class="fas fa-calendar-day"></i> Agenda del Día</h1>
        <div style="display:flex; gap:10px;">
            <a href="calendario.php" class="btn-primary" style="background:#3498db; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;"><i class="far fa-calendar-alt"></i> VER CALENDARIO</a>
            <a href="nueva.php" class="btn-primary" style="background:#3498db; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;"><i class="fas fa-plus"></i> NUEVA CITA</a>
        </div>
    </div>

    <div style="background:white; padding:20px; border-radius:8px; margin-bottom:20px; box-shadow:0 2px 5px rgba(0,0,0,0.05);">
        <form action="" method="GET" style="display:flex; gap:15px; align-items:flex-end; flex-wrap:wrap;">
            <div style="flex:1; min-width:200px;">
                <label style="display:block; margin-bottom:5px; font-weight:bold; color:#555;">Fecha:</label>
                <input type="date" name="fecha" class="form-control" value="<?php echo $fecha; ?>" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:5px;">
            </div>
            <div style="flex:1; min-width:200px;">
                <label style="display:block; margin-bottom:5px; font-weight:bold; color:#555;">Doctor:</label>
                <select name="id_odontologo" class="form-control" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:5px;">
                    <option value="">Todos los doctores</option>
                    <?php foreach($doctores as $d): ?>
                        <option value="<?php echo $d['id_odontologo']; ?>" <?php echo ($id_odontologo == $d['id_odontologo']) ? 'selected' : ''; ?>>
                            Dr. <?php echo $d['nombres'] . ' ' . $d['apellidos']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn-primary" style="background:#3498db; color:white; border:none; padding:10px 25px; border-radius:5px; cursor:pointer; height:40px;">
                <i class="fas fa-search"></i> BUSCAR
            </button>
        </form>
    </div>

    <div style="background:white; border-radius:8px; overflow:hidden; box-shadow:0 2px 5px rgba(0,0,0,0.05);">
        <div style="background:#34495e; color:white; padding:15px; display:flex; justify-content:space-between; align-items:center;">
            <h3 style="margin:0;"><i class="far fa-clock"></i> Citas Programadas</h3>
            <span style="background:rgba(255,255,255,0.2); padding:2px 12px; border-radius:20px; font-size:0.9rem;">
                <?php echo count($citas); ?> Cita(s)
            </span>
        </div>

        <?php if(empty($citas)): ?>
            <div style="padding:60px; text-align:center; color:#999;">
                <i class="far fa-calendar-times" style="font-size:3rem; margin-bottom:15px; opacity:0.3;"></i>
                <p>No hay citas programadas para este día.</p>
            </div>
        <?php else: ?>
            <div class="agenda-lista">
                <?php foreach($citas as $c): ?>
                    <div style="padding:20px; border-bottom:1px solid #eee; display:flex; justify-content:space-between; align-items:center;">
                        <div style="display:flex; gap:20px; align-items:center;">
                            <div style="text-align:center; min-width:80px; border-right:2px solid #3498db; padding-right:15px;">
                                <span style="font-size:1.2rem; font-weight:bold; color:#2c3e50;"><?php echo date('H:i', strtotime($c['fecha_hora_inicio'])); ?></span>
                            </div>
                            <div>
                                <h4 style="margin:0; color:#2c3e50;"><?php echo $c['paciente']; ?></h4>
                                <p style="margin:5px 0 0; font-size:0.9rem; color:#666;">
                                    <i class="fas fa-stethoscope"></i> Dr. <?php echo $c['odontologo']; ?> | 
                                    <i class="fas fa-comment-medical"></i> <?php echo $c['motivo']; ?>
                                </p>
                            </div>
                        </div>
                        <span style="background:#3498db; color:white; padding:5px 15px; border-radius:20px; font-size:0.8rem; font-weight:bold;">
                            <?php echo $c['estado']; ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div style="text-align:center; margin-top:20px;">
        <button onclick="window.print()" style="background:#7f8c8d; color:white; border:none; padding:12px 30px; border-radius:5px; cursor:pointer; font-weight:bold;">
            <i class="fas fa-print"></i> IMPRIMIR AGENDA
        </button>
    </div>
</main>
</body>
</html>