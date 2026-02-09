<div class="container my-4">
  <h2 class="mb-4 text-success">➕ Agregar Nuevo Producto</h2>

  <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success" role="alert">
      ✅ Producto agregado correctamente.
    </div>
  <?php elseif (isset($_GET['error'])): ?>
    <div class="alert alert-danger" role="alert">
      ❌ Error al agregar el producto.
    </div>
  <?php endif; ?>

  <form method="POST" action="index.php?seccion=guardar_nuevo_producto" class="row g-3">
    <div class="col-md-6">
      <label for="nombre" class="form-label fw-semibold">Nombre:</label>
      <input type="text" id="nombre" name="nombre" required class="form-control" placeholder="Ej: Hamburguesa">
    </div>

    <div class="col-md-6">
      <label for="unidad" class="form-label fw-semibold">Unidad de Medida:</label>
      <input type="text" id="unidad" name="unidad" required class="form-control" placeholder="Ej: Kg, Litro, Unidad">
    </div>

    <div class="col-md-6">
      <label for="cantidad" class="form-label fw-semibold">Cantidad Inicial:</label>
      <input type="number" min="0" step="1" id="cantidad" name="cantidad" required class="form-control">
    </div>

   <div class="col-md-6">
  <label for="gasto_x_sabado" class="form-label fw-semibold">Gasto por Sábado:</label>
    <input type="number" id="gasto_x_sabado" name="gasto_x_sabado" class="form-control" value="0" readonly>
  </div>


    <div class="col-md-12">
      <label for="id_area" class="form-label fw-semibold">Proveedor:</label>
      <select id="id_area" name="id_area" required class="form-select">
        <option value="" disabled selected>-- Seleccione un proveedor --</option>
        <?php foreach ($areas as $area): ?>
          <option value="<?= $area['id'] ?>"><?= htmlspecialchars($area['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-12 d-flex justify-content-end">
      <button type="submit" class="btn btn-success fw-bold px-4">✅ Guardar Producto</button>
    </div>
  </form>
</div>
