<?php
// controllers/PedidoController.php
require_once __DIR__ . '/../models/modelproducto.php';
require_once __DIR__ . '/../models/modelpedido.php';
date_default_timezone_set('America/Argentina/Buenos_Aires');


class PedidoController {
    public function formularioManual() {
        global $pdo;
        $productoModel = new Producto($pdo);

        $id_area = $_GET['id_area'] ?? null;

        if ($id_area) {
            $productos = $productoModel->obtenerProductosPorArea($id_area);
            $areaSeleccionada = $productoModel->obtenerNombreArea($id_area);
        } else {
            $productos = $productoModel->obtenerStockPorArea();
            $areaSeleccionada = null;
        }

        $areas = $productoModel->obtenerAreas();
        include __DIR__ . '/../views/pedido/generarpedido.php';
    }

   public function guardarPedidoManual() {
    global $pdo;
    $pedidoModel = new Pedido($pdo);
    $productosSeleccionados = $_POST['productos'] ?? [];
    $areaNombre = $_POST['area'] ?? 'SinArea'; // obtenemos el área del POST

    if (empty($productosSeleccionados)) {
        $error = urlencode("No seleccionaste ningún producto.");
        header("Location: index.php?seccion=formulario_pedido_manual&error=$error");
        exit;
    }

    // Limpiar nombre del área
    $areaNombreLimpio = preg_replace("/[^a-zA-Z0-9]/", "", $areaNombre);

    // Crear remito con formato REM-AREA-YYYYMMDD-His
    $nro_remito = 'REM-' . strtoupper($areaNombreLimpio) . '-' . date('Ymd-His');

    $fecha = date('Y-m-d');

    $idPedido = $pedidoModel->crearPedido($nro_remito, $fecha);

    foreach ($productosSeleccionados as $idProducto => $cantidad) {
        if (is_numeric($cantidad) && $cantidad > 0) {
            $pedidoModel->agregarDetalle($idPedido, $idProducto, $cantidad);
        }
    }

    header("Location: index.php?seccion=formulario_pedido_manual&success=1&remito=" . urlencode($nro_remito));
    exit;
}



    
    
    public function verPedidos() {
        global $pdo;
        $pedidoModel = new Pedido($pdo);
        $busqueda = $_GET['busqueda'] ?? null;
        $pedidos = $pedidoModel->obtenerTodosLosPedidos($busqueda);
        include __DIR__ . '/../views/pedido/ver_pedidos.php';
        
    }
    public function formularioRecepcion() {
        global $pdo;
        $pedidoModel = new Pedido($pdo);
    
        $pedidosPendientes = $pedidoModel->obtenerPedidosPendientes();
        $detalle = null;
        $pedidoSeleccionado = null;
    
        if (isset($_POST['buscar'])) {
            $pedidoSeleccionado = $_POST['id_pedido'];
            $detalle = $pedidoModel->obtenerDetallePedido($pedidoSeleccionado);
        }
    
        include __DIR__ . '/../views/pedido/agregar_pedido.php';
    }
    
    public function guardarRecepcion() {
        global $pdo;
        $pedidoModel = new Pedido($pdo);
        $productoModel = new Producto($pdo);
    
        $id_pedido = $_POST['id_pedido'];
        $cantidades = $_POST['recibido'] ?? [];
        $pagados = $_POST['pagado'] ?? [];
    
        foreach ($cantidades as $id_producto => $cantidad_recibida) {
            $pagado = isset($pagados[$id_producto]) ? 1 : 0;
    
            // actualizar detalle del pedido
            $pedidoModel->actualizarDetalleRecepcion($id_pedido, $id_producto, $cantidad_recibida, $pagado);
    
            // sumar al stock actual
            $productoModel->sumarStock($id_producto, $cantidad_recibida);
        }
    
        $pedidoModel->marcarPedidoComoRecibido($id_pedido);
    
       // Redirigir con mensaje de éxito
       header("Location: index.php?seccion=agregar_pedido&exito=1&remito=$id_pedido");
        exit;
    }
    public function marcarComoPagado() {
        global $pdo;
        $pedidoModel = new Pedido($pdo);
    
        $id_pedido = isset($_POST['id_pedido']) ? intval($_POST['id_pedido']) : null;
        $id_producto = isset($_POST['id_producto']) ? intval($_POST['id_producto']) : null;
    
        if ($id_pedido && $id_producto) {
            if ($pedidoModel->marcarDetalleComoPagado($id_pedido, $id_producto)) {
                header("Location: index.php?seccion=ver_pedidos");
                exit;
            } else {
                echo "<p style='text-align:center; color:red;'>❌ Error: no se pudo marcar como pagado.</p>";
            }
        } else {
            echo "<p style='text-align:center; color:red;'>❌ Error: faltan datos para marcar como pagado.</p>";
        }
    }
    
    
    
    
    
}

