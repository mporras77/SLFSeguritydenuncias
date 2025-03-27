<?php
session_start();
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
    header("Location: ../usuarios/index.php");
    exit();
}

require_once '../spoon/spoon.php';

$objDB = new DBConexion();

// Función para obtener colores dinámicamente
function getColor($indice) {
    $colores = ['#109618', '#ff9900', '#3366cc', '#dc3912', 'Brown'];
    return $colores[$indice % count($colores)];
}

// Validación y sanitización de parámetros GET
$tipo_proceso = isset($_GET['tipoProceso']) ? filter_var($_GET['tipoProceso'], FILTER_SANITIZE_STRING) : '';
$year = isset($_GET['year']) ? intval($_GET['year']) : 0;
$mes = isset($_GET['mes']) ? filter_var($_GET['mes'], FILTER_SANITIZE_STRING) : '';

if (!$tipo_proceso || !$year || !$mes) {
    exit(json_encode(["error" => "Parámetros inválidos o faltantes."]));
}

// Mapeo de procesos a tablas
$procesos = [
    "Atenciones" => ["tabla" => "atenciones", "id" => "id_atencion"],
    "Denuncias" => ["tabla" => "denuncias", "id" => "id_denuncia"],
    "Solicitudes" => ["tabla" => "solicitudes", "id" => "id_solicitud"],
    "Reclamos" => ["tabla" => "reclamos", "id" => "id_reclamo"]
];

if (!isset($procesos[$tipo_proceso])) {
    exit(json_encode(["error" => "Tipo de proceso inválido."]));
}

$tabla = $procesos[$tipo_proceso]["tabla"];
$id_proceso = $procesos[$tipo_proceso]["id"];

// Configurar localización para nombres de meses en español
$objDB->execute("SET lc_time_names = 'es_VE';");

// Consulta SQL optimizada con parámetros
$query = "SELECT usuarios.nombre, COUNT($tabla.$id_proceso) AS total, usuarios.id_usuario
          FROM $tabla
          INNER JOIN usuarios ON usuarios.id_usuario = $tabla.idusuario
          WHERE MONTHNAME(STR_TO_DATE(MONTH(fecha_registro), '%m')) = ? 
          AND year = ?
          GROUP BY usuarios.id_usuario";

$rs = $objDB->getRecords($query, [$mes, $year]);

$rows = [];
$indice = 0;

foreach ($rs as $data) {
    $rows[] = [
        "c" => [
            ["v" => $data['nombre']],
            ["v" => intval($data['total'])],
            ["v" => intval($data['id_usuario'])],
            ["v" => getColor($indice++)]
        ]
    ];
}

// Estructura de datos para JSON
$data = [
    "cols" => [
        ["label" => "Nombre", "type" => "string"],
        ["label" => "Total por funcionario", "type" => "number"],
        ["label" => "Id Usuario", "type" => "number"],
        ["type" => "string", "role" => "style"]
    ],
    "rows" => $rows
];

echo json_encode($data);
?>
