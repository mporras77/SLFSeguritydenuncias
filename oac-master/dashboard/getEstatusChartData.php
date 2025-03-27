<?php
session_start();
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
    header("Location: ../usuarios/index.php");
    exit();
}

require_once '../spoon/spoon.php';

$objDB = new DBConexion();

/**
 * Función para obtener el color según el índice.
 */
function getColor($indice) {
    $colores = ['#109618', '#ff9900', '#3366cc', '#dc3912', 'Brown'];
    return $colores[$indice - 1] ?? '#000000'; // Color por defecto en caso de error
}

// Validación de parámetros GET
$tipo_proceso = $_GET['tipoProceso'] ?? '';
$year = intval($_GET['year'] ?? 0);
$mes = htmlspecialchars($_GET['mes'] ?? '');
$id_usuario = intval($_GET['id_usuario'] ?? 0);

if (empty($tipo_proceso) || empty($year) || empty($mes) || empty($id_usuario)) {
    exit("Parámetros inválidos o faltantes.");
}

// Mapeo de tipos de proceso a tablas y campos
$procesos = [
    "Atenciones"  => "atenciones",
    "Denuncias"   => "denuncias",
    "Solicitudes" => "solicitudes",
    "Reclamos"    => "reclamos"
];

if (!isset($procesos[$tipo_proceso])) {
    exit("Tipo de proceso inválido.");
}

$tabla = $procesos[$tipo_proceso];
$id_proceso = "id_" . strtolower($tipo_proceso);

// Configuración del lenguaje en MySQL
$objDB->execute("SET lc_time_names = 'es_VE'");

// Consulta SQL para obtener el total de estatus por mes y año
$query = "SELECT estatus, COUNT(estatus) AS total 
          FROM {$tabla}  
          WHERE idusuario = ? 
          AND MONTHNAME(STR_TO_DATE(MONTH(fecha_registro), '%m')) = ?
          AND year = ? 
          GROUP BY estatus";

// Ejecución de la consulta
$rs = $objDB->getRecords($query, [$id_usuario, $mes, $year]);

// Preparación de los datos para JSON
$rows = [];
$indice = 0;

foreach ($rs as $data) {
    $total = intval($data['total']);
    $indice++;
    $color = getColor($indice);
    $rows[] = ["c" => [["v" => $data['estatus']], ["v" => $total], ["v" => $color]]];
}

// Definir columnas para el gráfico
$cols = [
    ["label" => "Estatus", "type" => "string"],
    ["label" => "Total por estatus", "type" => "number"],
    ["type" => "string", "role" => "style"]
];

// Construcción del JSON de respuesta
$data = ["cols" => $cols, "rows" => $rows];
echo json_encode($data);
?>
