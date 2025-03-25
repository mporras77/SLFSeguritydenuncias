<?php
/*
 * 13.02.2012
 * Manipulación del objeto usuario
 */

require_once('../spoon/spoon.php');

$txtusuario = filter_input(INPUT_POST, "txtusuario", FILTER_SANITIZE_STRING);
$txtpassword = filter_input(INPUT_POST, "txtpassword", FILTER_SANITIZE_STRING);

if (!$txtusuario || !$txtpassword) {
    exit("Error: Usuario y contraseña son requeridos.");
}

$objUsuario = new Usuario();
$objUsuario->setNomUsu(trim($txtusuario));
$objUsuario->setPasUsu(trim($txtpassword));

if ($objUsuario->logInUser() === true) {
    $perfil = $objUsuario->getPerfil();

    if ($perfil === '1' || $perfil === '2') {
        echo 1;
        exit;
    } elseif ($perfil === '3') {
        echo 3;
        exit;
    }
}

// Si llega aquí, la autenticación falló
exit("Error: Credenciales incorrectas.");
