<?php
session_start();
require_once __DIR__ . '/../models/Atencion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $atencionModel = new Atencion();
    
    $id_cita = $_POST['id_cita'];
    $diagnostico = $_POST['diagnostico'];
    
    // Procesar los servicios seleccionados (checkboxes)
    // El formulario enviará un array 'servicios' con los IDs seleccionados
    // y tendremos que buscar sus precios (o enviarlos desde el form ocultos).
    // Para simplificar y ser más seguros, aquí solo recibimos IDs y cantidades, 
    // pero en este ejemplo simple asumiremos que el form envía:
    // servicios[0][id] y servicios[0][precio]
    
    $servicios_seleccionados = [];
    if(isset($_POST['servicios'])){
        foreach($_POST['servicios'] as $serv_id) {
            // El precio viene en un input hidden asociado al ID
            $precio = $_POST['precio_' . $serv_id];
            $servicios_seleccionados[] = [
                'id' => $serv_id,
                'precio' => $precio
            ];
        }
    }

    if (empty($servicios_seleccionados)) {
        // Error: No seleccionó servicios
        header("Location: ../views/citas/atender.php?id_cita=$id_cita&error=noservice");
        exit;
    }

    // Guardar todo
    $resultado = $atencionModel->registrar($id_cita, $diagnostico, $servicios_seleccionados);

    if ($resultado) {
        // Éxito: Redirigir a la agenda o al recibo de pago
        // Vamos a redirigir a la Agenda con éxito
        header("Location: ../views/citas/calendario.php?mensaje=AtencionRegistrada");
    } else {
        header("Location: ../views/citas/atender.php?id_cita=$id_cita&error=db");
    }
}
?>