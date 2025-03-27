<?php
/*
 * Autenticación de usuario
 */

// Incluir la clase Usuario y otros archivos necesarios
require_once('../spoon/spoon.php');
require_once('../clases/Usuario.php'); // Asegurar que la clase Usuario está disponible

// Obtener y limpiar los valores del formulario
$correo = filter_input(INPUT_POST, "usuario", FILTER_SANITIZE_EMAIL);
$contrasena = filter_input(INPUT_POST, "password", FILTER_UNSAFE_RAW);

$correo = trim($correo);
$contrasena = trim($contrasena);

// Verificar que los datos no estén vacíos
if (empty($correo) || empty($contrasena)) {
    http_response_code(400); // Código de error 400 (Bad Request)
    exit("Error: Correo y contraseña son requeridos.");
}

// Iniciar sesión si es necesario
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Crear instancia del objeto Usuario
$objUsuario = new Usuario();
$objUsuario->setNomUsu($correo);
$objUsuario->setPasUsu($contrasena);

// Intentar iniciar sesión
if ($objUsuario->logInUser() === true) {
    $_SESSION['usuario'] = $correo; // Guardar sesión del usuario autenticado
    $perfil = $objUsuario->getPerfil();

    if ($perfil === '1' || $perfil === '2') {
        echo "<script>window.location.href='../dashboard.php';</script>"; // Redirigir
        exit;
    } elseif ($perfil === '3') {
        echo "<script>window.location.href='../dashboard.php';</script>"; // Redirigir
        exit;
    }
}

// Si llega aquí, la autenticación falló
http_response_code(401); // Código de error 401 (Unauthorized)
exit("Error: Credenciales incorrectas.");
?>
