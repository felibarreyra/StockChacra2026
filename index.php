<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);


// Si no hay usuario logueado, redirige a login.php
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Stock Chacra</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredericka+the+Great&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" type="image/png" href="./img/chacra.png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>
    <?php require_once __DIR__ . '/views/header.php'; ?>
    <?php require_once __DIR__ . '/views/nav.php'; ?>
<?php
require_once __DIR__ . '/config/db.php';
// Verifica si se ha enviado el parámetro 'seccion' en la URL
$seccion = isset($_GET['seccion']) ? $_GET['seccion'] : 'stock';

switch ($seccion) {
    case 'stock':
        require_once __DIR__ . '/controllers/controllerproducto.php';
        $controller = new StockController();
        $controller->stockActual();
    break;

    case 'formulario_pedido_manual':
        require_once __DIR__ . '/controllers/controllerpedido.php';
        $controller = new PedidoController();
        $controller->formularioManual();
    break;

    case 'recalcular_gastos':
    require_once __DIR__ . '/controllers/controllerproducto.php';
    $controller = new StockController();
    $controller->recalcularGastos();
    break;


    case 'guardar_pedido_manual':
        require_once __DIR__ . '/controllers/controllerpedido.php';
        $controller = new PedidoController();
        $controller->guardarPedidoManual();
    break;

    case 'agregar_pedido':
        require_once __DIR__ . '/controllers/controllerpedido.php';
        $controller = new PedidoController();
        $controller->formularioRecepcion();
    break;

    case 'guardar_recepcion':
        require_once __DIR__ . '/controllers/controllerpedido.php';
        $controller = new PedidoController();
        $controller->guardarRecepcion();
    break;

    case 'ver_pedidos':
        require_once __DIR__ . '/controllers/controllerpedido.php';
        $controller = new PedidoController();
        $controller->verPedidos();
    break;
    
    case 'cambiar_area':
    require_once __DIR__ . '/controllers/controllerproducto.php';
    $controller = new StockController();
    $controller->cambiarAreaProducto();
    break;

    
    case 'formulario_consumo_masivo':
    require_once __DIR__ . '/controllers/controllerproducto.php';
    $controller = new StockController();
    $controller->formularioConsumoMasivo();
    break;

    case 'guardar_consumo_masivo':
    require_once __DIR__ . '/controllers/controllerproducto.php';
    $controller = new StockController();
    $controller->guardarConsumoMasivo();
    break;


    case 'marcar_pagado':
        require_once __DIR__ . '/controllers/controllerpedido.php';
        $controller = new PedidoController();
        $controller->marcarComoPagado();
    break;
    case 'ver_consumos':
        require_once __DIR__ . '/controllers/controllerproducto.php'; 
        $controller = new StockController();
        $controller->verConsumos();
        break;
    case 'formulario_agregar_producto':
        require_once __DIR__ . '/controllers/controllerproducto.php'; 
        $controller = new StockController();
        $controller->formularioAgregarProducto();
        break;
    case 'guardar_nuevo_producto':
        require_once __DIR__ . '/controllers/controllerproducto.php'; 
        $controller = new StockController();
        $controller->guardarNuevoProducto();
        break;
        
    case 'formulario_agregar_area':
        require_once __DIR__ . '/controllers/controllerproducto.php'; 
        $controller = new StockController();
        $controller->formularioAgregarArea();
        break;
    case 'guardar_nueva_area':
        require_once __DIR__ . '/controllers/controllerproducto.php'; 
        $controller = new StockController();
        $controller->guardarNuevaArea();
        break;
    case 'eliminar_pedido':
        require_once __DIR__ . '/models/modelpedido.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pedido'])) {
            $idPedido = intval($_POST['id_pedido']);
            $pedidoModel = new Pedido($pdo);
            $pedidoModel->eliminarPedido($idPedido);
            header("Location: index.php?seccion=ver_pedidos");
            exit;
            }
        break;

    case 'eliminar_producto':
        require_once __DIR__ . '/models/modelproducto.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_producto'])) {
            $idProducto = intval($_POST['id_producto']);
            $productoModel = new Producto($pdo);
            $productoModel->eliminarProducto($idProducto);
            header("Location: index.php?seccion=stock");
            exit;
        }
    break;
    case 'eliminar_consumo':
        require_once __DIR__ . '/models/modelproducto.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_consumo'])) {
            $idConsumo = intval($_POST['id_consumo']);
            $productoModel = new Producto($pdo);
            $productoModel->eliminarConsumo($idConsumo);
            header("Location: index.php?seccion=ver_consumos");
            exit;
        }
    break;
     case 'actualizar_gasto':
        require_once __DIR__ . '/controllers/controllerproducto.php';
        $controller = new StockController();
        $controller->actualizarGasto();
        break;
    case 'actualizar_stock':
        require_once __DIR__ . '/controllers/controllerproducto.php';
        $controller = new StockController();
        $controller->actualizarStockManual();
    break;

   case 'eliminar_area':
    require_once __DIR__ . '/models/modelproducto.php';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_area'])) {
        $idArea = intval($_POST['id_area']);
        $productoModel = new Producto($pdo);
        $productoModel->eliminarArea($idArea);
        header("Location: index.php?seccion=formulario_agregar_area");
        exit;
    }
    break;

 
    case 'buscar_producto':
    require_once 'models/modelproducto.php';
    $productoModel = new Producto($pdo);

    $resultados = [];
    if (isset($_GET['buscar'])) {
        $termino = $_GET['buscar'];
        $resultados = $productoModel->buscarPorNombre($termino);
    }

    require 'views/stock/buscar_producto.php';
    break;

    default:
        echo "<p style='text-align:center;'>Sección no encontrada</p>";
        break;
}
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
