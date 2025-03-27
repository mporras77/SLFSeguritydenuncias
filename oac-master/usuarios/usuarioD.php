<?php
// Iniciar sesión para mantener la sesión del usuario
session_start(); 

// Incluir el archivo de conexión a la base de datos
include 'conexionBD.php';

// Verificar si la solicitud es de tipo POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar conexión a la base de datos
    if (!$conexion) {
        die("Error de conexión: " . mysqli_connect_error());
    }

    // Validar que los campos requeridos estén presentes en la solicitud
    if (!isset($_POST['usuario'], $_POST['password'])) {
        header("Location: log-reg.php?error=campos_vacios");
        exit();
    }

    // Limpiar los datos del formulario para evitar inyecciones SQL
    $correo = mysqli_real_escape_string($conexion, trim($_POST['usuario']));
    $contrasena = $_POST['password']; // No es necesario escaparlo, ya que no se usa directamente en SQL

    // Consultar la base de datos para encontrar el usuario
    $query = "SELECT id_usuario, usuario, password, nombre FROM usuarios WHERE usuario = '$correo'";
    $resultado = mysqli_query($conexion, $query);

    // Verificar si el usuario existe
    if (mysqli_num_rows($resultado) > 0) {
        $usuario = mysqli_fetch_assoc($resultado);

        // Verificar si la contraseña ingresada coincide con la almacenada
        if (password_verify($contrasena, $usuario['password'])) {
            // Guardar los datos del usuario en la sesión
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['usuario'] = $usuario['usuario'];
            $_SESSION['nombre'] = $usuario['nombre'];

            // Redirigir al panel de usuario
            header("Location: ../dashboard/index.php");
            exit();
        } else {
            // Si la contraseña es incorrecta, redirigir con un mensaje de error
            header("Location: log-reg.php?error=contraseña_incorrecta");
            exit();
        }
    } else {
        // Si el usuario no se encuentra en la base de datos, redirigir con un error
        header("Location: log-reg.php?error=usuario_no_encontrado");
        exit();
    }

    // Cerrar la conexión con la base de datos
    mysqli_close($conexion);
} else {
    // Si el acceso no es por método POST, redirigir con un error
    header("Location: log-reg.php?error=acceso_denegado");
    exit();
}
?>
