<div class="container my-4 agregar-pedido-container">
  <h1 class="mb-4 text-primary">📦 Registrar Llegada de Pedido</h1>

  <?php if (isset($_GET['exito'])): ?>
    <div class="alert alert-success" role="alert">
      ✅ Pedido recibido y stock actualizado correctamente.
    </div>
  <?php endif; ?>

  <form method="POST" action="index.php?seccion=agregar_pedido" class="row g-3 align-items-end mb-4">
    <div class="col-md-8">
      <label for="id_pedido" class="form-label fw-semibold">Seleccionar pedido pendiente:</label>
      <select name="id_pedido" id="id_pedido" required class="form-select">
        <option value="">-- Seleccione --</option>
        <?php foreach ($pedidosPendientes as $pedido): ?>
          <option value="<?= $pedido['id'] ?>" <?= ($pedidoSeleccionado == $pedido['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($pedido['nro_remito']) ?> - <?= htmlspecialchars($pedido['fecha']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-4 d-grid">
      <button type="submit" name="buscar" class="btn btn-primary fw-semibold">🔍 Ver Detalle</button>
    </div>
  </form>

  <?php 
  function mostrarDecimalLimpio($num) {
    return rtrim(rtrim(number_format($num, 2, '.', ''), '0'), '.');
  }

  // ✅ Normalizar valores si se envía el form
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recibido'])) {
      foreach ($_POST['recibido'] as $id => $valor) {
          $_POST['recibido'][$id] = str_replace(',', '.', $valor);
      }
  }
  ?>

  <?php if (isset($detalle)): ?>
    <form method="POST" action="index.php?seccion=guardar_recepcion">
      <input type="hidden" name="id_pedido" value="<?= htmlspecialchars($pedidoSeleccionado) ?>">

      <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
        <table class="table table-striped table-bordered align-middle text-center">
          <thead class="table-dark sticky-top">
            <tr>
              <th class="text-start">Producto</th>
              <th>Pedido</th>
              <th>Recibido</th>
              <th>Pago</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($detalle as $item): ?>
              <tr>
                <td class="text-start"><?= htmlspecialchars($item['nombre']) ?></td>
                <td><?= mostrarDecimalLimpio($item['cantidad_pedida']) ?></td>
                <td>
                  <input
                    type="number"
                    name="recibido[<?= $item['id_producto'] ?>]"
                    min="0"
                    step="0.01"
                    value="<?= mostrarDecimalLimpio($item['cantidad_pedida']) ?>"
                    class="form-control form-control-sm"
                    style="max-width: 100px; margin: auto;"
                    aria-label="Cantidad recibida de <?= htmlspecialchars($item['nombre']) ?>"
                  >
                </td>
                <td class="text-center align-middle">
                  <?php if ($item['fue_pagado'] == 1): ?>
                    <input type="checkbox" checked disabled>
                    <input type="hidden" name="pagado[<?= $item['id_producto'] ?>]" value="1">
                  <?php else: ?>
                    <input type="checkbox" name="pagado[<?= $item['id_producto'] ?>]" value="1" class="form-check-input mx-auto d-block">
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="mt-3 d-flex justify-content-end">
        <button type="submit" class="btn btn-success fw-semibold px-4">✅ Confirmar Recepción</button>
      </div>
    </form>
  <?php endif; ?>
</div>
