<?php
    require_once("../comprueba.php");
    require_once("Producto.php");
    require_once("RepositorioProducto.php");
    require_once("../conexion.php");

  
    $id = 0;
    $nombre ="";
    $tipo = "";
    $tamanio="";
    $precio=0;
    $descripcion="";
   

    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["id"])){

        $producto = new Producto();


        $producto->id = $_POST["id"];
        $producto->nombre = $_POST["nombre"];
        $producto->descripcion = $_POST["descripcion"];
        $producto->tamanio = $_POST["tamanio"];
        $producto->tipo = $_POST["tipo"];
        $producto->precio = $_POST["precio"];
        

        if(isset($_FILES['imagen']) && $_FILES['imagen']['name'] != ""){

            $imagen = date("y-m-d - h-i-s") . "-" . $_FILES['imagen']['name'];
            $file_loc = $_FILES['imagen']['tmp_name'];
            move_uploaded_file($file_loc,"../img/" . $imagen);

            $producto-> imagen = $imagen;  
        }


    
        $repo = new RepositorioProducto($pdo);
        $repo->save($producto);

    }else{
        if(isset($_GET['accion']) && ($_GET['accion'] == "modificar")){

            $id = $_GET["id"];
            $repo = new RepositorioProducto($pdo);
            $producto = $repo->findById($id);

            $nombre = $producto->nombre;
            $tipo = $producto->tipo;
            $tamanio=$producto->tamanio;
            $precio=$producto->precio;
            $descripcion=$producto->descripcion;    
            $imagen = $producto -> imagen;
        }

        if(isset($_GET['accion']) && ($_GET['accion'] == "eliminar")){
            $id = $_GET["id"];
            $repo = new RepositorioProducto($pdo);
            $repo->deleteById($id);
        }
    }
    
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Productos</title>
    <link rel="stylesheet" href="../css/styles.css">
    <?php require_once("../cabecera.php"); ?>
</head>
<body>
<?php require_once("../templates/navbar.php") ?>

    <div class="container mt-4">
        <div class="row mb-3">
            <div class="col">
                <a href="index.php?accion=limpiar" class="btn btn-dark">Insertar nuevo producto</a>
            </div>
        </div>


    

    <div class="card mb-4">

        <div class="card-header text-center">
            <h5>Formulario de Producto</h5>
        </div>

        <div class="card-body">
            <form action="index.php" method="post" enctype="multipart/form-data">
                <div class="row">

                    
                    <div class="col-md-2 mb-3">
                        <label for="id" class="form-label">ID</label>
                        <input type="text" id="id" name="id" class="form-control text-center" 
                            value="<?php echo $id ?>" readonly>
                    </div>

                    
                    <div class="col-md-5 mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" 
                            value="<?php echo $nombre ?>" maxlength="50" placeholder="Nombre del producto">
                    </div>

                    
                    <div class="col-md-3 mb-3">
                        <label for="precio" class="form-label">Precio (€)</label>
                        <input type="number" id="precio" name="precio" class="form-control text-end" 
                            value="<?php echo $precio ?>" step="0.01" min="0" style="max-width: 120px;">
                    </div>
                </div>

                <div class="row">
                    
                    <div class="col-md-12 mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea id="descripcion" name="descripcion" class="form-control" rows="3" 
                         maxlength="200" placeholder="Describe brevemente el producto..."><?php echo $descripcion ?></textarea>
                    </div>
                </div>

                <div class="row">
                   
                    <div class="col-md-4 mb-3">
                        <label for="tamanio" class="form-label">Tamaño</label>
                        <select id="tamanio" name="tamanio" class="form-select">
                            <option value="pequeño" <?php echo ($tamanio == "pequeño") ? "selected" : ""; ?>>Pequeño</option>
                            <option value="mediano" <?php echo ($tamanio == "mediano") ? "selected" : ""; ?>>Mediano</option>
                            <option value="grande" <?php echo ($tamanio == "grande") ? "selected" : ""; ?>>Grande</option>
                            <option value="sintamaño" <?php echo ($tamanio == "sintamaño") ? "selected" : ""; ?>>No aplica</option>
                        </select>
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label for="tipo" class="form-label">Tipo</label>
                        <select id="tipo" name="tipo" class="form-select">
                            <option value="comida" <?php echo ($tipo == "comida") ? "selected" : ""; ?>>Comida</option>
                            <option value="bebida" <?php echo ($tipo == "bebida") ? "selected" : ""; ?>>Bebida</option>
                            <option value="otro" <?php echo ($tipo == "otro") ? "selected" : ""; ?>>Otro</option>
                        </select>
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label for="imagen" class="form-label">Imagen</label>
                        <input type="file" id="imagen" name="imagen" class="form-control">
                    </div>
                </div>

               
                <?php if (isset($imagen) && $imagen != '') { ?>
                    <div class="row mb-3">
                        <div class="col-md-12 text-center">
                            <img src="../img/<?php echo $imagen ?>" class="img-thumbnail" style="max-height: 100px;">
                        </div>
                    </div>
                <?php } ?>

              
                <div class="row">
                    <div class="col text-center">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Guardar Producto
                        </button>
                    </div>
                </div>
            </form>
        </div>
</div>


        <div class="card">
            <div class="card-header">
                Lista de Productos
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Nombre</th>
                                <th>Tamanio</th>
                                <th>Precio</th>
                                <th>Tipo</th>
                                <th>Imagen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $repo = new RepositorioProducto($pdo);
                            $lista = $repo->findAll();

                            foreach($lista as $producto){
                                echo "<tr>";
                                echo "<td>{$producto->id}</td>";
                                echo "<td>{$producto->nombre}</td>";
                                echo "<td>{$producto->tamanio}</td>";
                                echo "<td>{$producto->precio}€</td>";
                                echo "<td>{$producto->tipo}</td>";
                                echo "<td><img src='../img/{$producto->imagen}' class='img-thumbnail' width='80px' height = '80px'></td>";
                                echo "<td><a href='?id={$producto->id}&accion=modificar' class='btn btn-sm btn-warning'>Modificar</a></td>";
                                echo "<td><a href='?id={$producto->id}&accion=eliminar' class='btn btn-sm btn-danger' onclick='return confirm(\"¿Estás seguro de que deseas eliminar este producto?\")'>Eliminar</a></td>";
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