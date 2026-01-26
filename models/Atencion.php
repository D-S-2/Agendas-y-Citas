<?php
require_once __DIR__ . '/../config/db.php';

class Atencion {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // REGISTRAR ATENCIÓN COMPLETA (Transacción)
    public function registrar($id_cita, $diagnostico, $servicios) {
        try {
            // 1. Iniciar transacción (para que si algo falla, no se guarde nada)
            $this->conn->beginTransaction();

            // 2. Insertar cabecera de atención
            $sqlAtencion = "INSERT INTO atenciones (id_cita, diagnostico) VALUES (:cita, :diag)";
            $stmt = $this->conn->prepare($sqlAtencion);
            $stmt->bindParam(":cita", $id_cita);
            $stmt->bindParam(":diag", $diagnostico);
            $stmt->execute();
            $id_atencion = $this->conn->lastInsertId(); // Guardamos el ID generado

            // 3. Insertar detalles (Servicios realizados)
            $sqlDetalle = "INSERT INTO detalle_atencion (id_atencion, id_servicio, cantidad, precio_unitario, subtotal) 
                           VALUES (:id_at, :id_serv, 1, :precio, :subtotal)";
            $stmtDetalle = $this->conn->prepare($sqlDetalle);

            foreach($servicios as $serv) {
                // $serv trae 'id' y 'precio' desde el formulario
                $subtotal = $serv['precio'] * 1; 
                
                $stmtDetalle->bindParam(":id_at", $id_atencion);
                $stmtDetalle->bindParam(":id_serv", $serv['id']);
                $stmtDetalle->bindParam(":precio", $serv['precio']);
                $stmtDetalle->bindParam(":subtotal", $subtotal);
                $stmtDetalle->execute();
            }

            // 4. Actualizar estado de la CITA a 'ATENDIDA'
            $sqlCita = "UPDATE citas SET estado = 'ATENDIDA' WHERE id_cita = :id";
            $stmtCita = $this->conn->prepare($sqlCita);
            $stmtCita->bindParam(":id", $id_cita);
            $stmtCita->execute();

            // 5. Confirmar todo
            $this->conn->commit();
            return $id_atencion; // Devolvemos ID para luego generar el pago

        } catch (Exception $e) {
            $this->conn->rollBack(); // Deshacer cambios si hubo error
            return false;
        }
    }
    // OBTENER HISTORIAL COMPLETO DE UN PACIENTE
    public function obtenerHistorialPorPaciente($id_paciente) {
        // Esta consulta une: Atención -> Cita -> Doctor -> Servicios
        // Usa GROUP_CONCAT para poner todos los servicios (Limpieza, Extracción) en una sola línea de texto
        $sql = "SELECT 
                    a.id_atencion,
                    a.fecha_atencion, 
                    a.diagnostico,
                    CONCAT(u.nombres, ' ', u.apellidos) as doctor,
                    GROUP_CONCAT(s.nombre SEPARATOR ', ') as tratamientos
                FROM atenciones a
                INNER JOIN citas c ON a.id_cita = c.id_cita
                INNER JOIN odontologos o ON c.id_odontologo = o.id_odontologo
                INNER JOIN usuarios u ON o.id_usuario = u.id_usuario
                LEFT JOIN detalle_atencion d ON a.id_atencion = d.id_atencion
                LEFT JOIN servicios s ON d.id_servicio = s.id_servicio
                WHERE c.id_paciente = :id
                GROUP BY a.id_atencion
                ORDER BY a.fecha_atencion DESC"; // Lo más reciente primero

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id_paciente);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>