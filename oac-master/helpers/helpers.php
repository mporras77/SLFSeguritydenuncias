<?php
session_start();

if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
    header("Location: ../usuarios/");
    exit();
}

include '../spoon/spoon.php';
$objDB = new DBConexion();

function get_fullname($txtcedula) {
    global $objDB;
    $query = "SELECT * FROM ciudadanos WHERE cedula = ?";
    $rs = $objDB->getRecord($query, [$txtcedula]);

    if ($rs) {
        $id_ciudadano = $rs['id_ciudadano'];
        
        $queries = [
            'denuncias' => "SELECT COUNT(id_denuncia) AS total FROM denuncias WHERE id_ciudadano = ?",
            'solicitudes' => "SELECT COUNT(id_solicitud) AS total FROM solicitudes WHERE id_ciudadano = ?",
            'reclamos' => "SELECT COUNT(id_reclamo) AS total FROM reclamos WHERE id_ciudadano = ?"
        ];
        
        $totals = [];
        foreach ($queries as $key => $query) {
            $result = $objDB->getRecord($query, [$id_ciudadano]);
            $totals[$key] = $result['total'] ?? 0;
        }
        
        return array_merge($rs, $totals);
    }
    return null;
}

function getLocations($table, $column, $id) {
    global $objDB;
    $query = "SELECT id_$table, $table FROM $table WHERE $column = ? ORDER BY $table ASC";
    return $objDB->getRecords($query, [$id]) ?: "no hay";
}

function get_totalPorProceso($comunidad) {
    global $objDB;
    $queries = [
        'denuncias' => "SELECT COUNT(id_denuncia) AS total FROM denuncias WHERE comunidad = ?",
        'solicitudes' => "SELECT COUNT(id_solicitud) AS total FROM solicitudes WHERE comunidad = ?",
        'reclamos' => "SELECT COUNT(id_reclamo) AS total FROM reclamos WHERE comunidad = ?"
    ];
    
    $totals = [];
    foreach ($queries as $key => $query) {
        $result = $objDB->getRecord($query, [$comunidad]);
        $totals[$key] = $result['total'] ?? 0;
    }
    
    return $totals;
}

function get_totalPorProcesoPorFecha($fechaInicial, $fechaFinal) {
    global $objDB;
    $queries = [
        'denuncias' => "SELECT COUNT(id_denuncia) AS total FROM denuncias WHERE fecha_registro BETWEEN ? AND ?",
        'solicitudes' => "SELECT COUNT(id_solicitud) AS total FROM solicitudes WHERE fecha_registro BETWEEN ? AND ?",
        'reclamos' => "SELECT COUNT(id_reclamo) AS total FROM reclamos WHERE fecha_registro BETWEEN ? AND ?"
    ];
    
    $totals = [];
    foreach ($queries as $key => $query) {
        $result = $objDB->getRecord($query, [$fechaInicial, $fechaFinal]);
        $totals[$key] = $result['total'] ?? 0;
    }
    
    return $totals;
}

if (!empty($_POST['txtcedula'])) {
    echo json_encode(get_fullname($_POST['txtcedula']));
    exit();
}

if (!empty($_POST['fechainicial']) && !empty($_POST['fechafinal'])) {
    $fechaInicial = date('Y-m-d', strtotime(str_replace('/', '-', $_POST['fechainicial'])));
    $fechaFinal = date('Y-m-d', strtotime(str_replace('/', '-', $_POST['fechafinal'])));
    echo json_encode(get_totalPorProcesoPorFecha($fechaInicial, $fechaFinal));
    exit();
}

if (!empty($_POST['comunidad'])) {
    echo json_encode(get_totalPorProceso($_POST['comunidad']));
    exit();
}

if (!empty($_GET['id_estado'])) {
    echo json_encode(getLocations('municipios', 'id_estado', $_GET['id_estado']));
    exit();
}

if (!empty($_GET['id_municipio'])) {
    echo json_encode(getLocations('parroquias', 'id_municipio', $_GET['id_municipio']));
    exit();
}

if (!empty($_GET['id_parroquia'])) {
    echo json_encode(getLocations('comunidades', 'id_parroquia', $_GET['id_parroquia']));
    exit();
}
?>
