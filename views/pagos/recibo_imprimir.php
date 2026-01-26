<?php
require_once '../../config/db.php';
require_once '../../models/Pago.php';

// Validar ID
if (!isset($_GET['id'])) {
    die("Error: Recibo no especificado.");
}

$id_recibo = $_GET['id'];
$pagoModel = new Pago();
$datos = $pagoModel->obtenerDatosRecibo($id_recibo);

if (!$datos) {
    die("Error: Recibo no encontrado.");
}

$info = $datos['info'];
$items = $datos['items'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo #<?php echo $info['numero_recibo']; ?></title>
    <link rel="stylesheet" href="../../assets/css/recibos.css">
</head>
<body>

    <div class="ticket-container">
        <div class="no-print action-buttons">
            <button onclick="window.print()" class="btn-print">🖨️ Imprimir</button>
            <a href="index.php" class="btn-back">⬅ Volver a Caja</a>
        </div>

        <div class="header">
            <h2>CLÍNICA "DR. MUELITAS"</h2>
            <p>Av. Principal #123, Ciudad</p>
            <p>NIT: 123456789</p>
            <hr>
            <h3>RECIBO DE CAJA</h3>
            <p><strong>Nro:</strong> <?php echo $info['numero_recibo']; ?></p>
            <p><strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($info['fecha_recibo'])); ?></p>
        </div>

        <div class="client-info">
            <p><strong>Paciente:</strong> <?php echo $info['nombres'] . ' ' . $info['apellido_paterno']; ?></p>
            <p><strong>CI:</strong> <?php echo $info['ci']; ?></p>
            <p><strong>Método Pago:</strong> <?php echo $info['metodo']; ?></p>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th align="right">Importe</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $item): ?>
                <tr>
                    <td><?php echo $item['nombre']; ?></td>
                    <td align="right"><?php echo number_format($item['precio_unitario'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td class="total-label">TOTAL BS</td>
                    <td class="total-amount"><?php echo number_format($info['total'], 2); ?></td>
                </tr>
            </tfoot>
        </table>

        <div class="footer">
            <p>¡Gracias por su visita!</p>
            <p>Servicio de Calidad</p>
        </div>
    </div>

</body>
</html>