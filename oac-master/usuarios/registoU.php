<?php
include 'conexionBD.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar conexión a la base de datos
    if (!$conexion) {
        die("Error de conexión: " . mysqli_connect_error());
    }

    // Verificar que los campos requeridos están presentes
    if (!isset($_POST['nombre'], $_POST['correo'], $_POST['password'])) {
        header("Location: ./index.php?error=campos_vacios");
        exit();
    }

    // Obtener y limpiar datos del formulario
    $nombre_completo = mysqli_real_escape_string($conexion, trim($_POST['nombre']));
    $correo = mysqli_real_escape_string($conexion, trim($_POST['correo']));
    $contrasena = mysqli_real_escape_string($conexion, $_POST['password']);

    // Validar formato de correo
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        header("Location: ./index.php?error=correo_invalido");
        exit();
    }

    // Verificar si el usuario ya existe
    $verificar_usuario = mysqli_query($conexion, "SELECT * FROM usuarios WHERE usuario='$correo'");
    if (mysqli_num_rows($verificar_usuario) > 0) {
        header("Location: ./index.php?error=usuario_existente");
        exit();
    }

    // Encriptar la contraseña usando password_hash
    $contrasena_hash = password_hash($contrasena, PASSWORD_BCRYPT);

    // Obtener el siguiente ID disponible
    $query_perfil = mysqli_query($conexion, "SELECT COALESCE(MAX(id_usuario), 0) + 1 AS next_id FROM usuarios");
    $row = mysqli_fetch_assoc($query_perfil);
    $next_id = $row['next_id'];
    $perfil = 'SLF' . str_pad($next_id, 3, '0', STR_PAD_LEFT);

    // Insertar usuario en la base de datos
    $query = "INSERT INTO usuarios (id_usuario, usuario, password, nombre, activo, perfil, fecharegistro) 
              VALUES ('$next_id', '$correo', '$contrasena_hash', '$nombre_completo', 1, '$perfil', NOW())";

    if (mysqli_query($conexion, $query)) {
        header("Location: ./index.php?registro=exitoso");
        exit();
    } else {
        header("Location: ./index.php?error=" . urlencode(mysqli_error($conexion)));
        exit();
    }

    // Cerrar conexión
    mysqli_close($conexion);
} else {
    header("Location: ./index.php?error=acceso_denegado");
    exit();
}
?>
