<?php
require_once __DIR__ . '/../config/db.php';

class Producto {
    private $pdo;
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function obtenerStockPorArea() {
        $sql = "SELECT p.*, a.nombre AS nombre_area, a.id AS id_area
                FROM productos p
                JOIN areas a ON p.id_area = a.id
                ORDER BY a.nombre, p.nombre";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }
    public function obtenerStockActual() {
        $sql = "SELECT p.*, a.nombre AS nombre_area, a.id AS id_area
                FROM productos p
                JOIN areas a ON p.id_area = a.id
                ORDER BY a.nombre, p.nombre";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function obtenerProductosPorArea($id_area) {
        $sql = "SELECT p.*, a.nombre AS nombre_area, a.id AS id_area
                FROM productos p
                JOIN areas a ON p.id_area = a.id
                WHERE a.id = ?
                ORDER BY p.nombre";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_area]);
        return $stmt->fetchAll();
    }

    public function obtenerNombreArea($id_area) {
        $stmt = $this->pdo->prepare("SELECT nombre FROM areas WHERE id = ?");
        $stmt->execute([$id_area]);
        return $stmt->fetchColumn();
    }

    public function obtenerAreas() {
        $stmt = $this->pdo->query("SELECT * FROM areas ORDER BY nombre");
        return $stmt->fetchAll();
    }
    public function sumarStock($id_producto, $cantidad) {
        $stmt = $this->pdo->prepare("
            UPDATE productos 
            SET cantidad_actual = cantidad_actual + ?, 
            fecha_ingreso = NOW()
            WHERE id = ?");
        $stmt->execute([$cantidad, $id_producto]);
    }
    
    public function restarStock($idProducto, $cantidad) {
        $stmt = $this->pdo->prepare("UPDATE productos SET cantidad_actual = cantidad_actual - ? WHERE id = ?");
        $stmt->execute([$cantidad, $idProducto]);
    }

    public function obtenerConsumosFiltrados($fecha = null, $id_area = null) {
        $sql = "SELECT c.*, p.nombre AS nombre_producto, p.id_area, a.nombre AS nombre_area
                FROM consumos c
                JOIN productos p ON c.id_producto = p.id
                JOIN areas a ON p.id_area = a.id
                WHERE 1=1";
    
        $params = [];
    
        if ($fecha) {
            $sql .= " AND c.fecha = ?";
            $params[] = $fecha;
        }
    
        if ($id_area) {
            $sql .= " AND p.id_area = ?";
            $params[] = $id_area;
        }
    
        $sql .= " ORDER BY c.fecha DESC";
    
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function registrarConsumo($id_producto, $fecha, $cantidad) {
        $stmt = $this->pdo->prepare("INSERT INTO consumos (id_producto, fecha, cantidad) VALUES (?, ?, ?)");
        return $stmt->execute([$id_producto, $fecha, $cantidad]);
    }
    public function obtenerProductoPorId($id_producto) {
        $stmt = $this->pdo->prepare("SELECT * FROM productos WHERE id = ?");
        $stmt->execute([$id_producto]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function agregarProducto($nombre, $unidad, $cantidad_inicial, $gasto_x_sabado, $id_area) {
        $stmt = $this->pdo->prepare("
            INSERT INTO productos (nombre, unidad_medida, cantidad_actual, gasto_x_sabado, id_area, fecha_ingreso)
            VALUES (UPPER(?), UPPER(?), ?, ?, ?, NOW())
        ");
        return $stmt->execute([$nombre, $unidad, $cantidad_inicial, $gasto_x_sabado, $id_area]);
    }
    public function agregarArea($nombre) {
        $stmt = $this->pdo->prepare("INSERT INTO areas (nombre) VALUES (?)");
        return $stmt->execute([$nombre]);
    }

    public function eliminarProducto($id_producto) {
        $stmt = $this->pdo->prepare("DELETE FROM productos WHERE id = ?");
        return $stmt->execute([$id_producto]);
    }
    public function eliminarConsumo($id_consumo) {
        $stmt = $this->pdo->prepare("DELETE FROM consumos WHERE id = ?");
        return $stmt->execute([$id_consumo]);
    }
    public function actualizarGastoPorSabado($id_producto, $nuevo_gasto) {
        $stmt = $this->pdo->prepare("UPDATE productos SET gasto_x_sabado = ? WHERE id = ?");
        return $stmt->execute([$nuevo_gasto, $id_producto]);
    }
    public function actualizarStock($id_producto, $nuevo_stock) {
    $stmt = $this->pdo->prepare("UPDATE productos SET cantidad_actual = ? WHERE id = ?");
    return $stmt->execute([$nuevo_stock, $id_producto]);
    }

   public function buscarPorNombre($nombre) {
    $sql = "SELECT * FROM productos WHERE UPPER(nombre) LIKE UPPER(:nombre)";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['nombre' => "%$nombre%"]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function eliminarArea($id) {
    $stmt = $this->pdo->prepare("DELETE FROM areas WHERE id = ?");
    return $stmt->execute([$id]);
}
public function cambiarArea($id_producto, $id_area) {
    $stmt = $this->pdo->prepare("UPDATE productos SET id_area = ? WHERE id = ?");
    return $stmt->execute([$id_area, $id_producto]);
}
public function obtenerPromedioConsumo($id_producto) {
    $stmt = $this->pdo->prepare("
        SELECT AVG(cantidad) 
        FROM consumos 
        WHERE id_producto = ?
    ");
    $stmt->execute([$id_producto]);
    return $stmt->fetchColumn() ?: 0; // devuelve 0 si no hay consumos
}
public function actualizarGastoTodosConPromedio() {
    $sql = "UPDATE productos p
            LEFT JOIN (
                SELECT id_producto, AVG(cantidad) AS promedio
                FROM consumos
                GROUP BY id_producto
            ) c ON p.id = c.id_producto
            SET p.gasto_x_sabado = COALESCE(c.promedio, 0)";
    return $this->pdo->query($sql);
}







    
}

