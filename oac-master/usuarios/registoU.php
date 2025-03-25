<?php
include 'conexionBD.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener y limpiar datos del formulario
    $nombre_completo = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $usuario = mysqli_real_escape_string($conexion, $_POST['correo']);
    $contrasena = mysqli_real_escape_string($conexion, $_POST['password']);

    // Encriptar la contraseña
    $contrasena = hash('sha512', $contrasena);

    // Verificar si el usuario ya existe
    $verificar_usuario = mysqli_query($conexion, "SELECT * FROM usuarios WHERE usuario='$usuario'");
    if (mysqli_num_rows($verificar_usuario) > 0) {
        echo '<script>
                alert("Este correo ya está registrado. Usa otro.");
                window.location = "index.php";
              </script>';
        exit();
    }

    // Obtener el siguiente ID disponible sin saltos
    $resultado = mysqli_query($conexion, 
        "SELECT COALESCE(MIN(t1.id_usuario + 1), 1) AS next_id 
         FROM usuarios t1 
         WHERE NOT EXISTS (SELECT 1 FROM usuarios t2 WHERE t2.id_usuario = t1.id_usuario + 1)"
    );
    $row = mysqli_fetch_assoc($resultado);
    $next_id = $row['next_id'];

    // Generar el perfil único en formato "SLF###"
    $perfil = 'SLF' . str_pad($next_id, 3, '0', STR_PAD_LEFT);

    // Insertar el nuevo usuario en la base de datos
    $query = "INSERT INTO usuarios (id_usuario, usuario, password, nombre, activo, perfil, fecharegistro) 
              VALUES ('$next_id', '$usuario', '$contrasena', '$nombre_completo', 1, '$perfil', NOW())";

    if (mysqli_query($conexion, $query)) {
        echo '<script>
                alert("Usuario registrado correctamente.");
                window.location = "../index.php";
              </script>';
    } else {
        echo '<script>
                alert("Hubo un error en el registro. Inténtalo de nuevo.");
                window.location = "index.php";
              </script>';
    }

    // Cerrar la conexión
    mysqli_close($conexion);
} else {
    echo '<script>
            alert("Acceso denegado.");
            window.location = "index.php";
          </script>';
}
?>
