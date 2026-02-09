<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Stock Actual</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php
function mostrarDecimalLimpio($num) {
    return rtrim(rtrim(number_format($num, 2, '.', ''), '0'), '.');
}

// ✅ Normalizar valores POST para aceptar "," como separador decimal
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['nuevo_stock'])) {
        $_POST['nuevo_stock'] = str_replace(',', '.', $_POST['nuevo_stock']);
    }
    if (isset($_POST['nuevo_gasto'])) {
        $_POST['nuevo_gasto'] = str_replace(',', '.', $_POST['nuevo_gasto']);
    }
}
?>
<div class="container-fluid my-4">
    <div class="stock-actual-container">
        <h1>📦 Stock Actual <?= isset($areaSeleccionada) ? "– Proveedor: $areaSeleccionada" : "" ?></h1>

        <!-- ✅ Mensaje de confirmación -->
        <?php if (isset($_GET['recalculo']) && $_GET['recalculo'] == 1): ?>
            <div class="alert alert-success">
                ✅ Gastos sugeridos recalculados correctamente.
            </div>
        <?php endif; ?>

        <!-- Botón recalcular gastos -->
        <div class="d-flex justify-content-end mb-3">
            <form method="POST" action="index.php?seccion=recalcular_gastos">
                <button type="submit" class="btn btn-secondary">
                    🔄 Recalcular Gastos Sugeridos (Promedio)
                </button>
            </form>
        </div>

        <!-- Filtro por proveedor -->
        <form method="GET" action="" class="stock-filter-form mb-3 d-flex align-items-center gap-2">
            <input type="hidden" name="seccion" value="stock">
            <label for="id_area" class="form-label mb-0">Filtrar por proveedor:</label>
            <select name="id_area" id="id_area" class="form-select w-auto" onchange="this.form.submit()">
                <option value="">-- Todas --</option>
                <?php foreach ($areas as $a): ?>
                    <option value="<?= $a['id'] ?>" <?= (isset($_GET['id_area']) && $_GET['id_area'] == $a['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($a['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <!-- Tabla con scroll responsive -->
        <div class="table-responsive" style="max-height: 500px; overflow-y:auto;">
            <table class="table table-striped table-hover table-bordered align-middle">
                <thead class="table-dark sticky-top">
                    <tr>
                        <th scope="col">Producto</th>
                        <th scope="col">Unidad</th>
                        <th scope="col">Cantidad</th>
                        <th scope="col">Gasto por Sábado</th>
                        <th scope="col">Últ. Fecha Ingreso</th>
                        <th scope="col">Proveedor</th>
                        <th scope="col">Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $p): 
                        $cantidad = $p['cantidad_actual'];
                        $gasto = $p['gasto_x_sabado'];
                        $alertaStock = ($cantidad < $gasto);
                        $diferencia = round($gasto - $cantidad, 2);
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($p['nombre']) ?></td>
                        <td><?= htmlspecialchars($p['unidad_medida']) ?></td>

                        <!-- Stock -->
                        <td>
                            <form action="index.php?seccion=actualizar_stock" method="POST" class="d-flex align-items-center gap-2 mb-0">
                                <input type="hidden" name="id_producto" value="<?= $p['id'] ?>">
                                <input type="number" min="0" step="0.01" name="nuevo_stock" 
                                    value="<?= mostrarDecimalLimpio($p['cantidad_actual']) ?>"
                                    class="form-control form-control-sm" style="max-width: 120px;">
                                <button type="submit" class="btn btn-primary btn-sm" title="Actualizar stock">💾</button>
                            </form>
                        </td>

                        <!-- Gasto -->
                        <td>
                            <form action="index.php?seccion=actualizar_gasto" method="POST" class="d-flex align-items-center gap-2 mb-0">
                                <input type="hidden" name="id_producto" value="<?= $p['id'] ?>">
                                <input 
                                    type="number" 
                                    min="0" 
                                    step="0.01"
                                    name="nuevo_gasto" 
                                    value="<?= mostrarDecimalLimpio($p['gasto_x_sabado']) ?>"
                                    class="form-control form-control-sm"
                                    style="max-width: 100px;"
                                    title="Actualizar Gasto"
                                    data-bs-toggle="tooltip" 
                                    data-bs-placement="top"
                                >
                                <button type="submit" class="btn btn-warning btn-sm" aria-label="Actualizar gasto">💾</button>
                            </form>
                        </td>

                        <td><?= isset($p['fecha_ingreso']) ? htmlspecialchars($p['fecha_ingreso']) : 'N/D' ?></td>

                        <!-- Área -->
                        <td>
                            <form action="index.php?seccion=cambiar_area" method="POST" class="d-flex align-items-center gap-2 mb-0">
                                <input type="hidden" name="id_producto" value="<?= $p['id'] ?>">
                                <select name="id_area" class="form-select form-select-sm" style="max-width: 150px;">
                                    <?php foreach ($areas as $a): ?>
                                        <option value="<?= $a['id'] ?>" <?= $a['id'] == $p['id_area'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($a['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-success btn-sm" title="Cambiar área">💾</button>
                            </form>
                        </td>

                        <!-- Eliminar -->
                        <td>
                            <form method="POST" action="index.php?seccion=eliminar_producto"
                                onsubmit="return confirm('¿Estás seguro de eliminar este producto? Esta acción no se puede deshacer.');"
                                class="d-inline"
                            >
                                <input type="hidden" name="id_producto" value="<?= $p['id'] ?>">
                                <button type="submit" 
                                    class="btn btn-danger btn-sm" 
                                    data-bs-toggle="tooltip" 
                                    data-bs-placement="top" 
                                    title="Eliminar Producto"
                                    aria-label="Eliminar producto"
                                >
                                    🗑️
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
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
