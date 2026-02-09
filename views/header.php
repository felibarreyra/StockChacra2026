<header class="d-flex justify-content-between align-items-center p-3 bg-light border-bottom">
  <div class="logo">
    <img src="https://www.lachacrafutbol.com.ar/web/wp-content/uploads/2023/02/previewiso-futbollch.svg" alt="La Chacra Fútbol Logo" width="100" height="100" class="img-fluid">
  </div>

  <div class="user-section d-flex align-items-center gap-3">
    <div>Hola, <strong><?= htmlspecialchars($_SESSION['usuario']) ?></strong></div>
    <a href="logout.php" class="btn btn-outline-danger btn-sm">Cerrar sesión</a>
  </div>
</header>
