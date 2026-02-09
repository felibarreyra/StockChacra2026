<div class="container my-4">
  <h2 class="mb-4 text-success">➕ Agregar Nuevo Proveedor</h2>

  <form method="POST" action="index.php?seccion=guardar_nueva_area" class="row g-3">
    <div class="col-md-8">
      <label for="nombre_area" class="form-label fw-semibold">Nombre del Proveedor:</label>
      <input type="text" id="nombre_area" name="nombre" required class="form-control" placeholder="Ej: Producción">
    </div>

    <div class="col-md-4 d-flex align-items-end">
      <button type="submit" class="btn btn-success w-100 fw-bold">✅ Guardar Proveedor</button>
    </div>
  </form>

  <hr class="my-4">

  <h4 class="mb-3 text-danger">📋 Proveedores Existentes</h4>
  <table class="table table-bordered table-hover">
    <thead class="table-light">
      <tr>
        <th>Nombre</th>
        <th style="width: 150px;">Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($areas as $area): ?>
        <tr>
          <td><?= htmlspecialchars($area['nombre']) ?></td>
          <td class="text-center">
            <form method="POST" action="index.php?seccion=eliminar_area" onsubmit="return confirm('¿Eliminar esta área?')" class="d-inline">
              <input type="hidden" name="id_area" value="<?= $area['id'] ?>">
              <button type="submit" class="btn btn-danger btn-sm">🗑 Eliminar</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
