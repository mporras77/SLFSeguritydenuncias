<?php
session_start();
include 'conexion_be.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Verificar conexión a la base de datos
    if (!$conexion) {
        die("Error de conexión: " . mysqli_connect_error());
    }

    // Obtener y limpiar datos del formulario
    $usuario = mysqli_real_escape_string($conexion, trim($_POST['usuario']));
    $contrasena = trim($_POST['contrasena']);

    // Verificar si el usuario existe
    $query = "SELECT * FROM usuarios WHERE usuario='$usuario'";
    $resultado = mysqli_query($conexion, $query);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $datos_usuario = mysqli_fetch_assoc($resultado);

        // Verificar la contraseña usando password_verify()
        if (password_verify($contrasena, $datos_usuario['password'])) {
            $_SESSION['usuario'] = $usuario; // Guardar sesión del usuario
            header("Location: ./index.php");
            exit();
        } else {
            header("Location: ./login.php?error=contrasena_incorrecta");
            exit();
        }
    } else {
        header("Location: ./login.php?error=usuario_no_existe");
        exit();
    }

    // Cerrar conexión
    mysqli_close($conexion);
} else {
    header("Location: ./login.php?error=acceso_denegado");
    exit();
}
?>
