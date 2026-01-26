<?php
session_start();
require_once __DIR__ . '/../models/Servicio.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servicioModel = new Servicio();
    
    // Validar si es para crear o eliminar
    if (isset($_POST['accion']) && $_POST['accion'] == 'eliminar') {
        $id = $_POST['id_servicio'];
        $servicioModel->eliminar($id);
    } else {
        // Crear
        $nombre = $_POST['nombre'];
        $precio = $_POST['precio'];
        
        if($servicioModel->crear($nombre, $precio)){
            $_SESSION['mensaje'] = "Servicio agregado correctamente";
        }
    }
    
    // Volver a la lista
    header("Location: ../views/servicios/lista.php");
    exit;
}
?>