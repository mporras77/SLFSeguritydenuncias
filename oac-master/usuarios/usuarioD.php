<?php
session_start();
include 'conexionBD.php';

// Validar si se reciben los datos por POST
if (!isset($_POST['usuario'], $_POST['password'])) {
    die('<script>alert("Faltan datos."); window.location = "index.php";</script>');
}

$usuario = trim($_POST['usuario']);
$password = trim($_POST['password']);
$passwordHash = hash('sha256', $password); // Hash de la contraseña ingresada

// Preparar la consulta segura con prepared statements
$stmt = $conexion->prepare("SELECT id_usuario, password FROM usuarios WHERE usuario = ? LIMIT 1");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

// Verificar si el usuario existe
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Depuración: Imprimir los hashes para ver si coinciden
    error_log("Ingresado: " . $passwordHash);
    error_log("En la BD: " . $row['password']);

    // Comparar los hashes de la contraseña ingresada y la almacenada en la BD
    if (hash_equals($row['password'], $passwordHash)) {
        $_SESSION['usuario'] = $usuario;
        header("Location: ./index.php"); // Redirigir al usuario logueado
        exit;
    } else {
        echo '<script>alert("Contraseña incorrecta"); window.location = "login.php";</script>';
        exit;
    }
} else {
    echo '<script>alert("Usuario no existente, por favor regístrate"); window.location = "login.php";</script>';
    exit;
}

// Cerrar conexión
$stmt->close();
$conexion->close();
?>
