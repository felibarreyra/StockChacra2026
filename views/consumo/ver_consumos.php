<?php
function mostrarDecimalLimpio($num) {
    return rtrim(rtrim(number_format($num, 2, '.', ''), '0'), '.');
}

$fechaSeleccionada = $_GET['fecha_sabado'] ?? date('Y-m-d', strtotime('last saturday'));
$idAreaSeleccionada = $_GET['id_area'] ?? '';
?>

<div class="container my-4 consumos-container">
  <h1 class="mb-4">📊 Consumos Registrados</h1>

  <!-- Filtro por sábado y área -->
  <form method="GET" class="row g-3 align-items-center mb-4">
    <input type="hidden" name="seccion" value="ver_consumos">

    <div class="col-auto">
      <label for="fecha_sabado" class="form-label fw-semibold mb-0">Filtrar por sábado:</label>
    </div>
    <div class="col-auto">
      <input type="date" name="fecha_sabado" id="fecha_sabado" value="<?= htmlspecialchars($fechaSeleccionada) ?>" class="form-control">
    </div>

    <div class="col-auto">
      <label for="id_area" class="form-label fw-semibold mb-0">Filtrar por proveedor:</label>
    </div>
    <div class="col-auto">
      <select name="id_area" id="id_area" class="form-select">
        <option value="">-- Todas --</option>
        <?php foreach ($areas as $a): ?>
          <option value="<?= $a['id'] ?>" <?= ($idAreaSeleccionada == $a['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($a['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-auto">
      <button type="submit" class="btn btn-primary fw-semibold">Filtrar</button>
    </div>
  </form>

  <?php if (empty($consumos)): ?>
    <p>No hay consumos registrados<?= $fechaSeleccionada ? " para esa fecha." : " aún." ?></p>
  <?php else: ?>
    <div class="table-responsive" style="max-height: 400px; overflow-y:auto;">
      <table class="table table-striped table-bordered align-middle">
        <thead class="table-dark sticky-top">
          <tr>
            <th>Producto</th>
            <th>Fecha</th>
            <th>Cantidad</th>
            <th>Eliminar</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($consumos as $consumo): ?>
            <tr>
              <td><?= htmlspecialchars($consumo['nombre_producto'] ?? $consumo['nombre']) ?></td>
              <td><?= htmlspecialchars($consumo['fecha']) ?></td>
              <td><?= mostrarDecimalLimpio($consumo['cantidad']) ?></td>
              <td>
                <form method="POST" action="index.php?seccion=eliminar_consumo" 
                      onsubmit="return confirm('¿Estás seguro de eliminar este consumo? Esta acción no se puede deshacer.');" 
                      style="display:inline;">
                  <input type="hidden" name="id_consumo" value="<?= htmlspecialchars($consumo['id']) ?>">
                  <button type="submit" class="btn btn-danger btn-sm" title="Eliminar Consumo">🗑️</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="text-end mt-3">
      <a href="generar_consumo_pdf.php?fecha_sabado=<?= urlencode($fechaSeleccionada) ?>&id_area=<?= urlencode($idAreaSeleccionada) ?>" 
         class="btn btn-outline-secondary fw-semibold" target="_blank" rel="noopener">
        📄 Descargar PDF
      </a>
    </div>
  <?php endif; ?>
</div>
