<?php
session_start();

if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
    header("Location:../usuarios/");
    exit();
}

include '../spoon/spoon.php';
$objDB = new DBConexion();

// Función para obtener el total de procesos
function get_totalPorProceso($objDB) {
    $procesos = ['denuncias', 'solicitudes', 'reclamos'];
    $totalPorProceso = [];

    foreach ($procesos as $proceso) {
        $query = "SELECT COUNT(id_{$proceso}) AS total FROM {$proceso} 
                  WHERE fecha_tope_entrega BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 5 DAY)";
        $result = $objDB->getRecord($query);
        $totalPorProceso[$proceso] = $result['total'] ?? 0;
    }

    return $totalPorProceso;
}

// Si se solicita la cantidad de notificaciones
if (isset($_POST['getNotificaciones']) && !empty($_POST['getNotificaciones'])) {
    echo json_encode(get_totalPorProceso($objDB));
    exit();
}

// Consulta unificada para denuncias, reclamos y solicitudes
$procesos = ['denuncias', 'reclamos', 'solicitudes'];
$queries = [];

foreach ($procesos as $proceso) {
    $queries[] = "SELECT *, id_{$proceso} AS id, '{$proceso}' AS tipo 
                  FROM {$proceso} 
                  INNER JOIN ciudadanos ON {$proceso}.id_ciudadano = ciudadanos.id_ciudadano 
                  WHERE {$proceso}.fecha_tope_entrega 
                  BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 5 DAY)";
}

$finalQuery = implode(" UNION ALL ", $queries);
$rset = $objDB->getRecords($finalQuery) ?: [];

// Función para obtener la inicial del tipo de proceso
function getTipoProceso($tipo_proceso) {
    return strtoupper(substr($tipo_proceso, 0, 1));
}

// Imprimir datos en la tabla
foreach ($rset as $value) {
    $inicial_tipo_proceso = getTipoProceso($value["tipo"]);
    $fecha = date_create_from_format("Y-m-d", $value['fecha_tope_entrega']);
    $fecha_tope_entrega = $fecha ? date_format($fecha, "d/m/Y") : "Fecha no válida";

    $codigo_proceso = "OAC-" . $inicial_tipo_proceso . "-" . htmlspecialchars($value['id']) . "-" . htmlspecialchars($value['year']);

    echo "<tr>
            <td>{$codigo_proceso}</td>
            <td>" . htmlspecialchars($value['observaciones']) . "</td>
            <td>" . htmlspecialchars($value['apellidos']) . ", " . htmlspecialchars($value['nombres']) . "</td>
            <td>" . htmlspecialchars($value['telefonos']) . "</td>
            <td>{$fecha_tope_entrega}</td>
          </tr>";
}
