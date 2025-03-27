<?php
session_start(); // Iniciar sesión si no está iniciada

// Limitar caché para evitar que la sesión se recupere después del logout
session_cache_limiter('nocache');

// Eliminar todas las variables de sesión
session_unset();

// Destruir la sesión
session_destroy();

// Eliminar la cookie de sesión si existe
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}

// Redirigir a la página de login
header('Location: log-reg.php');
exit(); // Asegura que el script no continúe ejecutándose
?>
