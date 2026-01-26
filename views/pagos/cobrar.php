<?php
$page_title = "Realizar Cobro";
// Usamos estilos globales, no hace falta uno específico por ahora
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';

if(!isset($_GET['id_atencion'])) {
    echo "<script>window.location.href='index.php';</script>";
    exit;
}

$id_atencion = $_GET['id_atencion'];
$monto = $_GET['monto'];
?>

<main class="main-content">
    <div style="max-width: 500px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
        <h2 style="text-align: center; color: #2c3e50; margin-bottom: 20px;">Registrar Pago</h2>
        
        <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; text-align: center; margin-bottom: 20px;">
            <p>Total a Pagar</p>
            <h1 style="color: #27ae60; margin: 0;">Bs <?php echo number_format($monto, 2); ?></h1>
        </div>

        <form action="../../controllers/pagoController.php" method="POST">
            <input type="hidden" name="id_atencion" value="<?php echo $id_atencion; ?>">
            <input type="hidden" name="monto" value="<?php echo $monto; ?>">

            <div class="form-group">
                <label>Método de Pago:</label>
                <select name="metodo" class="form-control" required style="font-size: 1.1rem; padding: 10px;">
                    <option value="EFECTIVO">Efectivo</option>
                    <option value="TARJETA">Tarjeta de Débito/Crédito</option>
                    <option value="TRANSFERENCIA">QR / Transferencia</option>
                </select>
            </div>

            <div style="margin-top: 30px;">
                <button type="submit" class="btn-primary" style="width: 100%; font-size: 1.2rem; padding: 15px;">
                    <i class="fas fa-check-circle"></i> Confirmar Pago
                </button>
                <a href="index.php" style="display: block; text-align: center; margin-top: 15px; color: #7f8c8d; text-decoration: none;">Cancelar</a>
            </div>
        </form>
    </div>
</main>

<?php require_once '../../includes/footer.php'; ?>