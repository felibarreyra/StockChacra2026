<?php
require_once __DIR__ . '/../models/modelproducto.php';
date_default_timezone_set('America/Argentina/Buenos_Aires');

class StockController {
    
    public function stockActual() {
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

        // 🔹 Agregar promedio de consumo a cada producto
        foreach ($productos as &$p) {
            $p['promedio_consumo'] = $productoModel->obtenerPromedioConsumo($p['id']);
        }
        unset($p); // buena práctica al modificar por referencia
    
        $areas = $productoModel->obtenerAreas();
        include __DIR__ . '/../views/stock/actual.php';
    }

    public function formularioConsumoMasivo() {
        global $pdo;
        $productoModel = new Producto($pdo);

        $id_area = $_GET['id_area'] ?? null;

        if ($id_area) {
            $productos = $productoModel->obtenerProductosPorArea($id_area);
        } else {
            $productos = $productoModel->obtenerStockPorArea();
        }

        $areas = $productoModel->obtenerAreas();

        include __DIR__ . '/../views/consumo/formulario_consumo_masivo.php';
    }

    public function guardarConsumoMasivo() {
    global $pdo;
    $productoModel = new Producto($pdo);

    $fecha = $_POST['fecha_consumo'] ?? date('Y-m-d');
    $ids = $_POST['id_producto'] ?? [];
    $nuevos_stocks = $_POST['nuevo_stock'] ?? [];

    if (!is_array($ids)) $ids = [$ids];
    if (!is_array($nuevos_stocks)) $nuevos_stocks = [$nuevos_stocks];

    foreach ($ids as $index => $id_producto) {
        $nuevo_stock = floatval($nuevos_stocks[$index] ?? 0);

        if ($nuevo_stock < 0) continue; // ignorar inválidos

        // 🔹 Traer producto actual
        $producto = $productoModel->obtenerProductoPorId($id_producto);
        if (!$producto) continue;

        $stock_anterior = floatval($producto['cantidad_actual']);
        $consumo = $stock_anterior - $nuevo_stock;

        // 🔹 Registrar consumo si corresponde
        if ($consumo > 0) {
            $productoModel->registrarConsumo($id_producto, $fecha, $consumo);
        }

        // 🔹 Actualizar stock al valor nuevo
        $productoModel->actualizarStock($id_producto, $nuevo_stock);
    }

    $id_area = $_GET['id_area'] ?? '';
    header("Location: index.php?seccion=formulario_consumo_masivo&exito=1&id_area=$id_area");
    exit;
}




    public function verConsumos() {
        global $pdo;
        $productoModel = new Producto($pdo);
    
        $fecha_sabado = $_GET['fecha_sabado'] ?? null;
        $id_area = $_GET['id_area'] ?? null;
    
        $consumos = $productoModel->obtenerConsumosFiltrados($fecha_sabado, $id_area);
        $areas = $productoModel->obtenerAreas();
    
        include __DIR__ . '/../views/consumo/ver_consumos.php';
    }
    
    public function formularioAgregarProducto() {
        global $pdo;
        $productoModel = new Producto($pdo);
        $areas = $productoModel->obtenerAreas();
        require 'views/stock/formulario_agregar_producto.php';
    }
    
    public function guardarNuevoProducto() {
        global $pdo;
        $productoModel = new Producto($pdo);
    
        $nombre = $_POST['nombre'];
        $unidad = $_POST['unidad'];
        $cantidad = $_POST['cantidad'];
        $gasto = $_POST['gasto_x_sabado'];
        $id_area = $_POST['id_area'];
    
        if ($productoModel->agregarProducto($nombre, $unidad, $cantidad, $gasto, $id_area)) {
            header("Location: index.php?seccion=formulario_agregar_producto&success=1");
            exit;
        } else {
            header("Location: index.php?seccion=formulario_agregar_producto&error=1");
            exit;
        }
    }
    
    public function formularioAgregarArea() {
        global $pdo;
        $productoModel = new Producto($pdo);
        $areas = $productoModel->obtenerAreas();
        require 'views/stock/formulario_agregar_area.php';
    }

