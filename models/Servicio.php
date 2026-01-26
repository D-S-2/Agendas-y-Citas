<?php
require_once __DIR__ . '/../config/db.php';

class Servicio {
    private $conn;
    private $table = "servicios";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Listar todos los servicios activos
    public function listarActivos() {
        $query = "SELECT * FROM " . $this->table . " WHERE activo = 1 ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crear un nuevo servicio
    public function crear($nombre, $precio) {
        $query = "INSERT INTO " . $this->table . " (nombre, precio, activo) VALUES (:nombre, :precio, 1)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":precio", $precio);
        return $stmt->execute();
    }

    // Eliminar (Desactivar) servicio
    public function eliminar($id) {
        $query = "UPDATE " . $this->table . " SET activo = 0 WHERE id_servicio = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>