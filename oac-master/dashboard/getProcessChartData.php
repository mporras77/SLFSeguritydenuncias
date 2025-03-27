<?php
session_start();
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
    header("Location: ../usuarios/index.php");
    exit();
}

require_once '../spoon/spoon.php';

$objDB = new DBConexion();

// Validación de parámetros GET
$year = isset($_GET['year']) ? intval($_GET['year']) : 0;
$atenciones = isset($_GET['atenciones']) ? intval($_GET['atenciones']) : 0;

if ($year <= 0) {
    exit("Parámetro 'year' inválido o faltante.");
}

// Si se solicitan solo atenciones
if ($atenciones) {
    $query = "SELECT COUNT(id_atencion) AS total FROM atenciones WHERE year = ?";
    $rs = $objDB->getRecord($query, [$year]);
    
    $data = [
        "cols" => [
            ["label" => "Atenciones", "type" => "string"],
            ["label" => "Total", "type" => "number"]
        ],
        "rows" => [
            ["c" => [["v" => "Atenciones"], ["v" => intval($rs['total'])]]]
        ]
    ];
    
    echo json_encode($data);
    exit();
}

// Consulta única para obtener todas las estadísticas
$query = "SELECT 
            (SELECT COUNT(id_denuncia) FROM denuncias WHERE year = ?) AS total_denuncias,
            (SELECT COUNT(id_solicitud) FROM solicitudes WHERE year = ?) AS total_solicitudes,
            (SELECT COUNT(id_reclamo) FROM reclamos WHERE year = ?) AS total_reclamos";

$rs = $objDB->getRecord($query, [$year, $year, $year]);

// Conversión de valores a enteros
$totDenuncias = intval($rs['total_denuncias']);
$totSolicitudes = intval($rs['total_solicitudes']);
$totReclamos = intval($rs['total_reclamos']);

// Estructura de respuesta JSON
$data = [
    "cols" => [
        ["label" => "Procesos", "type" => "string"],
        ["label" => "Total por proceso", "type" => "number"]
    ],
    "rows" => [
        ["c" => [["v" => "Solicitudes"], ["v" => $totSolicitudes]]],
        ["c" => [["v" => "Denuncias"], ["v" => $totDenuncias]]],
        ["c" => [["v" => "Reclamos"], ["v" => $totReclamos]]]
    ]
];

echo json_encode($data);
?>
