<?php
// 👉 Solo define esto si tenés problemas de ruta con las fuentes
define('FPDF_FONTPATH', __DIR__ . '/pdf/fpdf/font');

require('./pdf/fpdf/fpdf.php');
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/models/modelpedido.php';

// Validar ID pedido
if (!isset($_GET['id_pedido'])) {
    die("ID de pedido no proporcionado");
}

$id_pedido = intval($_GET['id_pedido']);

$pedidoModel = new Pedido($pdo);

// 🔹 Obtener info del pedido
$sqlPedido = "SELECT nro_remito, fecha FROM pedidos WHERE id = :id";
$stmtPedido = $pdo->prepare($sqlPedido);
$stmtPedido->execute(['id' => $id_pedido]);
$pedido = $stmtPedido->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
    die("Pedido no encontrado");
}

// 🔹 Obtener área del pedido (usa el método del model)
$areaPedido = $pedidoModel->obtenerAreaDePedido($id_pedido);

// 🔹 Obtener detalles del pedido
$sqlDetalles = "SELECT pr.nombre, dp.cantidad_pedida 
                FROM detalle_pedido dp
                JOIN productos pr ON pr.id = dp.id_producto
                WHERE dp.id_pedido = :id";
$stmtDetalles = $pdo->prepare($sqlDetalles);
$stmtDetalles->execute(['id' => $id_pedido]);

// ----------------- CLASE PDF -----------------
class PDF extends FPDF {
    function Header() {
        // Logo
        $this->Image('./img/chacra.png', 10, 8, 30); 
        $this->Ln(25);
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, utf8_decode('Detalle del Pedido'), 0, 1, 'C');
        $this->Ln(5);
    }
}

// ----------------- CREAR PDF -----------------
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

$pdf->Cell(0, 10, utf8_decode("Remito: ") . $pedido['nro_remito'], 0, 1);
$pdf->Cell(0, 10, utf8_decode("Fecha: ") . $pedido['fecha'], 0, 1);
$pdf->Cell(0, 10, utf8_decode("Proveedor: ") . ($areaPedido ?? "N/D"), 0, 1);
$pdf->Ln(8);

// Encabezado tabla
$pdf->SetFillColor(60, 116, 36);
$pdf->SetTextColor(255);
$pdf->Cell(120, 10, 'Producto', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Cantidad Pedida', 1, 1, 'C', true);
$pdf->SetTextColor(0);

// Función para mostrar números sin ceros innecesarios
function mostrarDecimalLimpio($num) {
    return rtrim(rtrim(number_format($num, 2, '.', ''), '0'), '.');
}

// Contenido tabla
while ($row = $stmtDetalles->fetch(PDO::FETCH_ASSOC)) {
    $pdf->Cell(120, 10, utf8_decode($row['nombre']), 1);
    $pdf->Cell(40, 10, mostrarDecimalLimpio($row['cantidad_pedida']), 1, 1, 'C');
}

// ----------------- NOMBRE DEL ARCHIVO -----------------
$fecha = date('Ymd', strtotime($pedido['fecha']));
$areaLimpia = strtoupper(str_replace(' ', '_', $areaPedido ?? "GENERAL"));
$nombreArchivo = "REM_" . $areaLimpia . "_" . $fecha . ".pdf";

$pdf->Output('I', $nombreArchivo);
