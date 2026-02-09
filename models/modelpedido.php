<?php
require_once __DIR__ . '/../config/db.php';

class Pedido {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function crearPedido($nro_remito, $fecha) {
        $stmt = $this->pdo->prepare("INSERT INTO pedidos (nro_remito, fecha, estado) VALUES (?, ?, 'Pendiente')");
        $stmt->execute([$nro_remito, $fecha]);
        return $this->pdo->lastInsertId();
    }

    public function agregarDetalle($idPedido, $idProducto, $cantidadPedida) {
        $stmt = $this->pdo->prepare("INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad_pedida) VALUES (?, ?, ?)");
        $stmt->execute([$idPedido, $idProducto, $cantidadPedida]);
    }

    public function obtenerTodosLosPedidos($busqueda = null) {
    $sql = "SELECT 
                p.id AS id_pedido, 
                p.nro_remito, 
                p.fecha, 
                p.estado, 
                d.id_producto, 
                d.cantidad_pedida, 
                d.cantidad_recibida, 
                d.fue_pagado, 
                pr.nombre AS nombre_producto, 
                pr.cantidad_actual
            FROM pedidos p
            LEFT JOIN detalle_pedido d ON p.id = d.id_pedido
            LEFT JOIN productos pr ON d.id_producto = pr.id";

    $params = [];

    if (!empty($busqueda)) {
        $sql .= " WHERE p.nro_remito LIKE :busqueda OR p.fecha LIKE :busqueda";
        $params['busqueda'] = '%' . $busqueda . '%';
    }

    $sql .= " ORDER BY 
                (p.estado = 'Pendiente') DESC, 
                p.fecha DESC, 
                p.nro_remito";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    public function obtenerConsumoPorFecha($fecha) {
        $stmt = $this->pdo->prepare("
            SELECT p.nombre, c.cantidad, c.fecha
            FROM consumo c
            JOIN productos p ON c.id_producto = p.id
            WHERE DATE(c.fecha) = ?");
        $stmt->execute([$fecha]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function obtenerPedidosPendientes() {
        $stmt = $this->pdo->query("SELECT * FROM pedidos WHERE estado = 'Pendiente'");
        return $stmt->fetchAll();
    }

    public function obtenerDetallePedido($id_pedido) {
        $stmt = $this->pdo->prepare("
            SELECT dp.*, p.nombre 
            FROM detalle_pedido dp
            JOIN productos p ON dp.id_producto = p.id
            WHERE dp.id_pedido = ?");
        $stmt->execute([$id_pedido]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarDetalleRecepcion($id_pedido, $id_producto, $recibido, $pagado) {
        $stmt = $this->pdo->prepare("
            UPDATE detalle_pedido 
            SET cantidad_recibida = ?, fue_pagado = ? 
            WHERE id_pedido = ? AND id_producto = ?");
        $stmt->execute([$recibido, $pagado, $id_pedido, $id_producto]);
    }

    public function marcarPedidoComoRecibido($id_pedido) {
        $stmt = $this->pdo->prepare("UPDATE pedidos SET estado = 'Recibido' WHERE id = ?");
        $stmt->execute([$id_pedido]);
    }
    public function marcarDetalleComoPagado($id_pedido, $id_producto) {
        $stmt = $this->pdo->prepare("
            UPDATE detalle_pedido 
            SET fue_pagado = 1 
            WHERE id_pedido = ? AND id_producto = ?");
        return $stmt->execute([$id_pedido, $id_producto]);
    }
    public function eliminarPedido($idPedido) {
        // Eliminar los detalles del pedido
        $stmt = $this->pdo->prepare("DELETE FROM detalle_pedido WHERE id_pedido = ?");
        $stmt->execute([$idPedido]);
    
        // Eliminar el pedido
        $stmt = $this->pdo->prepare("DELETE FROM pedidos WHERE id = ?");
        $stmt->execute([$idPedido]);
    }
    public function obtenerAreaDePedido($id_pedido) {
    $stmt = $this->pdo->prepare("
        SELECT a.nombre AS area
        FROM detalle_pedido dp
        JOIN productos pr ON dp.id_producto = pr.id
        JOIN areas a ON pr.id_area = a.id
        WHERE dp.id_pedido = ?
        LIMIT 1
    ");
    $stmt->execute([$id_pedido]);
    return $stmt->fetchColumn();
}

    
    
    
}

