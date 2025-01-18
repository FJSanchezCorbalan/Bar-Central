<?php 
require_once("../comprueba.php");
require_once("../conexion.php");
require_once("../Productos/RepositorioProducto.php");
require_once("../Pedido.php");
require_once("../RepositorioPedido.php");
require_once("RepositorioUsuario.php");
require_once("../Factura/Factura.php");
require_once("../Factura/RepositorioFactura.php");

$repoPedidos = new RepositorioPedido($pdo);
$repoProductos = new RepositorioProducto($pdo);
$repoUsuario = new RepositorioUsuario($pdo);
$repoFacturas = new RepositorioFactura($pdo);

$usuario = $repoUsuario->getUserById($_SESSION['user_id']);
$ultimosPagos = $repoFacturas->findLastInvoicesByUserId($_SESSION['user_id']);
$ultimosPedidos = $repoPedidos->findLastByUser($_SESSION['user_id']);
$idfavorito = $repoPedidos->getFavoriteProduct($_SESSION['user_id']);

 if(isset($_SESSION['mensaje'])): ?>
    <?php echo $_SESSION['mensaje']; ?>
    <?php unset($_SESSION['mensaje']);?>
<?php endif;

if ($idfavorito) {
    $productoFavorito = $repoProductos->findById($idfavorito);
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['imagen']) && $_FILES['imagen']['name'] != "") {
        $imagen = date("y-m-d - h-i-s") . "-" . $_FILES['imagen']['name'];
        $file_loc = $_FILES['imagen']['tmp_name'];
        move_uploaded_file($file_loc, "../img/" . $imagen);

        $id = $_SESSION['user_id'];
        $sql = "UPDATE users SET avatar = :imagen WHERE id = :id";
        $consulta = $pdo->prepare($sql);
        $consulta->bindParam(':imagen', $imagen);
        $consulta->bindParam(':id', $id);
        $consulta->execute();
        
        $usuario->avatar = $imagen;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil Usuario</title>
    <link rel="stylesheet" href="../css/styles.css">
    <?php require_once("../cabecera.php") ?>
</head>
<body>
    <?php require_once("../templates/navbar.php") ?>

    <!--Perfil de Usuario-->
    <div class="profile-section">
        <div class="usuario-card-container">
            <div class="usuario-card">
                <h2 class="usuario-titulo">Perfil de Usuario</h2>
                <div class="usuario-avatar">
                    <img src="../img/<?php echo $usuario->avatar; ?>" alt="" class="usuario-avatar-img">
                </div>
                <div class="usuario-info">
                    <p><strong>Username:</strong> <?php echo $usuario->username; ?></p>
                    <p><strong>Email:</strong> <?php echo $usuario->email; ?></p>
                    <p><strong>Saldo:</strong> <?php echo $usuario->saldo; ?>€</p>
                    
                    <h3>Cambiar Avatar</h3>
                    <form action="" enctype="multipart/form-data" method="post" class="avatar-form">
                        <div class="avatar-change-container">
                            <label for="imagen" class="btn btn-secondary">Seleccionar Imagen</label>
                            <input type="file" name="imagen" id="imagen" style="display: none;">
                            <button type="submit" class="btn btn-dark">Cambiar Imagen</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!--Ultimos Pagos-->
    <div class="profile-section">
        <h2 class="section-title">Últimos Pagos</h2>
        <div class="list-container">
            <?php if (count($ultimosPagos) > 0): ?>
                <ul class="list-group">
                    <?php foreach ($ultimosPagos as $pago): ?>
                        <li class="list-group-item">
                            <strong>Fecha:</strong> <?php echo $pago->fecha; ?><br>
                            <strong>Monto:</strong> <?php echo number_format($pago->monto, 2); ?>€<br>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="no-content-message">No has realizado pagos aún.</p>
            <?php endif; ?>
        </div>
    </div>

    <!--Ultimos 5 pedidos-->
    <div class="profile-section">
        <h2 class="section-title">Últimos 5 pedidos</h2>
        <div class="list-container">
            <?php if (count($ultimosPedidos) > 0): ?>
                <ul class="list-group">
                    <?php 
                    foreach ($ultimosPedidos as $pedido) {
                        $producto = $repoProductos->findById($pedido->id_producto);
                        echo "<li class='list-group-item'>";
                        echo "<strong>Producto:</strong> " . $producto->nombre . "<br>";
                        echo "<strong>Fecha:</strong> " . $pedido->fecha . "<br>";
                        echo "<strong>Cantidad:</strong> " . $pedido->cantidad . "<br>";
                        echo "<strong>Precio:</strong> " . $pedido->precio . "€";
                        echo "<form action='../repetirPedido.php' method='POST' class='mt-2'>";
                        echo "<input type='hidden' name='id_pedido' value='" . $pedido->id . "'>";
                        echo "<button type='submit' class='btn btn-dark btn-sm repetirPedido'>Repetir Pedido</button>";
                        echo "</form>";
                        echo "</li>";
                    }
                    ?>
                </ul>
            <?php else: ?>
                <p class="no-content-message">No has realizado pedidos aún.</p>
            <?php endif; ?>
        </div>
    </div>

    <!--Producto Favorito-->
    <div class="profile-section">
        <h2 class="section-title">Producto Favorito</h2>
        <div class="favorite-product-container">
            <?php if (isset($productoFavorito) && $productoFavorito != null): ?>
                <div class="card favorite-product-card">
                    <img src="../img/<?php echo $productoFavorito->imagen; ?>" class="card-img-top" alt="<?php echo $productoFavorito->nombre; ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $productoFavorito->nombre; ?></h5>
                        <p class="card-text">
                            <strong>Descripción:</strong> <?php echo $productoFavorito->descripcion; ?><br>
                            <strong>Precio:</strong> <?php echo $productoFavorito->precio; ?>€
                        </p>
                    </div>
                </div>
            <?php else: ?>
                <p class="no-content-message">No tienes un producto favorito aún.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>

        document.querySelectorAll('.repetirPedido').forEach(boton => {
            boton.addEventListener('click', function(e) {
                e.preventDefault();
                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
                setTimeout(() => {
                    this.closest('form').submit();
                }, 1000);
            });
        });

    </script>



                    
</body>
</html>