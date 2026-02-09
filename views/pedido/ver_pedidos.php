<?php
// Agrupamos por id_pedido
$agrupados = [];

foreach ($pedidos as $fila) {
    $id = $fila['id_pedido'];
    if (!isset($agrupados[$id])) {
        $agrupados[$id] = [
            'nro_remito' => $fila['nro_remito'],
            'fecha' => $fila['fecha'],
            'estado' => $fila['estado'],
            'productos' => []
        ];
    }

    $agrupados[$id]['productos'][] = [
        'nombre_producto' => $fila['nombre_producto'],
        'cantidad_pedida' => $fila['cantidad_pedida'],
        'cantidad_recibida' => $fila['cantidad_recibida'],
        'cantidad_actual' => $fila['cantidad_actual'],
        'fue_pagado' => $fila['fue_pagado'],
        'id_producto' => $fila['id_producto']
    ];
}
?>
<?php function mostrarDecimalLimpio($num) {
    return rtrim(rtrim(number_format($num, 2, '.', ''), '0'), '.');
  } ?>

<div class="container py-4 mt-5">

  <h1 class="text-center mb-4">📋 Pedidos Generados</h1>

  <?php if (!empty($_GET['busqueda'])): ?>
    <p class="text-center text-muted">🔍 Mostrando resultados para: <strong><?= htmlspecialchars($_GET['busqueda']) ?></strong></p>
  <?php endif; ?>

  <!-- Buscador -->
  <form method="GET" action="" class="d-flex justify-content-center mb-4 gap-2">
    <input type="hidden" name="seccion" value="ver_pedidos">
    <input type="text" name="busqueda" class="form-control w-25" placeholder="Buscar por remito o fecha..." value="<?= htmlspecialchars($_GET['busqueda'] ?? '') ?>">
    <button type="submit" class="btn btn-success">🔍 Buscar</button>
  </form>

  <?php if (empty($pedidos)): ?>
    <p class="text-center text-danger">No hay pedidos generados aún.</p>
  <?php else: ?>
    <div class="table-responsive tabla-scroll">
      <table class="table table-bordered table-hover align-middle">
        <thead class="table-success text-center">
          <tr>
            <th>#</th>
            <th>Remito</th>
            <th>Fecha</th>
            <th>Estado</th>
            <th colspan="5">📦 Detalle del Pedido</th>
            <th>Eliminar</th>
            <th>Descargar</th>
          </tr>
        </thead>
        <tbody>
          <?php $i = 1; foreach ($agrupados as $id => $pedido): ?>
            <tr>
              <td><?= $i++ ?></td>
              <td><?= htmlspecialchars($pedido['nro_remito']) ?></td>
              <td><?= htmlspecialchars($pedido['fecha']) ?></td>
              <td class="<?= $pedido['estado'] === 'Pendiente' ? 'text-warning' : 'text-success' ?>">
                <?= htmlspecialchars($pedido['estado']) ?>
              </td>
              <td colspan="5">
                <table class="table table-sm table-bordered mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Producto</th>
                      <th>Pedido</th>
                      <th>Recibido</th>
                      <th>Stock</th>
                      <th>Pago</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($pedido['productos'] as $prod): ?>
                      <tr>
                        <td><?= htmlspecialchars($prod['nombre_producto']) ?></td>
                        <td><?= mostrarDecimalLimpio($prod['cantidad_pedida']) ?></td>
                        <td><?= mostrarDecimalLimpio($prod['cantidad_recibida']) ?></td>
                        <td><?= mostrarDecimalLimpio($prod['cantidad_actual']) ?></td>
                        <td>
                          <?php if ($prod['fue_pagado']): ?>
                            ✅
                          <?php else: ?>
                            <form method="POST" action="index.php?seccion=marcar_pagado" class="d-inline">
                              <input type="hidden" name="id_pedido" value="<?= $id ?>">
                              <input type="hidden" name="id_producto" value="<?= $prod['id_producto'] ?>">
                              <button type="submit" class="btn btn-sm btn-outline-success" title="Marcar como pago">💰</button>
                            </form>
                          <?php endif; ?>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </td>
              <td class="text-center">
                <?php
                  $tieneImpagos = false;
                  foreach ($pedido['productos'] as $prod) {
                      if (!$prod['fue_pagado']) {
                          $tieneImpagos = true;
                          break;
                      }
                  }
                ?>

                <?php if ($pedido['estado'] !== 'Pendiente' && !$tieneImpagos): ?>
                  <form method="POST" action="index.php?seccion=eliminar_pedido"
                        onsubmit="return confirm('¿Estás seguro de eliminar este pedido? Esta acción no se puede deshacer.');"
                        class="d-inline">
                    <input type="hidden" name="id_pedido" value="<?= $id ?>">
                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar Pedido">🗑️</button>
                  </form>
                <?php else: ?>
                  <span class="text-muted" title="No se puede eliminar: <?= $pedido['estado'] === 'Pendiente' ? 'Pedido pendiente' : 'Hay productos sin pagar' ?>">⛔</span>
                <?php endif; ?>
              </td>
              <td class="text-center">
                <a href="generar_pedido_pdf.php?id_pedido=<?= $id ?>" 
                   class="btn btn-sm btn-primary" 
                   target="_blank" 
                   title="Descargar PDF">
                  📄 PDF
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
