<?php 
function mostrarDecimalLimpio($num) { 
    return rtrim(rtrim(number_format($num, 2, '.', ''), '0'), '.');
}

$fecha_sabado = date('Y-m-d', strtotime('last saturday'));
$texto_fecha = "Sábado (" . date('d/m/Y', strtotime('last saturday')) . ")";
?>

<div class="container my-4 registrar-consumo-container">
  <h2 class="mb-4 text-warning">🍽 Registrar Consumos</h2>

  <?php if (isset($_GET['exito'])): ?>
    <div class="alert alert-success">✅ Consumidos registrados correctamente.</div>
  <?php endif; ?>

  <?php if (isset($_GET['error']) && $_GET['error'] === 'stock'): ?>
    <div class="alert alert-danger">❌ No se puede registrar una cantidad mayor al stock disponible.</div>
  <?php endif; ?>
  
  <?php if (isset($_GET['error']) && $_GET['error'] === 'stock' && isset($_GET['productos'])): ?>
    <div class="alert alert-danger">
        ❌ No se pudo registrar consumo de: <?= htmlspecialchars(urldecode($_GET['productos'])) ?> (excede el stock disponible)
    </div>
<?php endif; ?>


  <!-- Filtro de área -->
  <form method="GET" action="" class="mb-4 row g-3 align-items-center">
    <input type="hidden" name="seccion" value="formulario_consumo_masivo">
    <div class="col-auto">
      <label for="id_area" class="form-label">Filtrar por proveedor:</label>
    </div>
    <div class="col-auto">
      <select name="id_area" id="id_area" onchange="this.form.submit()" class="form-select">
        <option value="">-- Todas --</option>
        <?php foreach ($areas as $a): ?>
          <option value="<?= $a['id'] ?>" <?= ($_GET['id_area'] ?? '') == $a['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($a['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
  </form>

  <!-- Mostrar fecha visible -->
  <div class="mb-3">
    <label class="form-label fw-bold">Fecha de consumo:</label>
    <div><?= $texto_fecha ?></div>
  </div>

  <!-- Tabla de productos con inputs -->
  <form method="POST" action="index.php?seccion=guardar_consumo_masivo">
    <input type="hidden" name="fecha_consumo" value="<?= $fecha_sabado ?>">
    <table class="table table-bordered table-hover table-sm">
      <thead class="table-success text-center">
  <tr>
    <th>Producto</th>
    <th>Stock Anterior</th>
    <th>Nuevo Stock</th>
  </tr>
</thead>
<tbody>
  <?php foreach ($productos as $producto): ?>
    <tr>
      <td><?= htmlspecialchars($producto['nombre']) ?></td>
      <td><?= mostrarDecimalLimpio($producto['cantidad_actual']) ?></td>
      <td>
        <input type="hidden" name="id_producto[]" value="<?= $producto['id'] ?>">
        <input type="number"
               step="0.01"
               min="0"
               name="nuevo_stock[]"
               class="form-control form-control-sm"
               value="<?= mostrarDecimalLimpio($producto['cantidad_actual']) ?>">
      </td>
    </tr>
  <?php endforeach; ?>
</tbody>

    </table>
    <div class="text-end">
      <button type="submit" class="btn btn-warning fw-bold">➖ Registrar Consumos</button>
    </div>
  </form>
</div>
