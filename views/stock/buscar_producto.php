<?php
function mostrarDecimalLimpio($num) {
    return rtrim(rtrim(number_format($num, 2, '.', ''), '0'), '.');
}
?>

<div class="container my-4">
    <h2 class="mb-4 text-primary">🔍 Buscar Producto</h2>
    
    <form method="GET" action="" class="row g-3 mb-4">
        <input type="hidden" name="seccion" value="buscar_producto">
        
        <div class="col-md-8">
            <label for="buscar" class="form-label fw-semibold">Nombre del producto:</label>
            <input type="text" name="buscar" id="buscar" class="form-control" placeholder="Ej: Hamburguesa" required>
        </div>
        
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-success w-100 fw-bold">Buscar</button>
        </div>
    </form>

    <?php if (isset($resultados)): ?>
        <h3 class="mb-3">Resultados:</h3>
        <?php if (empty($resultados)): ?>
            <div class="alert alert-warning" role="alert">
                No se encontraron productos con ese nombre.
            </div>
        <?php else: ?>
            <div class="table-responsive shadow-sm rounded">
                <table class="table table-striped table-bordered align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Producto</th>
                            <th>Unidad</th>
                            <th>Stock</th>
                            <th>Gasto por Sábado</th>
                            <th>Últ. Ingreso</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resultados as $p): ?>
                            <tr>
                                <td class="text-start"><?= htmlspecialchars($p['nombre']) ?></td>
                                <td><?= htmlspecialchars($p['unidad_medida']) ?></td>
                                <td><?= mostrarDecimalLimpio($p['cantidad_actual']) ?></td>
                                <td><?= mostrarDecimalLimpio($p['gasto_x_sabado']) ?></td>
                                <td><?= htmlspecialchars($p['fecha_ingreso'] ?? 'N/D') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
