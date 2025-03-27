<?php
/**
 * @author Luis Salazar
 * @copyright 2015
 */
require_once "../spoon/spoon.php";
session_start();

// Validar que los campos requeridos están presentes y no están vacíos
$comunidad = trim($_POST['comunidad'] ?? '');
$id_parroquia = $_POST['parroquias'] ?? '';

if (empty($comunidad) || empty($id_parroquia)) {
    exit("Verifique que los campos requeridos hayan sido llenados e intente de nuevo.");
}

try {
    $objDB = new DBConexion();
    $objDB->execute("SET NAMES utf8");

    $_data = [
        'comunidad'    => $comunidad,
        'id_parroquia' => $id_parroquia
    ];

    if (!empty($_POST['id_comunidad'])) {
        $id_comunidad = $_POST['id_comunidad'];
        $update = $objDB->update("comunidades", $_data, "id_comunidad = ?", [$id_comunidad]);
        $processLastId = $update ? 1 : 0;
    } else {
        $processLastId = $objDB->insert("comunidades", $_data);
    }

    echo ($processLastId > 0) ? 1 : "Error al guardar la comunidad.";
} catch (Exception $ex) {
    if ($ex->getCode() == 23000) {
        echo "Esta comunidad ya existe. Selecciónela en la pantalla anterior, luego haga clic en Comunidad y asóciela al estado, municipio y parroquia correspondiente.";
    } else {
        echo "Error: " . $ex->getMessage();
    }
}
?>
