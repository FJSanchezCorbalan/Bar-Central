<?php
if (isset($_SESSION['username'])){
    $nombreUsuario = $_SESSION['username'];
    $saldoUsuario = $_SESSION['saldo'];
} else {
    header('Location: /fernando/central2/index.php');
    exit();
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-black">
  <div class="container-fluid">

    <a class="navbar-brand" href="#">
      <img src="/fernando/central2/templates/logo.webp" alt="Bar-Central Logo" class="navbar-logo">
    </a>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div style="margin-right:150px;" class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link text-silver" href="/fernando/central2/pedidos.php">Hacer Pedido</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-silver" href="/fernando/central2/listarpedidosHoy.php">Pedidos de Hoy</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-silver" href="/fernando/central2/usuarios/perfil.php">Perfil de Usuario</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-silver" href="/fernando/central2/usuarios/cartera.php">Mi Cartera</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-silver" href="/fernando/central2/logout.php">Cerrar Sesión</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-silver" href="#" data-bs-toggle="collapse" data-bs-target="#crudMenu" aria-expanded="false" aria-controls="crudMenu">
            CRUD
          </a>
          <div class="collapse" id="crudMenu">
            <ul class="list-unstyled">
              <li><a class="dropdown-item" href="/fernando/central2/Productos">CRUD Productos</a></li>
              <li><a class="dropdown-item" href="/fernando/central2/crudPedidos.php">CRUD Pedidos</a></li>
            </ul>
          </div>
        </li>
      </ul>
    </div>

    <div class="d-flex align-items-center ms-3">
      <span style=" margin-right:15px; font-size: 1rem !important; color: #C5B69C !important; font-weight: 500 !important; margin-left: 10px !important;">Hola, <?php echo htmlspecialchars($nombreUsuario); ?></span>
      <span style=" margin-right:15px; font-size: 1rem !important; color: #C5B69C !important; font-weight: 500 !important; margin-left: 10px !important;">Saldo: <?php echo number_format($saldoUsuario, 2); ?>€</span>
    </div>
  </div>
</nav>
