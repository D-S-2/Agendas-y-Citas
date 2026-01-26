<?php
$page_title = "Historia Clínica";
$page_css = "historia.css";

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../models/Paciente.php';
require_once '../../models/Atencion.php';

$paciente = null;
$historial = [];
$mensaje = "";

// Si enviaron el formulario de búsqueda
if (isset($_GET['buscar_ci'])) {
    $ci = trim($_GET['buscar_ci']);
    
    $pacienteModel = new Paciente();
    $paciente = $pacienteModel->buscarPorCI($ci);

    if ($paciente) {
        // Si existe el paciente, buscamos su historia
        $atencionModel = new Atencion();
        $historial = $atencionModel->obtenerHistorialPorPaciente($paciente['id_paciente']);
    } else {
        $mensaje = "No se encontró ningún paciente con el CI: " . $ci;
    }
}
?>

<main class="main-content">
    <div class="page-header">
        <h1>Historia Clínica Digital</h1>
    </div>

    <div class="search-box">
        <form action="" method="GET" style="width: 100%; display: flex; gap: 10px;">
            <input type="text" name="buscar_ci" class="search-input" placeholder="Ingrese el C.I. del paciente..." value="<?php echo isset($_GET['buscar_ci']) ? $_GET['buscar_ci'] : ''; ?>" required>
            <button type="submit" class="btn-primary"><i class="fas fa-search"></i> Buscar</button>
        </form>
    </div>

    <?php if($mensaje): ?>
        <div class="alert alert-danger" style="background:#f8d7da; color:#721c24; padding:15px; border-radius:5px;">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <?php if($paciente): ?>
        
        <div class="patient-card">
            <div>
                <h2><?php echo $paciente['nombres'] . ' ' . $paciente['apellido_paterno'] . ' ' . $paciente['apellido_materno']; ?></h2>
                <p><strong>CI:</strong> <?php echo $paciente['ci']; ?> | <strong>Edad:</strong> 
                <?php 
                    $nac = new DateTime($paciente['fecha_nacimiento']);
                    $hoy = new DateTime();
                    echo $hoy->diff($nac)->y . " años";
                ?>
                </p>
            </div>
            <div style="font-size: 3rem; opacity: 0.5;">
                <i class="fas fa-user-circle"></i>
            </div>
        </div>

        <?php if(empty($historial)): ?>
            <div style="text-align: center; color: #777; padding: 40px;">
                <i class="fas fa-folder-open" style="font-size: 3rem; margin-bottom: 10px;"></i>
                <p>Este paciente aún no tiene registros médicos.</p>
            </div>
        <?php else: ?>
            <div class="timeline">
                <?php foreach($historial as $h): ?>
                <div class="timeline-item">
                    <span class="timeline-date">
                        <i class="far fa-clock"></i> <?php echo date('d/m/Y - h:i A', strtotime($h['fecha_atencion'])); ?>
                    </span>
                    
                    <div class="timeline-doctor">
                        Atendido por: <strong>Dr. <?php echo $h['doctor']; ?></strong>
                    </div>

                    <div class="timeline-diag">
                        <strong>Diagnóstico / Notas:</strong><br>
                        <?php echo nl2br($h['diagnostico']); ?>
                    </div>

                    <div class="timeline-treatments">
                        <i class="fas fa-tooth"></i> Tratamientos: <?php echo $h['tratamientos']; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    <?php endif; ?>

</main>

<?php require_once '../../includes/footer.php'; ?>