<?php
require_once __DIR__ . '/../config/db.php';

class Paciente {
    private $conn;
    private $table = "pacientes";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Listar todos los pacientes (para la tabla)
    public function listarTodos() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY id_paciente DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un solo paciente (para editar)
    public function obtenerPorId($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_paciente = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear nuevo paciente
    public function crear($datos) {
        $query = "INSERT INTO " . $this->table . " 
                  (ci, nombres, apellido_paterno, apellido_materno, fecha_nacimiento, sexo, direccion, telefono) 
                  VALUES (:ci, :nombres, :ap, :am, :nac, :sexo, :dir, :tel)";
        
        $stmt = $this->conn->prepare($query);
        
        // Vincular valores
        $stmt->bindParam(":ci", $datos['ci']);
        $stmt->bindParam(":nombres", $datos['nombres']);
        $stmt->bindParam(":ap", $datos['apellido_paterno']);
        $stmt->bindParam(":am", $datos['apellido_materno']);
        $stmt->bindParam(":nac", $datos['fecha_nacimiento']);
        $stmt->bindParam(":sexo", $datos['sexo']);
        $stmt->bindParam(":dir", $datos['direccion']);
        $stmt->bindParam(":tel", $datos['telefono']);

        return $stmt->execute();
    }

    // Actualizar paciente
    public function actualizar($datos) {
        $query = "UPDATE " . $this->table . " 
                  SET ci=:ci, nombres=:nombres, apellido_paterno=:ap, apellido_materno=:am, 
                      fecha_nacimiento=:nac, sexo=:sexo, direccion=:dir, telefono=:tel 
                  WHERE id_paciente = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":ci", $datos['ci']);
        $stmt->bindParam(":nombres", $datos['nombres']);
        $stmt->bindParam(":ap", $datos['apellido_paterno']);
        $stmt->bindParam(":am", $datos['apellido_materno']);
        $stmt->bindParam(":nac", $datos['fecha_nacimiento']);
        $stmt->bindParam(":sexo", $datos['sexo']);
        $stmt->bindParam(":dir", $datos['direccion']);
        $stmt->bindParam(":tel", $datos['telefono']);
        $stmt->bindParam(":id", $datos['id_paciente']);

        return $stmt->execute();
    }

    // Buscar paciente por CI (Para la historia clínica)
    public function buscarPorCI($ci) {
        $query = "SELECT * FROM " . $this->table . " WHERE ci = :ci LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":ci", $ci);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>