    public function guardarNuevaArea() {
        global $pdo;
        $productoModel = new Producto($pdo);
    
        $nombre = $_POST['nombre'];
    
        if ($productoModel->agregarArea($nombre)) {
            echo "<p style='color:green; text-align:center;'>✅ Área agregada correctamente.</p>";
        } else {
            echo "<p style='color:red; text-align:center;'>❗ Error al agregar el área.</p>";
        }
    }

    public function eliminarProducto() {
        global $pdo;
        $productoModel = new Producto($pdo);
    
        $id_producto = $_POST['id_producto'] ?? null;
    
        if ($id_producto) {
            if ($productoModel->eliminarProducto($id_producto)) {
                echo "<p style='color:green; text-align:center;'>✅ Producto eliminado correctamente.</p>";
            } else {
                echo "<p style='color:red; text-align:center;'>❗ Error al eliminar el producto.</p>";
            }
        } else {
            echo "<p style='color:red; text-align:center;'>❗ ID de producto no proporcionado.</p>";
        }
    }

    public function actualizarGasto() {
        global $pdo;
        $productoModel = new Producto($pdo);

        $id = $_POST['id_producto'];
        $nuevoGasto = $_POST['nuevo_gasto'];

        if (is_numeric($nuevoGasto) && $nuevoGasto >= 0) {
            $productoModel->actualizarGastoPorSabado($id, $nuevoGasto);
            header("Location: index.php?seccion=stock");
            exit;
        } else {
            echo "<p style='color:red;text-align:center;'>❌ Gasto inválido.</p>";
        }
    }

    public function actualizarStockManual() {
        global $pdo;
        $productoModel = new Producto($pdo);

        $id_producto = $_POST['id_producto'] ?? null;
        $nuevo_stock = $_POST['nuevo_stock'] ?? null;

        if (!$id_producto || !is_numeric($nuevo_stock) || $nuevo_stock < 0) {
            echo "<p style='color:red; text-align:center;'>❌ Valor de stock inválido.</p>";
            return;
        }

        if ($productoModel->actualizarStock($id_producto, $nuevo_stock)) {
            header("Location: index.php?seccion=stock&actualizado=1");
            exit;
        } else {
            echo "<p style='color:red; text-align:center;'>❌ Error al actualizar el stock.</p>";
        }
    }

    public function eliminarArea() {
        global $pdo;
        $productoModel = new Producto($pdo);

        $id = $_POST['id_area'] ?? null;

        if ($id && is_numeric($id)) {
            if ($productoModel->eliminarArea($id)) {
                header("Location: index.php?seccion=formulario_agregar_area&eliminado=1");
                exit;
            } else {
                echo "<p style='color:red; text-align:center;'>❌ No se pudo eliminar el área.</p>";
            }
        } else {
            echo "<p style='color:red; text-align:center;'>❌ ID inválido.</p>";
        }
    }

    public function cambiarAreaProducto() {
        global $pdo;
        $productoModel = new Producto($pdo);

        $id_producto = $_POST['id_producto'] ?? null;
        $id_area = $_POST['id_area'] ?? null;

        if ($id_producto && $id_area && is_numeric($id_producto) && is_numeric($id_area)) {
            if ($productoModel->cambiarArea($id_producto, $id_area)) {
                header("Location: index.php?seccion=stock&cambio_area=1");
                exit;
            } else {
                echo "<p style='color:red; text-align:center;'>❌ Error al cambiar el área del producto.</p>";
            }
        } else {
            echo "<p style='color:red; text-align:center;'>❌ Datos inválidos para cambiar área.</p>";
        }
    }
    public function recalcularGastos() {
    global $pdo;
    $productoModel = new Producto($pdo);

    if ($productoModel->actualizarGastoTodosConPromedio()) {
        header("Location: index.php?seccion=stock&recalculo=1");
        exit;
    } else {
        echo "<p style='color:red; text-align:center;'>❌ Error al recalcular los gastos.</p>";
    }
}


}
