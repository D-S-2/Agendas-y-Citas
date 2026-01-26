<?php
$page_title = "Caja y Facturación";
$page_css = "pagos.css"; // <--- IMPORTANTE: Cargar el CSS nuevo

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../models/Pago.php';

$pagoModel = new Pago();
$pendientes = $pagoModel->obtenerPendientes();
?>

<main class="main-content">
    <div class="page-header">
        <h1>Caja - Cobros Pendientes</h1>
    </div>

    <div class="card-caja">
        <?php if(empty($pendientes)): ?>
            <div class="empty-state">
                <i class="fas fa-check-circle" style="font-size: 3rem; color: #2ecc71; margin-bottom: 10px;"></i>
                <p>¡Excelente! No hay cobros pendientes por ahora.</p>
            </div>
        <?php else: ?>
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Fecha Atención</th>
                        <th>Paciente</th>
                        <th>Cédula (CI)</th>
                        <th>Monto a Pagar</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($pendientes as $p): ?>
                    <tr>
                        <td>
                            <i class="far fa-calendar-alt" style="color:#aaa"></i> 
                            <?php echo date('d/m/Y H:i', strtotime($p['fecha_atencion'])); ?>
                        </td>
                        <td>
                            <strong style="color: #2c3e50;"><?php echo $p['nombres'] . ' ' . $p['apellido_paterno']; ?></strong>
                        </td>
                        <td><?php echo $p['ci']; ?></td>
                        <td>
                            <span class="precio-deuda">Bs <?php echo number_format($p['total_servicios'], 2); ?></span>
                        </td>
                        <td>
                            <a href="cobrar.php?id_atencion=<?php echo $p['id_atencion']; ?>&monto=<?php echo $p['total_servicios']; ?>" 
                               class="btn-cobrar">
                                <i class="fas fa-cash-register"></i> Cobrar
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</main>
<?php require_once '../../includes/footer.php'; ?>