<?php
$page_title = "Registrar Paciente";
$page_css = "pacientes.css";
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../models/Paciente.php';

// Variables por defecto
$p = null;
$titulo = "Nuevo Paciente";

// Si recibimos un ID, es edición
if (isset($_GET['id'])) {
    $pacienteModel = new Paciente();
    $p = $pacienteModel->obtenerPorId($_GET['id']);
    $titulo = "Editar Paciente";
}
?>

<main class="main-content">
    <div class="page-header">
        <h1><?php echo $titulo; ?></h1>
        <a href="lista.php" style="color: #e74c3c; text-decoration: none;">&larr; Cancelar y Volver</a>
    </div>

    <div class="table-container"> <form action="../../controllers/pacienteController.php" method="POST">
            
            <input type="hidden" name="id_paciente" value="<?php echo $p ? $p['id_paciente'] : ''; ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label>Cédula de Identidad (CI)</label>
                    <input type="text" name="ci" value="<?php echo $p ? $p['ci'] : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Nombres</label>
                    <input type="text" name="nombres" value="<?php echo $p ? $p['nombres'] : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label>Apellido Paterno</label>
                    <input type="text" name="apellido_paterno" value="<?php echo $p ? $p['apellido_paterno'] : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label>Apellido Materno</label>
                    <input type="text" name="apellido_materno" value="<?php echo $p ? $p['apellido_materno'] : ''; ?>">
                </div>

                <div class="form-group">
                    <label>Fecha de Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" value="<?php echo $p ? $p['fecha_nacimiento'] : ''; ?>">
                </div>

                <div class="form-group">
                    <label>Sexo</label>
                    <select name="sexo">
                        <option value="M" <?php if($p && $p['sexo']=='M') echo 'selected'; ?>>Masculino</option>
                        <option value="F" <?php if($p && $p['sexo']=='F') echo 'selected'; ?>>Femenino</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="text" name="telefono" value="<?php echo $p ? $p['telefono'] : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>Dirección</label>
                    <input type="text" name="direccion" value="<?php echo $p ? $p['direccion'] : ''; ?>">
                </div>
            </div>

            <div class="form-footer">
                <button type="submit" class="btn-save"><i class="fas fa-save"></i> Guardar Datos</button>
            </div>
        </form>
    </div>
</main>

<?php require_once '../../includes/footer.php'; ?>