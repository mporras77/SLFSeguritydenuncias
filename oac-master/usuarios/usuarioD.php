<?php
session_start(); // Iniciar sesión
include 'conexionBD.php'; // Conectar con la base de datos (también subir un nivel si está fuera)

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar conexión a la base de datos
    if (!$conexion) {
        die("Error de conexión: " . mysqli_connect_error());
    }

    // Validar que se enviaron los datos
    if (!isset($_POST['usuario'], $_POST['password'])) {
        header("Location: ../index.php?error=campos_vacios");
        exit();
    }

    // Limpiar datos del formulario
    $correo = mysqli_real_escape_string($conexion, trim($_POST['usuario']));
    $contrasena = $_POST['password']; // No es necesario escaparlo, ya que no se usa directamente en SQL

    // Buscar el usuario en la base de datos
    $query = "SELECT id_usuario, usuario, password, nombre FROM usuarios WHERE usuario = '$correo'";
    $resultado = mysqli_query($conexion, $query);

    if (mysqli_num_rows($resultado) > 0) {
        $usuario = mysqli_fetch_assoc($resultado);

        // Verificar la contraseña
        if (password_verify($contrasena, $usuario['password'])) {
            // Iniciar sesión y guardar datos del usuario
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['usuario'] = $usuario['usuario'];
            $_SESSION['nombre'] = $usuario['nombre'];

            header("Location: ../../../index.php"); // Redirigir al panel de usuario
            exit();
        } else {
            header("Location: ../index.php?error=contraseña_incorrecta");
            exit();
        }
    } else {
        header("Location: ../index.php?error=usuario_no_encontrado");
        exit();
    }

    mysqli_close($conexion); // Cerrar conexión
} else {
    header("Location: ../index.php?error=acceso_denegado");
    exit();
}
?>
