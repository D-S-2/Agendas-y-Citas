<?php
// models/Paciente.php
require_once __DIR__ . '/../config/db.php';

class Paciente {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function listarTodos() {
        $stmt = $this->conn->prepare("SELECT id_paciente, ci, nombres, apellido_paterno FROM pacientes ORDER BY nombres ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>