<?php
require_once __DIR__ . '/../config/db.php';

class Cita {
    private $conn;
    private $table = "citas";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // LISTAR PARA CALENDARIO (Con Filtro Opcional de Doctor)
    public function listarParaCalendario($id_odontologo = null) {
        $sql = "SELECT 
                    c.id_cita, 
                    c.fecha_hora_inicio as start, 
                    c.fecha_hora_fin as end, 
                    CONCAT(p.nombres, ' ', p.apellido_paterno) as title,
                    c.estado,
                    c.motivo
                FROM citas c
                INNER JOIN pacientes p ON c.id_paciente = p.id_paciente";

        // Filtrar por doctor si se especifica
        if ($id_odontologo) {
            $sql .= " WHERE c.id_odontologo = :odo";
        }
        
        $sql .= " ORDER BY c.fecha_hora_inicio ASC";

        $stmt = $this->conn->prepare($sql);
        
        if ($id_odontologo) {
            $stmt->bindParam(":odo", $id_odontologo, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // OBTENER UNA CITA CON TODOS SUS DATOS
    public function obtenerPorId($id) {
        $query = "SELECT c.*, p.ci, p.nombres as nombre_paciente, p.apellido_paterno
                  FROM " . $this->table . " c
                  INNER JOIN pacientes p ON c.id_paciente = p.id_paciente
                  WHERE c.id_cita = :id";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // VERIFICAR DISPONIBILIDAD DEL HORARIO
    public function verificarDisponibilidad($id_odontologo, $inicio, $fin, $id_cita_excluir = null) {
        $sql = "SELECT COUNT(*) as total FROM " . $this->table . " 
                WHERE id_odontologo = :odo 
                AND estado IN ('PROGRAMADA', 'ATENDIDA')
                AND (
                    (fecha_hora_inicio < :fin AND fecha_hora_fin > :inicio)
                )";
        
        // Si estamos editando, excluir la cita actual
        if ($id_cita_excluir) {
            $sql .= " AND id_cita != :exc";
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":odo", $id_odontologo, PDO::PARAM_INT);
        $stmt->bindParam(":inicio", $inicio);
        $stmt->bindParam(":fin", $fin);
        
        if ($id_cita_excluir) {
            $stmt->bindParam(":exc", $id_cita_excluir, PDO::PARAM_INT);
        }

        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Retorna true si está disponible (total = 0)
        return ($resultado['total'] == 0); 
    }

    // CREAR NUEVA CITA
    public function crear($datos) {
        $query = "INSERT INTO " . $this->table . " 
                  (id_paciente, id_odontologo, fecha_hora_inicio, fecha_hora_fin, motivo, estado, creada_por) 
                  VALUES (:pac, :odoc, :inicio, :fin, :motivo, 'PROGRAMADA', :creador)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":pac", $datos['id_paciente'], PDO::PARAM_INT);
        $stmt->bindParam(":odoc", $datos['id_odontologo'], PDO::PARAM_INT);
        $stmt->bindParam(":inicio", $datos['inicio']);
        $stmt->bindParam(":fin", $datos['fin']);
        $stmt->bindParam(":motivo", $datos['motivo']);
        $stmt->bindParam(":creador", $datos['id_usuario'], PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    // ACTUALIZAR CITA EXISTENTE
    public function actualizar($datos) {
        // Solo permitir actualizar si está PROGRAMADA
        $citaActual = $this->obtenerPorId($datos['id_cita']);
        
        if($citaActual['estado'] != 'PROGRAMADA') {
            return false; // No se puede editar citas atendidas o canceladas
        }
        
        $query = "UPDATE " . $this->table . " SET 
                  id_paciente = :pac, 
                  id_odontologo = :odoc, 
                  fecha_hora_inicio = :inicio, 
                  fecha_hora_fin = :fin, 
                  motivo = :motivo
                  WHERE id_cita = :id AND estado = 'PROGRAMADA'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":pac", $datos['id_paciente'], PDO::PARAM_INT);
        $stmt->bindParam(":odoc", $datos['id_odontologo'], PDO::PARAM_INT);
        $stmt->bindParam(":inicio", $datos['inicio']);
        $stmt->bindParam(":fin", $datos['fin']);
        $stmt->bindParam(":motivo", $datos['motivo']);
        $stmt->bindParam(":id", $datos['id_cita'], PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    // MOVER CITA (Drag & Drop en calendario)
    public function mover($id_cita, $inicio, $fin) {
        // Solo mover si está PROGRAMADA
        $query = "UPDATE " . $this->table . " 
                  SET fecha_hora_inicio = :inicio, fecha_hora_fin = :fin 
                  WHERE id_cita = :id AND estado = 'PROGRAMADA'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":inicio", $inicio);
        $stmt->bindParam(":fin", $fin);
        $stmt->bindParam(":id", $id_cita, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    // CANCELAR CITA (MEJORADO - Registra quién cancela)
    public function cancelar($id_cita, $id_usuario = null) {
        // Solo cancelar si está PROGRAMADA
        $query = "UPDATE " . $this->table . " 
                  SET estado = 'CANCELADA'";
        
        // Si tenemos el usuario que cancela, lo registramos en creada_por
        // (Esto es temporal, idealmente tendrías un campo cancelada_por)
        if($id_usuario) {
            $query .= ", creada_por = :usuario";
        }
        
        $query .= " WHERE id_cita = :id AND estado = 'PROGRAMADA'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id_cita, PDO::PARAM_INT);
        
        if($id_usuario) {
            $stmt->bindParam(":usuario", $id_usuario, PDO::PARAM_INT);
        }
        
        return $stmt->execute();
    }

    // MARCAR COMO NO ASISTIÓ
    public function marcarNoAsistio($id_cita) {
        $query = "UPDATE " . $this->table . " 
                  SET estado = 'NO_ASISTIO' 
                  WHERE id_cita = :id AND estado = 'PROGRAMADA'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id_cita, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    // OBTENER CITAS DEL DÍA (Para vista de agenda diaria)
    public function obtenerCitasDelDia($fecha, $id_odontologo = null) {
        $sql = "SELECT 
                    c.id_cita,
                    c.fecha_hora_inicio,
                    c.fecha_hora_fin,
                    c.estado,
                    c.motivo,
                    CONCAT(p.nombres, ' ', p.apellido_paterno, ' ', IFNULL(p.apellido_materno, '')) as paciente,
                    p.ci,
                    p.telefono,
                    CONCAT(u.nombres, ' ', u.apellidos) as odontologo
                FROM citas c
                INNER JOIN pacientes p ON c.id_paciente = p.id_paciente
                INNER JOIN odontologos o ON c.id_odontologo = o.id_odontologo
                INNER JOIN usuarios u ON o.id_usuario = u.id_usuario
                WHERE DATE(c.fecha_hora_inicio) = :fecha";
        
        if($id_odontologo) {
            $sql .= " AND c.id_odontologo = :odo";
        }
        
        $sql .= " ORDER BY c.fecha_hora_inicio ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":fecha", $fecha);
        
        if($id_odontologo) {
            $stmt->bindParam(":odo", $id_odontologo, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ANULAR CITAS VENCIDAS (Proceso automático opcional)
    public function anularVencidas() {
        // Marcar como NO_ASISTIO las citas programadas que pasaron más de 2 horas
        $sql = "UPDATE citas 
                SET estado = 'NO_ASISTIO' 
                WHERE estado = 'PROGRAMADA' 
                AND fecha_hora_fin < DATE_SUB(NOW(), INTERVAL 2 HOUR)";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute();
    }
}
?>