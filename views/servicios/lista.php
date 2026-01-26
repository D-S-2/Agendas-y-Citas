<?php
$page_title = "Catálogo de Servicios";
$page_css = "servicios.css"; // ¡AQUÍ CARGAMOS EL NUEVO DISEÑO!

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../models/Servicio.php';

$servicioModel = new Servicio();
$servicios = $servicioModel->listarActivos();
?>

<main class="main-content">
    <div class="page-header">
        <h1>Servicios y Tarifas</h1>
    </div>

    <?php if(isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
            <?php echo $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?>
        </div>
    <?php endif; ?>

    <div class="catalog-wrapper">
        
        <div class="panel panel-form">
            <h3><i class="fas fa-plus-circle"></i> Nuevo Servicio</h3>
            <form action="../../controllers/servicioController.php" method="POST">
                <div class="form-group">
                    <label>Nombre del Tratamiento:</label>
                    <input type="text" name="nombre" class="form-control" placeholder="Ej: Limpieza Profunda" required>
                </div>
                <div class="form-group">
                    <label>Precio (Bs):</label>
                    <input type="number" step="0.01" name="precio" class="form-control" placeholder="0.00" required>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">Agregar al Catálogo</button>
            </form>
        </div>

        <div class="panel panel-list">
            <h3><i class="fas fa-list"></i> Lista de Precios</h3>
            
            <?php if(empty($servicios)): ?>
                <p style="color: #777; font-style: italic;">No hay servicios registrados aún.</p>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tratamiento</th>
                            <th>Precio</th>
                            <th style="text-align: center;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($servicios as $s): ?>
                        <tr>
                            <td><?php echo $s['nombre']; ?></td>
                            <td style="font-weight: bold; color: #27ae60;">Bs <?php echo number_format($s['precio'], 2); ?></td>
                            <td style="text-align: center;">
                                <form action="../../controllers/servicioController.php" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este servicio?');">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <input type="hidden" name="id_servicio" value="<?php echo $s['id_servicio']; ?>">
                                    <button type="submit" class="btn-delete" title="Eliminar">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

    </div>
</main>

<?php require_once '../../includes/footer.php'; ?>