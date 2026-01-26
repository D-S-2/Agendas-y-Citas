<?php
// Cargamos tu configuración de base de datos
require_once 'config/db.php';

try {
    $database = new Database();
    $conn = $database->getConnection();

    // 1. La contraseña que quieres usar
    $password_plano = "123456";

    // 2. PHP genera el hash encriptado oficial
    $password_encriptado = password_hash($password_plano, PASSWORD_DEFAULT);

    // 3. Actualizamos el usuario 'admin' en la base de datos
    // Asegúrate que el usuario en tu BD sea 'admin'
    $sql = "UPDATE usuarios SET password_hash = :pass WHERE usuario = 'admin'";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':pass', $password_encriptado);
    
    if($stmt->execute()) {
        echo "<h1>¡Éxito!</h1>";
        echo "<p>La contraseña del usuario <b>admin</b> ha sido actualizada.</p>";
        echo "<p>Nueva contraseña encriptada (Hash): <b>" . $password_encriptado . "</b></p>";
        echo "<p>Ahora puedes ir al <a href='views/auth/login.php'>Login</a> e ingresar con <b>123456</b></p>";
    } else {
        echo "Error al actualizar.";
    }

} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}
?>