<?php
require_once("comprueba.php");
require_once("conexion.php");
require_once("Pedido.php");
require_once("RepositorioPedido.php");
require_once("usuarios/RepositorioUsuario.php");
require_once("Factura/Factura.php");
require_once("Factura/RepositorioFactura.php");

if (!isset($_POST['id_pedido'])) {
    header("Location: perfil.php");
    exit;
}

$repoPedidos = new RepositorioPedido($pdo);
$repoUsuarios = new RepositorioUsuario($pdo);
$repoFacturas = new RepositorioFactura($pdo); 


$idPedido = $_POST['id_pedido'];


$pedidoOriginal = $repoPedidos->findById($idPedido);

if ($pedidoOriginal) {

    $resultadoPago = $repoUsuarios->pagar($_SESSION['user_id'], $pedidoOriginal->precio);

    $usuario = $repoUsuarios->getUserById($_SESSION['user_id']);
    $_SESSION['saldo'] = $usuario->saldo;

    if ($resultadoPago) {

        $nuevoPedido = new Pedido();
        $nuevoPedido->setProperties(
            null,
            $pedidoOriginal->id_user,
            $pedidoOriginal->id_producto,
            date('Y-m-d H:i:s'),
            $pedidoOriginal->cantidad,
            $pedidoOriginal->precio
        );
        

        $pedidoGuardado = $repoPedidos->save($nuevoPedido);

        $_SESSION['mensaje'] = "<div class='alert alert-success'>Pago exitoso y pedido repetido.</div>";

    }else{
        $_SESSION['mensaje'] = "<div class='alert alert-danger'>No tienes suficiente saldo para completar el pago.</div>";
    }
}

header("Location: usuarios/perfil.php");
exit;