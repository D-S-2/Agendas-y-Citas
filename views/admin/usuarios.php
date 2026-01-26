<?php
$page_title = "Gestión de Personal";
$page_css = "usuarios.css"; // <--- IMPORTANTE: Cargamos el CSS aquí

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../models/Usuario.php';

// Validar que SOLO ADMIN entre aquí
if($_SESSION['rol'] != 'ADMIN') {
    echo "<script>window.location.href='dashboard.php';</script>";
    exit;
}

$userModel = new Usuario();
$usuarios = $userModel->listarTodos();
$roles = $userModel->obtenerRoles();
?>

<main class="main-content">
    <div class="page-header">
        <h1>Usuarios del Sistema</h1>
    </div>
    
    <?php if(isset($_GET['mensaje'])): ?>
        <div class="alert alert-success" style="background:#d4edda; color:#155724; padding:10px; margin-bottom:15px; border-radius:5px;">
            <i class="fas fa-check-circle"></i> Usuario creado correctamente.
        </div>
    <?php endif; ?>
    <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-danger" style="background:#f8d7da; color:#721c24; padding:10px; margin-bottom:15px; border-radius:5px;">
            <i class="fas fa-exclamation-triangle"></i> Error al crear usuario.
        </div>
    <?php endif; ?>

    <div style="display: flex; gap: 20px; align-items: flex-start; flex-wrap: wrap;">
        
        <div style="flex: 1; min-width: 300px;" class="user-card"> <h3><i class="fas fa-user-plus"></i> Nuevo Usuario</h3>
            
            <form action="../../controllers/usuarioController.php" method="POST">
                
                <div class="form-group">
                    <label>Rol de Usuario:</label>
                    <select name="id_rol" id="selectRol" class="form-control" required onchange="verificarRol()">
                        <option value="">Seleccione...</option>
                        <?php foreach($roles as $r): ?>
                            <option value="<?php echo $r['id_rol']; ?>"><?php echo $r['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div class="form-group">
                        <label>Nombres:</label>
                        <input type="text" name="nombres" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Apellidos:</label>
                        <input type="text" name="apellidos" class="form-control" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Usuario (Login):</label>
                    <input type="text" name="usuario" class="form-control" placeholder="Ej: drperez" required>
                </div>
                <div class="form-group">
                    <label>Contraseña:</label>
                    <input type="password" name="password" class="form-control" placeholder="******" required>
                </div>

                <div class="form-group">
                    <label>Email (Opcional):</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="form-group">
                    <label>Teléfono:</label>
                    <input type="text" name="telefono" class="form-control">
                </div>

                <div id="camposDoctor" style="display: none;">
                    <h4><i class="fas fa-stethoscope"></i> Datos Profesionales</h4>
                    <div class="form-group">
                        <label>Matrícula Profesional:</label>
                        <input type="text" name="matricula" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Especialidad:</label>
                        <input type="text" name="especialidad" class="form-control" placeholder="Ej: Ortodoncia">
                    </div>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">
                    <i class="fas fa-save"></i> Crear Usuario
                </button>
            </form>
        </div>

        <div style="flex: 2; min-width: 400px;" class="user-card"> <h3><i class="fas fa-users-cog"></i> Personal Registrado</h3>
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Nombre Completo</th>
                            <th>Rol</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($usuarios as $u): ?>
                        <tr>
                            <td style="font-weight: bold; color: #2980b9;"><?php echo $u['usuario']; ?></td>
                            <td><?php echo $u['nombres'] . ' ' . $u['apellidos']; ?></td>
                            <td>
                                <span class="role-tag"><?php echo $u['rol']; ?></span>
                            </td>
                            <td>
                                <?php if($u['activo']): ?>
                                    <span class="badge badge-active">Activo</span>
                                <?php else: ?>
                                    <span class="badge badge-inactive">Inactivo</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</main>

<script>
function verificarRol() {
    var select = document.getElementById('selectRol');
    var camposDoc = document.getElementById('camposDoctor');
    var textoSeleccionado = select.options[select.selectedIndex].text;

    // Lógica simple: Si contiene la palabra "DOCTOR" mostramos los campos extra
    if (textoSeleccionado.toUpperCase().includes("DOCTOR")) {
        camposDoc.style.display = "block";
    } else {
        camposDoc.style.display = "none";
        // Opcional: limpiar campos si se oculta
    }
}
</script>

<?php require_once '../../includes/footer.php'; ?>