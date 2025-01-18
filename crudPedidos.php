<?php

    require_once("comprueba.php");
    require_once("Pedido.php");
    require_once("RepositorioPedido.php");
    require_once("conexion.php");
    require_once("usuarios/RepositorioUsuario.php");


    $id = 0;
    $id_user ="";
    $id_producto = "";
    $fecha="";
    $cantidad="";
    $precio=0;

   

    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["id"])){

        $pedido = new Pedido();


        $pedido->id = $_POST["id"];
        $pedido->id_user = $_POST["id_user"];
        $pedido->id_producto = $_POST["id_producto"];
        $pedido->fecha = $_POST["fecha"];
        $pedido->cantidad = $_POST["cantidad"];
        $pedido->precio = $_POST["precio"];
        

        $repo = new RepositorioPedido($pdo);
        $repo->save($pedido);

    }else{
        if(isset($_GET['accion']) && ($_GET['accion'] == "modificar")){
 
            $id = $_GET["id"];
            $repo = new RepositorioPedido($pdo);
            $pedido = $repo->findById($id);
    


            $id = $pedido->id;
            $id_user = $pedido->id_user;
            $id_producto = $pedido->id_producto;
            $fecha = $pedido->fecha;
            $cantidad = $pedido->cantidad;
            $precio = $pedido->precio;

        }

        if(isset($_GET['accion']) && ($_GET['accion'] == "eliminar")){
            $id = $_GET["id"];
            $repo = new RepositorioPedido($pdo);
            $repo->deleteById($id);
        }
    }
    
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Pedidos</title>
    <link rel="stylesheet" href="css/styles.css">
    <?php require_once("cabecera.php"); ?>
</head>
<body>
<?php require_once("templates/navbar.php") ?>

    <div class="container mt-4">
        <div class="row mb-3">
            <div class="col">
                <a href="crudPedidos.php?accion=limpiar" class="btn btn-dark">Insertar nuevo pedido</a>
            </div>
        </div>


    <div class="card mb-4">

        <div class="card-header text-center">
            <h5>Formulario de Pedido</h5>
        </div>

        <div class="card-body">

            <form action="" method="post" enctype="multipart/form-data">

                <div class="row g-3">

                    <div class="col-md-4">
                        <label for="id" class="form-label">ID</label>
                        <input type="number" id="id" name="id" class="form-control" value="<?php echo $id; ?>">
                    </div>

                    <div class="col-md-4">
                        <label for="id_user" class="form-label">ID Usuario</label>
                        <input type="number" id="id_user" name="id_user" class="form-control" value="<?php echo $id_user; ?>" required>
                    </div>

                    <div class="col-md-4">
                        <label for="id_producto" class="form-label">ID Producto</label>
                        <input type="number" id="id_producto" name="id_producto" class="form-control" value="<?php echo $id_producto; ?>" required>
                    </div>
                </div>

                <div class="row g-3 mt-3">
                    <div class="col-md-6">
                    <input type="datetime-local" 
                    id="fecha" 
                    name="fecha" 
                    class="form-control" 
                    value="<?php 
                    $fechaObj = new DateTime($fecha);
                    echo $fechaObj->format('Y-m-d\TH:i');
           ?>" 
           required>
                    </div>
                    <div class="col-md-3">
                        <label for="cantidad" class="form-label">Cantidad</label>
                        <input type="number" step="1" id="cantidad" name="cantidad" class="form-control" value="<?php echo $cantidad; ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label for="precio" class="form-label">Precio</label>
                        <input type="number" step="0.01" id="precio" name="precio"  class="form-control" value="<?php echo $precio; ?>" required>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <button type="submit" class="btn btn-success px-4">Guardar Pedido</button>
                </div>
            </form>
        </div>
</div>

        

        <div class="card">
            <div class="card-header">
                Lista de Pedidos
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Id_User</th>
                                <th>Id_Producto</th>
                                <th>Fecha</th>
                                <th>Cantidad</th>
                                <th>Precio</th>
                                <th>Imagen Usuario</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            $repoPedido = new RepositorioPedido($pdo); 
                            $lista = $repoPedido->findAll();

                            $repoUsuario = new RepositorioUsuario($pdo); 

                            foreach($lista as $pedido){
                                $avatar = $repoUsuario->getAvatarById($pedido->id_user);

                                echo "<tr>";
                                echo "<td>{$pedido->id}</td>";
                                echo "<td>{$pedido->id_user}</td>";
                                echo "<td>{$pedido->id_producto}</td>";
                                echo "<td>{$pedido->fecha}</td>";
                                echo "<td>{$pedido->cantidad}</td>";
                                echo "<td>{$pedido->precio}€</td>";

                                if ($avatar) {
                                    echo "<td><div class ='usuario-avatar-pedidos'><img src='img/{$avatar}' alt='Avatar' class='usuario-avatar-img-pedidos'></td></div>";
                                } else {
                                    echo "<td>No disponible</td>";
                                }

                                echo "<td><a href='?id={$pedido->id}&accion=modificar' class='btn btn-sm btn-warning'>Modificar</a></td>";
                                echo "<td><a href='?id={$pedido->id}&accion=eliminar' class='btn btn-sm btn-danger' onclick='return confirm(\"¿Estás seguro de que deseas eliminar este producto?\")'>Eliminar</a></td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>