<?php
require_once("comprueba.php");
require_once("conexion.php");
require_once("Productos/Producto.php");
require_once("Productos/RepositorioProducto.php");
require_once("Pedido.php");
require_once("RepositorioPedido.php");
require_once("Factura/Factura.php");
require_once("Factura/RepositorioFactura.php");
require_once("usuarios/RepositorioUsuario.php");

$repoProductos = new RepositorioProducto($pdo);
$repoUsuarios = new RepositorioUsuario($pdo);


$bocadillos = $repoProductos->findAllBocadillos();
$bebidas = $repoProductos->findAllBebidas();
$otros = $repoProductos->findAllOtros();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $totalPago = 0;
    $mensaje = "Pedido de productos:\n";


    $categorias = [$bocadillos, $bebidas, $otros];
    foreach ($categorias as $lista) {
        foreach ($lista as $producto) {
            $nombre = "cb" . $producto->id;
            $cantidad = $_POST['cantidad' . $producto->id];
            if (isset($_POST[$nombre]) && $_POST[$nombre]) {
                $mensaje .= "{$producto->nombre} - Cantidad: $cantidad - Precio: " . ($producto->precio * $cantidad) . "€\n";
                $totalPago += $producto->precio * $cantidad;
            }
        }
    }

    if ($totalPago > 0) {
        
        $repoUsuarios = new RepositorioUsuario($pdo);
        $resultadoPago = $repoUsuarios->pagar($_SESSION['user_id'], $totalPago);

        if ($resultadoPago) {

            $usuario = $repoUsuarios->getUserById($_SESSION['user_id']);
            $_SESSION['saldo'] = $usuario->saldo;

            $repoPedidos = new RepositorioPedido($pdo);
            foreach ($categorias as $lista) {
                foreach ($lista as $producto) {
                    $nombre = "cb" . $producto->id;
                    $cantidad = $_POST['cantidad' . $producto->id];
                    if (isset($_POST[$nombre]) && $_POST[$nombre]) {
  
                        $p = new Pedido();
                        $p->id_user = $_SESSION['user_id'];
                        $p->id_producto = $producto->id;
                        $p->cantidad = $cantidad;
                        $p->precio = $producto->precio * $cantidad;
                        $p->fecha = date("Y-m-d H:i:s");
                        $repoPedidos->save($p);
                    }
                }
            }

            if (isset($_POST['whatsapp_boton'])) {
                $mensaje .= "\nTotal a pagar: " . $totalPago . "€";
                $mensaje = urlencode($mensaje);
                $whatsappNumber = '638577964';
                $whatsappLink = "https://wa.me/$whatsappNumber?text=$mensaje";
                
                echo "<div class='alert alert-success'>Pedido guardado y pago realizado. Haz clic para enviar por WhatsApp:</div>";
                echo "<a href='$whatsappLink' target='_blank' class='btn btn-success'>Enviar pedido por WhatsApp</a>";
            } else {
                echo "<div class='alert alert-success'>Pago exitoso y pedido guardado.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>No tienes suficiente saldo para completar el pago.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>No seleccionaste productos para comprar.</div>";
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos del Central</title>
    <link rel="stylesheet" href="css/styles.css">
    <?php require_once("cabecera.php"); ?>
</head>
<body id="bodyPedidos">
    
    <?php require_once("templates/navbar.php") ?>


    <div class="container mt-5">
        <form action="" method="post">

            <h2 class="titulos-secciones">Bocadillos y Sandwiches</h2>
            <div class="row">
                <?php   
                foreach ($bocadillos as $producto) {
                    echo "<div class='col-lg-4 col-md-6 col-sm-12 mb-4 product-card-container'>";
                    echo "<div class='product-card'>";
                    echo "<img src='img/{$producto->imagen}' alt='Producto' class='product-image'>";
                    echo "<div class='product-details'>";
                    echo "<div class='product-checkbox'>";
                    $nombre = "cb" . $producto->id;
                    echo "<input type='checkbox' name='$nombre' class='product-checkbox-input'>";
                    echo "</div>";
                    echo "<div class='product-title'>{$producto->nombre}</div>";
                    if ($producto->tamanio != 'sintamaño') {
                        echo "<div class='product-size text-muted'>Tamaño: {$producto->tamanio}</div>";
                    }
                    echo "<div class='product-price badge bg-success'>{$producto->precio}€</div>";

                    
                    echo "<div class='product-quantity'>";
                    echo "<label for='cantidad'>Cantidad:</label>";
                    echo "<select name='cantidad{$producto->id}' id='cantidad' class='quantity-select'>";
                    for ($i = 1; $i <= 10; $i++) {
                        echo "<option value='$i'>$i</option>";
                    }
                    echo "</select>";
                    echo "</div>";
                    echo "</div>"; 
                    echo "</div>"; 
                    echo "</div>"; 
                }
                ?>
            </div>

            <h2 class="titulos-secciones">Bebidas</h2>
            <div class="row">
                <?php
                foreach ($bebidas as $producto) {
                    echo "<div class='col-lg-3 col-md-4 col-sm-6 mb-4 product-card-container'>";
                    echo "<div class='product-card'>";
                    echo "<img src='img/{$producto->imagen}' alt='Producto' class='product-image'>";
                    echo "<div class='product-details'>";
                    echo "<div class='product-checkbox'>";
                    $nombre = "cb" . $producto->id;
                    echo "<input type='checkbox' name='$nombre' class='product-checkbox-input'>";
                    echo "</div>";
                    echo "<div class='product-title'>{$producto->nombre}</div>";
                    echo "<div class='product-price badge bg-success'>{$producto->precio}€</div>";

                    echo "<div class='product-quantity'>";
                    echo "<label for='cantidad'>Cantidad:</label>";
                    echo "<select name='cantidad{$producto->id}' id='cantidad' class='quantity-select'>";
                    for ($i = 1; $i <= 10; $i++) {
                        echo "<option value='$i'>$i</option>";
                    }
                    echo "</select>";
                    echo "</div>";
                    echo "</div>"; 
                    echo "</div>"; 
                    echo "</div>"; 
                }
                ?>
            </div>

            <h2 class="titulos-secciones">Otros Platos</h2>
            <div class="row">
                <?php
                foreach ($otros as $producto) {
                    echo "<div class='col-lg-3 col-md-4 col-sm-6 mb-4 product-card-container'>";
                    echo "<div class='product-card'>";
                    echo "<img src='img/{$producto->imagen}' alt='Producto' class='product-image'>";
                    echo "<div class='product-details'>";
                    echo "<div class='product-checkbox'>";
                    $nombre = "cb" . $producto->id;
                    echo "<input type='checkbox' name='$nombre' class='product-checkbox-input'>";
                    echo "</div>";
                    echo "<div class='product-title'>{$producto->nombre}</div>";
                    echo "<div class='product-price badge bg-success'>{$producto->precio}€</div>";

                    echo "<div class='product-quantity'>";
                    echo "<label for='cantidad'>Cantidad:</label>";
                    echo "<select name='cantidad{$producto->id}' class='quantity-select'>";
                    for ($i = 1; $i <= 10; $i++) {
                        echo "<option value='$i'>$i</option>";
                    }
                    echo "</select>";
                    echo "</div>";
                    echo "</div>"; 
                    echo "</div>"; 
                    echo "</div>"; 
                }
                ?>
            </div>


            <div class="row text-center mt-4">
                <div class="col-12">
                    <button id="btnEnviarApp" name="boton" type="submit" class="btn btn-dark">Enviar Pedido Por App</button>
                </div>
                <div class="col-12 mt-2">
                    <button id="btnEnviarWPP" style="margin-bottom: 50px;" name="whatsapp_boton" type="submit" class="btn btn-success">Enviar Pedido Por WPP</button>
                </div>
            </div>
       
        </form>

    </div>


<script>

    document.querySelectorAll('.product-checkbox-input').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const productCard = checkbox.closest('.product-card');
            if (checkbox.checked) {
                productCard.classList.add('selected');
            } else {
                productCard.classList.remove('selected');
            }
        });
    });


    const btnEnviarApp = document.getElementById('btnEnviarApp');
    const btnEnviarWPP = document.getElementById('btnEnviarWPP');


    function handleButtonClick(e) {
        e.preventDefault();
        

        if (this.id === 'btnEnviarWPP') {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'whatsapp_boton';
            hiddenInput.value = 'true';
            this.closest('form').appendChild(hiddenInput);
        }
        

        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
        

        setTimeout(() => {
            this.closest('form').submit();
        }, 1000);
    }


    btnEnviarApp.addEventListener('click', handleButtonClick);
    btnEnviarWPP.addEventListener('click', handleButtonClick);


</script>

</body>
</html>
