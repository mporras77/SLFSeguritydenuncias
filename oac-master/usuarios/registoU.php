<?php
// Incluir el archivo de conexión a la base de datos
include 'conexionBD.php';

// Verificar si la solicitud es de tipo POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Verificar la conexión a la base de datos
    if (!$conexion) {
        die("Error de conexión: " . mysqli_connect_error());
    }

    // Validar que los campos requeridos estén presentes en la solicitud
    if (!isset($_POST['nombre'], $_POST['correo'], $_POST['password'])) {
        header("Location: ./log-reg.php?error=campos_vacios");
        exit();
    }

    // Limpiar y obtener los datos del formulario
    $nombre_completo = mysqli_real_escape_string($conexion, trim($_POST['nombre']));
    $correo = mysqli_real_escape_string($conexion, trim($_POST['correo']));
    $contrasena = mysqli_real_escape_string($conexion, $_POST['password']);

    // Validar que el correo tenga un formato válido
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        header("Location: ./log-reg.php?error=correo_invalido");
        exit();
    }

    // Verificar si el usuario ya existe en la base de datos
    $verificar_usuario = mysqli_query($conexion, "SELECT * FROM usuarios WHERE usuario='$correo'");
    if (mysqli_num_rows($verificar_usuario) > 0) {
        header("Location: ./log-reg.php?error=usuario_existente");
        exit();
    }

    // Encriptar la contraseña antes de almacenarla en la base de datos
    $contrasena_hash = password_hash($contrasena, PASSWORD_BCRYPT);

    // Obtener el siguiente ID de usuario disponible en la base de datos
    $query_perfil = mysqli_query($conexion, "SELECT COALESCE(MAX(id_usuario), 0) + 1 AS next_id FROM usuarios");
    $row = mysqli_fetch_assoc($query_perfil);
    $next_id = $row['next_id'];
    
    // Generar un identificador de perfil basado en el ID del usuario
    $perfil = 'SLF' . str_pad($next_id, 3, '0', STR_PAD_LEFT);

    // Insertar los datos del usuario en la base de datos
    $query = "INSERT INTO usuarios (id_usuario, usuario, password, nombre, activo, perfil, fecharegistro) 
              VALUES ('$next_id', '$correo', '$contrasena_hash', '$nombre_completo', 1, '$perfil', NOW())";

    // Ejecutar la consulta e indicar el resultado
    if (mysqli_query($conexion, $query)) {
        header("Location: ./log-reg.php?registro=exitoso");
        exit();
    } else {
        header("Location: ./log-reg.php?error=" . urlencode(mysqli_error($conexion)));
        exit();
    }

    // Cerrar la conexión con la base de datos
    mysqli_close($conexion);
} else {
    // Redirigir si el acceso no es por método POST
    header("Location: ./log-reg.php?error=acceso_denegado");
    exit();
}
?>
