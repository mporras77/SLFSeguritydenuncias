<?php
require_once "../spoon/spoon.php";
require_once('../tcpdf/tcpdf.php');

session_start();
$objDB = new DBConexion();

if (isset($_GET['id'], $_GET['proceso'], $_GET['tabla'])) {
    $id = intval($_GET['id']); // Asegurar que sea un número entero
    $proceso = htmlspecialchars($_GET['proceso']); // Evitar XSS
    $tabla = htmlspecialchars($_GET['tabla']);

    // Usar consulta preparada para evitar inyección SQL
    $query = "SELECT * FROM $tabla 
              INNER JOIN ciudadanos ON $tabla.id_ciudadano = ciudadanos.id_ciudadano 
              WHERE $tabla.id_$proceso = ?";
    
    $rs = $objDB->getRecord($query, [$id]);
} else {
    die("Error: Faltan parámetros en la URL.");
}

// Crear PDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'LETTER', true, 'UTF-8', false);
$pdf->setHeaderData('oac_logo.png', '180.40');
$pdf->setHeaderFont([PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN]);
$pdf->setFooterFont([PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA]);
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->SetFont('dejavusans', '', 8);
$pdf->AddPage();

setlocale(LC_TIME, 'es_VE.UTF-8');
date_default_timezone_set('America/Caracas');
$fecha = strftime("%A %d de %B de %Y", strtotime(date('Y-m-d')));

$html = '<h4 style="text-align:left;">Barinas, ' . $fecha . '</h4>';
$pdf->writeHTML($html, true, 0, true, 0, '');

$html = '<br><br><br><h2 style="text-align:center;">Recepción de ' . ucfirst($proceso) . '</h2>';
$pdf->writeHTML($html, true, 0, true, 0, '');

$html = '<br><br><br><br>'
      . '<p style="text-align:justify;"><b>Narración de los hechos:</b> ' . htmlspecialchars($rs['narracion_hechos']) . '</p><br>'
      . '<div style="text-align:justify;"><b>Observaciones:</b> ' . htmlspecialchars($rs['observaciones']) . '</div><br>';
$pdf->writeHTML($html, true, 0, true, 0, '');

$y = 110;
$h = 7;
$border = 0;

$pdf->Ln();
$pdf->writeHTMLCell(120, $h, 14, $y-12, '<h3>Datos del Ciudadano</h3>', $border);
$pdf->Line(15, 105, 195, 105);

$last_id = ucfirst(substr($proceso, 0, 1)) . '-' . str_pad($id, 3, "0", STR_PAD_LEFT) . '-' . $rs['year'];

$pdf->writeHTMLCell(40, $h, 175, $y - 83, '<b>OAC-' . $last_id . '</b>', $border);
$pdf->writeHTMLCell(40, $h, 20, $y, '<b>Cédula:</b> ' . number_format($rs['cedula'], 0, ',', '.'), $border);
$pdf->writeHTMLCell(65, $h, 60, $y, '<b>Apellidos:</b> ' . htmlspecialchars($rs['apellidos']), $border);
$pdf->writeHTMLCell(65, $h, 125, $y, '<b>Nombres:</b> ' . htmlspecialchars($rs['nombres']), $border);
$pdf->writeHTMLCell(85, $h, 20, $y + $h, '<b>Teléfonos:</b> ' . htmlspecialchars($rs['telefonos']), $border);
$pdf->writeHTMLCell(65, $h, 125, $y + $h, '<b>Correo:</b> ' . htmlspecialchars($rs['correo']), $border);
$pdf->writeHTMLCell(170, $h, 20, $y + ($h * 2), '<b>Dirección:</b> ' . htmlspecialchars($rs['direccion']), $border);

$pdf->Line(85, 148, 120, 148);
$pdf->writeHTMLCell(28, $h, 90, $y + 41, number_format($rs['cedula'], 0, ',', '.'), $border, 0, false, true, 'C');
$pdf->writeHTMLCell(58, $h, 74, $y + 46, htmlspecialchars($rs['apellidos']) . ', ' . htmlspecialchars($rs['nombres']), $border, 0, false, true, 'C');

$pdf->Ln();
$pdf->writeHTML('<br><br><h3>Recibido en la OAC</h3>', true, 0, true, 0, '');
$pdf->Line(15, 180, 195, 180);

$pdf->writeHTMLCell(65, $h, 20, $y + ($h * 10) + 5, '<b>Funcionario:</b> ' . utf8_encode($_SESSION['nombre']), $border);
$pdf->writeHTMLCell(65, $h, 20, $y + ($h * 11) + 5, '<b>Código de proceso:</b> ' . $last_id, $border);

$pdf->Line(45, 218, 80, 218);
$pdf->writeHTMLCell(48, $h, 40, $y + 110, "Jefe o Coordinador", $border, 0, false, true, 'C');
$pdf->writeHTMLCell(60, $h, 33, $y + 115, 'Oficina de Atención al Ciudadano', $border, 0, false, true, 'C');

$pdf->Line(125, 218, 160, 218);
$pdf->writeHTMLCell(48, $h, 120, $y + 110, utf8_encode($_SESSION['nombre']), $border, 0, false, true, 'C');
$pdf->writeHTMLCell(60, $h, 113, $y + 115, 'Oficina de Atención al Ciudadano', $border, 0, false, true, 'C');

$html = '<br><br><br><br><br><br><br><i> '
      . 'Si no se presenta el o los requisitos faltantes, '
      . 'detallados arriba en <b>Observaciones,</b> dentro de los quince (15) días '
      . 'siguientes, contados a partir de la fecha impresa en esta comunicación, '
      . 'se considerará la solicitud, reclamo o denuncia defectuosa.</i> ';
$pdf->writeHTML($html, true, 0, true, 0, '');

$pdf->lastPage();
$pdf->Output($proceso . $id . '.pdf', 'I');
