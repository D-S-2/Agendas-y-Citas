<?php
/*
 * Clase de conexión a la base de datos usando PDO
 * Asegúrate de cambiar 'root' y '' por tu usuario y contraseña de MySQL si son diferentes.
 */
class Database {
    private $host = 'localhost';
    private $db_name = 'clinica_odontologica';
    private $username = 'root'; 
    private $password = ''; 
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8"); // Para admitir tildes y ñ
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>