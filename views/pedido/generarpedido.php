<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Generar Pedido Manual</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<?php
function mostrarDecimalLimpio($num) {
    return rtrim(rtrim(number_format($num, 2, '.', ''), '0'), '.');
}

// ✅ Normalizar POST para aceptar decimales con coma
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['productos'])) {
    foreach ($_POST['productos'] as $id => $valor) {
        $_POST['productos'][$id] = str_replace(',', '.', $valor);
    }
}
?>

<div class="container my-5 pedido-form-container">
    <form method="GET" action="" class="pedido-filtro mt-4 d-flex align-items-center gap-2">
        <input type="hidden" name="seccion" value="formulario_pedido_manual">
        <label for="id_area" class="form-label mb-0">Filtrar por proveedor:</label>
        <select
            name="id_area"
            id="id_area"
            class="form-select w-auto"
            onchange="this.form.submit()"
        >
            <option value="">-- Todas --</option>
            <?php foreach ($areas as $a): ?>
                <option value="<?= $a['id'] ?>" <?= (isset($_GET['id_area']) && $_GET['id_area'] == $a['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($a['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>
    <h1>📝 Pedido <?= isset($areaSeleccionada) && $areaSeleccionada ? "– Proveedor: $areaSeleccionada" : "" ?></h1>

    <?php if (isset($_GET['success']) && $_GET['remito']): ?>
        <div class="alert alert-success" role="alert">
            ✅ Pedido generado correctamente – Remito: <strong><?= htmlspecialchars($_GET['remito']) ?></strong>
        </div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="alert alert-danger" role="alert">
            ❌ <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>

   <form action="index.php?seccion=guardar_pedido_manual" method="POST" class="pedido-form">
    <!-- Campo oculto para enviar el área seleccionada -->
    <input type="hidden" name="area" value="<?= isset($areaSeleccionada) ? htmlspecialchars($areaSeleccionada) : '' ?>">

    <div class="table-responsive pedido-scroll" style="max-height: 500px; overflow-y:auto;">
        <table class="table table-striped table-hover table-bordered align-middle">
            <thead class="table-dark sticky-top">
                <tr>
                    <th>Producto</th>
                    <th>Unidad</th>
                    <th>Stock</th>
                    <th>Gasto por Sábado</th>
                    <th>Cantidad a Pedir</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['nombre']) ?></td>
                        <td><?= htmlspecialchars($p['unidad_medida']) ?></td>
                        <td>
                            <?php if ($p['cantidad_actual'] < $p['gasto_x_sabado']): ?>
                                <span class="text-danger" data-bs-toggle="tooltip" data-bs-placement="top"
                                      title="⚠ Faltan <?= mostrarDecimalLimpio(round($p['gasto_x_sabado'] - $p['cantidad_actual'], 2)) ?> unidades para cubrir el sábado">
                                    <?= mostrarDecimalLimpio($p['cantidad_actual']) ?> 🔴
                                </span>
                            <?php else: ?>
                                <span class="text-success" data-bs-toggle="tooltip" data-bs-placement="top"
                                      title="✔ Stock suficiente para el próximo sábado">
                                    <?= mostrarDecimalLimpio($p['cantidad_actual']) ?> ✅
                                </span>
                            <?php endif; ?>
                        </td>
                        <td><?= mostrarDecimalLimpio($p['gasto_x_sabado']) ?></td>
                        <td>
                            <input type="number" step="0.01" name="productos[<?= $p['id'] ?>]" min="0" value="0"
                                   class="form-control form-control-sm" style="max-width: 100px;"
                                   aria-label="Cantidad a pedir de <?= htmlspecialchars($p['nombre']) ?>">
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="form-footer mt-3">
        <button type="submit" class="btn btn-success">✅ Generar Pedido</button>
    </div>
</form>

</div>

<!-- Bootstrap JS Bundle (Popper incluido) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Inicializar tooltips -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  tooltipTriggerList.forEach(function (tooltipTriggerEl) {
    new bootstrap.Tooltip(tooltipTriggerEl)
  })
});
</script>

</body>
</html>
