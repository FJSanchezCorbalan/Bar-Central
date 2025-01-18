<?php
require_once("../comprueba.php");  
require_once("../conexion.php");
require_once("RepositorioUsuario.php"); 


$repoUsuario = new RepositorioUsuario($pdo);


$usuario = $repoUsuario->getUserById($_SESSION['user_id']);


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['montoRecarga'])) {
    $montoRecarga = $_POST['montoRecarga'];


    if ($montoRecarga > 0) {
        $repoUsuario->recargarSaldo($_SESSION['user_id'], $montoRecarga);
        $_SESSION['saldo'] += $montoRecarga;
        $usuario = $repoUsuario->getUserById($_SESSION['user_id']);
    } else {
        echo "<script>alert('Por favor, ingrese una cantidad válida para recargar.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Cartera</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <?php require_once("../cabecera.php"); ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        span{
            font-weight: bold;
            color: green;
        }
    </style>
</head>
<body>
    <?php require_once("../templates/navbar.php"); ?>

    <!--Contenedor principal de la cartera-->
    <div class="container mt-4 d-flex justify-content-center">
        <div class="card p-3 shadow-lg rounded-3" style="background-color: #FFF8E1; width: 350px;">

            <h2 class="text-center mb-4">
                <i class="fas fa-wallet"></i> Mi Cartera
            </h2>

            <p class="mb-4 text-center">Recarga tu saldo y gestiona tu dinero fácilmente.</p>

            <!--Saldo disponible-->
            <div class="d-flex justify-content-between mb-3">
                <p><strong>Saldo disponible:  </strong><span><?php echo number_format($usuario->saldo, 2);?>€</span></p>
            </div>

            <!--Formulario para recargar saldo-->
            <form action="cartera.php" method="POST" id="formRecarga">
                <div class="mb-3">
                    <label for="montoRecarga" class="form-label">Cantidad a recargar (€):</label>
                    <input type="number" class="form-control col-6 mx-auto" id="montoRecarga" name="montoRecarga" required>
                </div>
                <!--Botón para abrir el modal-->
                <button type="button" class="btn btn-dark w-100 shadow-lg" data-bs-toggle="modal" data-bs-target="#carteraModal">Recargar saldo</button>
            </form>
        </div>
    </div>

    <!--Modal de confirmación-->
    <div class="modal fade" id="carteraModal" tabindex="-1" aria-labelledby="carteraModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="carteraModalLabel">Confirmación</h5>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que deseas recargar tu saldo con esta cantidad?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <!--Este botón ahora enviará el formulario-->
                    <button type="submit" class="btn btn-primary" form="formRecarga">Confirmar</button>
                </div>
            </div>
        </div>
    </div>


</body>
</html>
