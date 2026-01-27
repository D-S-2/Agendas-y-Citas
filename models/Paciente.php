<?php
require_once __DIR__ . '/../config/db.php';

class Paciente {
    private $conn;
    private $table = "pacientes";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Listar todos los pacientes (para los selectores de citas)
    public function listarTodos() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY nombres, apellido_paterno";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un solo paciente por ID
    public function obtenerPorId($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_paciente = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>