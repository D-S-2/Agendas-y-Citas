<?php
// models/Odontologo.php
require_once __DIR__ . '/../config/db.php';

class Odontologo {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Listar doctores con sus nombres (uniendo con tabla usuarios)
    public function listarTodos() {
        $query = "SELECT o.id_odontologo, o.especialidad, u.nombres, u.apellidos 
                  FROM odontologos o
                  INNER JOIN usuarios u ON o.id_usuario = u.id_usuario
                  WHERE u.activo = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>