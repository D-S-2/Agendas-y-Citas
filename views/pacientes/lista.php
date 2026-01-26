<?php
// Configuración de la página
$page_title = "Gestión de Pacientes";
$page_css = "pacientes.css"; // ¡Cargamos el CSS específico!

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../models/Paciente.php';

// Obtener datos
$pacienteModel = new Paciente();
$pacientes = $pacienteModel->listarTodos();
?>

<main class="main-content">
    
    <?php if(isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?php echo $_SESSION['tipo_mensaje']; ?>" style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
            <?php echo $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?>
        </div>
    <?php endif; ?>

    <div class="page-header">
        <h1>Pacientes</h1>
        <a href="formulario.php" class="btn-primary"><i class="fas fa-plus"></i> Nuevo Paciente</a>
    </div>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>CI</th>
                    <th>Nombre Completo</th>
                    <th>Edad</th>
                    <th>Teléfono</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($pacientes as $p): ?>
                <?php 
                    // Calcular edad simple
                    $edad = "N/A";
                    if($p['fecha_nacimiento']){
                        $nacimiento = new DateTime($p['fecha_nacimiento']);
                        $hoy = new DateTime();
                        $edad = $hoy->diff($nacimiento)->y . " años";
                    }
                ?>
                <tr>
                    <td><?php echo $p['ci']; ?></td>
                    <td><?php echo $p['nombres'] . ' ' . $p['apellido_paterno'] . ' ' . $p['apellido_materno']; ?></td>
                    <td><?php echo $edad; ?></td>
                    <td><?php echo $p['telefono']; ?></td>
                    <td>
                        <a href="formulario.php?id=<?php echo $p['id_paciente']; ?>" class="btn-action btn-edit" title="Editar"><i class="fas fa-edit"></i></a>
                        <a href="#" class="btn-action btn-history" title="Historia Clínica"><i class="fas fa-file-medical"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</main>

<?php require_once '../../includes/footer.php'; ?>