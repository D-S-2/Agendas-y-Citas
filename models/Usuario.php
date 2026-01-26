<?php
require_once __DIR__ . '/../config/db.php';

class Usuario {
    private $conn;
    private $table = "usuarios";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Login (Ya lo tenías)
    public function obtenerPorUsuario($usuario) {
        $query = "SELECT u.id_usuario, u.usuario, u.password_hash, u.nombres, u.apellidos, u.id_rol, r.nombre as rol_nombre 
                  FROM " . $this->table . " u
                  INNER JOIN roles r ON u.id_rol = r.id_rol
                  WHERE u.usuario = :usuario AND u.activo = 1 LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario", $usuario);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 1. LISTAR TODOS LOS USUARIOS (Para la tabla)
    public function listarTodos() {
        $query = "SELECT u.id_usuario, u.usuario, u.nombres, u.apellidos, u.email, r.nombre as rol, u.activo 
                  FROM " . $this->table . " u
                  INNER JOIN roles r ON u.id_rol = r.id_rol
                  ORDER BY u.id_usuario DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. CREAR NUEVO USUARIO (Con lógica especial para Doctores)
    public function crear($datos) {
        try {
            $this->conn->beginTransaction();

            // A. Insertar en tabla USUARIOS
            $query = "INSERT INTO usuarios (id_rol, usuario, password_hash, nombres, apellidos, email, telefono, activo) 
                      VALUES (:rol, :user, :pass, :nom, :ape, :email, :tel, 1)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":rol", $datos['id_rol']);
            $stmt->bindParam(":user", $datos['usuario']);
            $stmt->bindParam(":pass", $datos['password']); // Ya debe venir hasheado
            $stmt->bindParam(":nom", $datos['nombres']);
            $stmt->bindParam(":ape", $datos['apellidos']);
            $stmt->bindParam(":email", $datos['email']);
            $stmt->bindParam(":tel", $datos['telefono']);
            $stmt->execute();
            
            $id_usuario = $this->conn->lastInsertId();

            // B. Si es DOCTOR (Rol 3 según tu BD), insertar en ODONTOLOGOS
            if ($datos['id_rol'] == 3) {
                $queryDoc = "INSERT INTO odontologos (id_usuario, matricula, especialidad) 
                             VALUES (:uid, :mat, :esp)";
                $stmtDoc = $this->conn->prepare($queryDoc);
                $stmtDoc->bindParam(":uid", $id_usuario);
                $stmtDoc->bindParam(":mat", $datos['matricula']);
                $stmtDoc->bindParam(":esp", $datos['especialidad']);
                $stmtDoc->execute();
            }

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // 3. OBTENER ROLES (Para el select del formulario)
    public function obtenerRoles() {
        $stmt = $this->conn->prepare("SELECT * FROM roles");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>