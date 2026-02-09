<?php
ob_start(); // Inicia el buffer de salida para evitar errores de encabezados

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('./pdf/fpdf/fpdf.php');
require_once __DIR__ . '/config/db.php';

// Validar parámetros
$fecha = $_GET['fecha_sabado'] ?? null;
$id_area = $_GET['id_area'] ?? null;

if (!$fecha) {
    die("⚠️ Fecha no especificada.");
}

// Consultar consumos
$params = ['fecha' => $fecha];
$sql = "SELECT c.id, c.fecha, c.cantidad, p.nombre AS nombre_producto
        FROM consumos c
        JOIN productos p ON c.id_producto = p.id
        WHERE c.fecha = :fecha";

if (!empty($id_area)) {
    $sql .= " AND p.id_area = :id_area";
    $params['id_area'] = $id_area;
}

$sql .= " ORDER BY p.nombre ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$consumos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$consumos) {
    die("❌ No se encontraron consumos para esa fecha.");
}

// Clase PDF
class PDF extends FPDF {
    function Header() {
        $this->Image('./img/chacra.png', 10, 8, 30); 
        $this->Ln(25);
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, iconv('UTF-8', 'windows-1252', 'Consumos Registrados'), 0, 1, 'C');
        $this->Ln(5);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Página ' . $this->PageNo(), 0, 0, 'C');
    }
}

// Función para limpiar decimales
function mostrarDecimalLimpio($num) {
    return rtrim(rtrim(number_format($num, 2, '.', ''), '0'), '.');
}

// Crear PDF
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// Datos del filtro
$pdf->Cell(0, 10, iconv('UTF-8', 'windows-1252', "Fecha: ") . $fecha, 0, 1);

if ($id_area !== null && $id_area !== '') {
    $stmtArea = $pdo->prepare("SELECT nombre FROM areas WHERE id = ?");
    $stmtArea->execute([$id_area]);
    $nombreArea = $stmtArea->fetchColumn();
    $pdf->Cell(0, 10, iconv('UTF-8', 'windows-1252', "Proveedor: ") . iconv('UTF-8', 'windows-1252', $nombreArea), 0, 1);
}

$pdf->Ln(5);

// Tabla
$pdf->SetFillColor(60, 116, 36);
$pdf->SetTextColor(255);
$pdf->Cell(130, 10, 'Producto', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Cantidad', 1, 1, 'C', true);
$pdf->SetTextColor(0);

foreach ($consumos as $consumo) {
    $pdf->Cell(130, 10, iconv('UTF-8', 'windows-1252', $consumo['nombre_producto']), 1);
    $pdf->Cell(40, 10, mostrarDecimalLimpio($consumo['cantidad']), 1, 1, 'C');
}

// Nombre del archivo
$nombreArchivo = 'consumo_' . $fecha;
if ($id_area !== null && $id_area !== '') {
    $nombreArchivo .= '_area' . $id_area;
}
$nombreArchivo .= '.pdf';

// Finalizar buffer antes de enviar PDF
ob_end_clean();
$pdf->Output('I', $nombreArchivo);
