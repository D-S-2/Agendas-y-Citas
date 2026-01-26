<?php
$page_title = "Reporte de Ingresos";
// Reusamos estilos de pagos y usuarios
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../models/Pago.php';

// Valores por defecto: Desde el día 1 del mes actual hasta hoy
$fecha_inicio = date('Y-m-01');
$fecha_fin = date('Y-m-d');
$resultados = [];
$total_ingresos = 0;

// Si se envió el filtro
if (isset($_GET['inicio']) && isset($_GET['fin'])) {
    $fecha_inicio = $_GET['inicio'];
    $fecha_fin = $_GET['fin'];
    
    $pagoModel = new Pago();
    $resultados = $pagoModel->reporteIngresos($fecha_inicio, $fecha_fin);

    // Calcular total
    foreach($resultados as $r) {
        $total_ingresos += $r['monto'];
    }
}
?>

<main class="main-content">
    <div class="page-header">
        <h1>Reportes Financieros</h1>
    </div>

    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 20px;">
        <form action="" method="GET" style="display: flex; gap: 20px; align-items: flex-end;">
            <div class="form-group" style="flex: 1;">
                <label>Desde:</label>
                <input type="date" name="inicio" class="form-control" value="<?php echo $fecha_inicio; ?>" required>
            </div>
            <div class="form-group" style="flex: 1;">
                <label>Hasta:</label>
                <input type="date" name="fin" class="form-control" value="<?php echo $fecha_fin; ?>" required>
            </div>
            <button type="submit" class="btn-primary" style="height: 42px; padding: 0 30px;">
                <i class="fas fa-filter"></i> Generar Reporte
            </button>
        </form>
    </div>

    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #f0f2f5;">
            <h3 style="margin: 0; color: #2c3e50;">Resultados</h3>
            <div style="background: #d4edda; color: #155724; padding: 10px 20px; border-radius: 5px; font-size: 1.2rem;">
                Total Generado: <strong>Bs <?php echo number_format($total_ingresos, 2); ?></strong>
            </div>
        </div>

        <?php if(empty($resultados)): ?>
            <p style="text-align: center; color: #999; padding: 20px;">
                No se encontraron ingresos en este rango de fechas.
            </p>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead style="background: #34495e; color: white;">
                        <tr>
                            <th>Fecha</th>
                            <th>Nro. Recibo</th>
                            <th>Paciente</th>
                            <th>Cajero</th>
                            <th>Método</th>
                            <th>Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($resultados as $row): ?>
                        <tr>
                            <td><?php echo date('d/m/Y H:i', strtotime($row['fecha_pago'])); ?></td>
                            <td><strong><?php echo $row['numero_recibo']; ?></strong></td>
                            <td><?php echo $row['nombres'] . ' ' . $row['apellido_paterno']; ?></td>
                            <td><?php echo $row['cajero'] ? $row['cajero'] : 'Sistema'; ?></td>
                            <td>
                                <span style="font-size: 0.85rem; padding: 2px 8px; background: #eee; border-radius: 4px;">
                                    <?php echo $row['metodo']; ?>
                                </span>
                            </td>
                            <td style="font-weight: bold; color: #27ae60;">
                                Bs <?php echo number_format($row['monto'], 2); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div style="margin-top: 20px; text-align: right;">
                <button onclick="window.print()" style="background: #7f8c8d; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
                    <i class="fas fa-print"></i> Imprimir Reporte
                </button>
            </div>
        <?php endif; ?>
    </div>
</main>

<style>
@media print {
    body * { visibility: hidden; }
    .main-content, .main-content * { visibility: visible; }
    .main-content { position: absolute; left: 0; top: 0; width: 100%; }
    form, button { display: none !important; } /* Ocultar filtros y botones al imprimir */
}
</style>

<?php require_once '../../includes/footer.php'; ?>