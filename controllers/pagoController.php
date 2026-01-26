<?php
session_start();
require_once __DIR__ . '/../models/Pago.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pagoModel = new Pago();

    $id_atencion = $_POST['id_atencion'];
    $monto = $_POST['monto'];
    $metodo = $_POST['metodo']; // EFECTIVO, QR, etc.
    $id_usuario = $_SESSION['id_usuario'];

    // Registrar pago
    $id_recibo = $pagoModel->registrarPago($id_atencion, $monto, $metodo, $id_usuario);

    if ($id_recibo) {
        // Redirigir a la vista de IMPRIMIR RECIBO
        header("Location: ../views/pagos/recibo_imprimir.php?id=$id_recibo");
    } else {
        // Error
        header("Location: ../views/pagos/index.php?error=pagofallido");
    }
}
?>