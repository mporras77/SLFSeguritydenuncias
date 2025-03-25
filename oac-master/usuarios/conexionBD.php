<?php

$conexion = mysqli_connect("localhost", "root", "", "mysql");

// Verificar si la conexión fue exitosa
if (!$conexion) {
    // Mostrar mensaje de error con SweetAlert
    echo '<script>
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "No se pudo conectar a la base de datos."
        });
    </script>';
} else {
    // Mostrar mensaje de éxito con SweetAlert
    echo '<script>
        Swal.fire({
            icon: "success",
            title: "Conexión exitosa",
            text: "Se ha conectado exitosamente a la base de datos."
        });
    </script>';
}

?>