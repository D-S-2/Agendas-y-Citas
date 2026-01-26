<?php
require_once __DIR__ . '/../config/db.php';

class Cita {
    private $conn;
    private $table = "citas";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function listarParaCalendario($id_odontologo = null) {
        $sql = "SELECT c.id_cita, c.fecha_hora_inicio as start, c.fecha_hora_fin as end, 
                CONCAT(p.nombres, ' ', p.apellido_paterno) as title, c.estado, c.motivo
                FROM citas c INNER JOIN pacientes p ON c.id_paciente = p.id_paciente";
        if ($id_odontologo) { $sql .= " WHERE c.id_odontologo = :odo"; }
        $stmt = $this->conn->prepare($sql);
        if ($id_odontologo) { $stmt->bindParam(":odo", $id_odontologo); }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerCitasDelDia($fecha, $id_odontologo = null) {
        $sql = "SELECT c.*, CONCAT(p.nombres, ' ', p.apellido_paterno) as paciente, p.ci, p.telefono,
                CONCAT(u.nombres, ' ', u.apellidos) as odontologo
                FROM citas c
                INNER JOIN pacientes p ON c.id_paciente = p.id_paciente
                INNER JOIN odontologos o ON c.id_odontologo = o.id_odontologo
                INNER JOIN usuarios u ON o.id_usuario = u.id_usuario
                WHERE DATE(c.fecha_hora_inicio) = :fecha";
        if($id_odontologo) { $sql .= " AND c.id_odontologo = :odo"; }
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":fecha", $fecha);
        if($id_odontologo) { $stmt->bindParam(":odo", $id_odontologo); }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $query = "SELECT c.*, p.ci, p.nombres as nombre_paciente, p.apellido_paterno FROM " . $this->table . " c
                  INNER JOIN pacientes p ON c.id_paciente = p.id_paciente WHERE c.id_cita = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($datos) {
        $query = "INSERT INTO citas (id_paciente, id_odontologo, fecha_hora_inicio, fecha_hora_fin, motivo, estado, creada_por) 
                  VALUES (:pac, :odoc, :inicio, :fin, :motivo, 'PROGRAMADA', :creador)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ":pac" => $datos['id_paciente'],
            ":odoc" => $datos['id_odontologo'],
            ":inicio" => $datos['inicio'],
            ":fin" => $datos['fin'],
            ":motivo" => $datos['motivo'],
            ":creador" => $datos['id_usuario']
        ]);
    }
}