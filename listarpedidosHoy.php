<?php  
ini_set('display_errors', 1);  
ini_set('display_startup_errors', 1);  
error_reporting(E_ALL);  
require_once("comprueba.php");
require_once("conexion.php");
require_once("Pedido.php");
require_once("RepositorioPedido.php");
require_once("Productos/RepositorioProducto.php");

$repo = new RepositorioPedido($pdo);

$lista = $repo->findPendientesByFecha(date("Y-m-d"));
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos de Hoy</title>
    <link rel="stylesheet" href="css/styles.css">
    <?php require_once("cabecera.php") ?>
    <style>
        .row{
            margin-top:4em;
        }
    </style>
</head>
<body>
<?php require_once("templates/navbar.php") ?>
    
<div class="container mt-4">
    
    
    <h2 class="mb-4 titulo-pedidosHoy">Pedidos de Hoy</h2>
    
    <div class="row">
        <?php foreach($lista as $pedido){

            $idUsuario = $pedido->id_user;
            $sql = "SELECT avatar, username FROM users where id = :id";
            $consulta = $pdo->prepare($sql);
            $consulta->setFetchMode(PDO::FETCH_OBJ);
            $consulta->bindParam(':id', $idUsuario);
            $consulta->execute();
            $usuario = $consulta->fetch();

            $repoProductos = new RepositorioProducto($pdo);
            $producto = $repoProductos->findById($pedido->id_producto);
            $imagenProducto = $repoProductos->getImagenById($pedido->id_producto);
            ?>
            
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <img src="img/<?php echo $usuario->avatar ?>" 

                                 class="rounded-circle me-3" 
                                 style="height: 50px; width: 50px; object-fit: cover;">
                            <div>
                                <h5 class="card-title mb-0"><?php echo $usuario->username ?></h5>
                                <small class="text-muted">
                                <?php echo date('d/m/Y H:i', strtotime($pedido->fecha)) ?>
                                </small>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center mb-3">
                            <img src="img/<?php echo $imagenProducto ?>" 
                                 class="rounded me-3" 
                                 style="height: 70px; width: 70px; object-fit: cover;">
                            <div>
                                <h6 class="mb-1"><?php echo $producto->nombre ?></h6>
                                <div class="d-flex align-items-center">
                                    <span class="me-3">
                                        <i class="fas fa-shopping-cart me-1"></i>
                                        Cantidad: <?php echo $pedido->cantidad ?>
                                    </span>
                                    <span class="badge bg-success">
                                        <?php echo number_format($pedido->precio, 2) ?>€
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                        <a href="actualizarEstado.php?estado=completado&id=<?php echo $pedido->id ?>" class="btn btn-sm btn-outline-primary me-2">
                            <i class="fas fa-check"></i> Completado
                       </a>
                        <a href="actualizarEstado.php?estado=cancelado&id=<?php echo $pedido->id ?>" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        </div>

                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

    
</body>
</html>