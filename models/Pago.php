<?php
require_once __DIR__ . '/../config/db.php';

class Pago {
    private $conn;
    private $table = "pagos";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // 1. LISTAR DEUDAS PENDIENTES
    // Busca atenciones que NO tengan un pago registrado aún
    public function obtenerPendientes() {
        $sql = "SELECT 
                    a.id_atencion, 
                    a.fecha_atencion,
                    p.nombres, p.apellido_paterno, p.ci,
                    v.total_servicios
                FROM atenciones a
                INNER JOIN citas c ON a.id_cita = c.id_cita
                INNER JOIN pacientes p ON c.id_paciente = p.id_paciente
                INNER JOIN vw_atencion_total v ON a.id_atencion = v.id_atencion
                LEFT JOIN pagos pg ON a.id_atencion = pg.id_atencion
                WHERE pg.id_pago IS NULL"; // Solo los que no tienen pago

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. REGISTRAR UN PAGO Y GENERAR RECIBO
    public function registrarPago($id_atencion, $monto, $metodo, $id_usuario) {
        try {
            $this->conn->beginTransaction();

            // A. Insertar el Pago
            $sql = "INSERT INTO pagos (id_atencion, monto, metodo, estado, registrado_por) 
                    VALUES (:id_at, :monto, :metodo, 'PAGADO', :user)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":id_at", $id_atencion);
            $stmt->bindParam(":monto", $monto);
            $stmt->bindParam(":metodo", $metodo);
            $stmt->bindParam(":user", $id_usuario);
            $stmt->execute();
            
            $id_pago = $this->conn->lastInsertId();

            // B. Generar registro de Recibo automáticamente
            // Número de recibo simple: REC + ID (ej: REC-000001)
            $nro_recibo = "REC-" . str_pad($id_pago, 6, "0", STR_PAD_LEFT);
            
            $sqlRecibo = "INSERT INTO recibos (id_pago, numero_recibo, total) VALUES (:id_p, :nro, :total)";
            $stmtR = $this->conn->prepare($sqlRecibo);
            $stmtR->bindParam(":id_p", $id_pago);
            $stmtR->bindParam(":nro", $nro_recibo);
            $stmtR->bindParam(":total", $monto);
            $stmtR->execute();
            
            $id_recibo = $this->conn->lastInsertId();

            $this->conn->commit();
            return $id_recibo; // Devolvemos ID del recibo para imprimirlo

        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // REPORTES: Ingresos por rango de fecha
    public function reporteIngresos($desde, $hasta) {
        // Ajustamos la fecha "hasta" para que incluya todo ese día (hasta las 23:59:59)
        $hasta = $hasta . ' 23:59:59';

        $sql = "SELECT 
                    p.fecha_pago,
                    p.monto,
                    p.metodo,
                    r.numero_recibo,
                    pac.nombres, pac.apellido_paterno, pac.ci,
                    u.usuario as cajero
                FROM pagos p
                INNER JOIN recibos r ON p.id_pago = r.id_pago
                INNER JOIN atenciones a ON p.id_atencion = a.id_atencion
                INNER JOIN citas c ON a.id_cita = c.id_cita
                INNER JOIN pacientes pac ON c.id_paciente = pac.id_paciente
                LEFT JOIN usuarios u ON p.registrado_por = u.id_usuario
                WHERE p.fecha_pago BETWEEN :desde AND :hasta
                ORDER BY p.fecha_pago DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":desde", $desde);
        $stmt->bindParam(":hasta", $hasta);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. OBTENER DATOS PARA IMPRIMIR EL RECIBO (NUEVA FUNCIÓN)
    public function obtenerDatosRecibo($id_recibo) {
        // A. Consultamos info de cabecera: recibo, pago, paciente y totales
        $sql = "SELECT 
                    r.numero_recibo, r.fecha_recibo, r.total,
                    p.metodo,
                    pac.nombres, pac.apellido_paterno, pac.ci,
                    c.id_cita
                FROM recibos r
                INNER JOIN pagos p ON r.id_pago = p.id_pago
                INNER JOIN atenciones a ON p.id_atencion = a.id_atencion
                INNER JOIN citas c ON a.id_cita = c.id_cita
                INNER JOIN pacientes pac ON c.id_paciente = pac.id_paciente
                WHERE r.id_recibo = :id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id_recibo);
        $stmt->execute();
        $cabecera = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cabecera) return false;

        // B. Buscamos qué servicios se le cobraron (Detalle de items)
        $sqlDetalle = "SELECT s.nombre, d.precio_unitario 
                       FROM detalle_atencion d
                       INNER JOIN servicios s ON d.id_servicio = s.id_servicio
                       INNER JOIN atenciones a ON d.id_atencion = a.id_atencion
                       INNER JOIN pagos p ON a.id_atencion = p.id_atencion
                       INNER JOIN recibos r ON p.id_pago = r.id_pago
                       WHERE r.id_recibo = :id";
                       
        $stmtDet = $this->conn->prepare($sqlDetalle);
        $stmtDet->bindParam(":id", $id_recibo);
        $stmtDet->execute();
        $items = $stmtDet->fetchAll(PDO::FETCH_ASSOC);

        // Devolvemos todo junto
        return ['info' => $cabecera, 'items' => $items];
    }
}
?>