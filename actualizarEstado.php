<?php
ini_set('display_errors', 1);  
ini_set('display_startup_errors', 1);  
error_reporting(E_ALL);  
require_once("conexion.php");
require_once("RepositorioPedido.php");

if (isset($_GET['estado']) && isset($_GET['id'])) {
    $estado = $_GET['estado'];
    $id = $_GET['id'];

    
    $repoPedido = new RepositorioPedido($pdo);

    
    if ($estado == 'completado') {
        $repoPedido->marcarComoCompletado($id);
        echo "El estado del pedido ha sido actualizado a 'Completado'.";
    } elseif ($estado == 'cancelado') {
        $repoPedido->marcarComoCancelado($id);
        echo "El estado del pedido ha sido actualizado a 'Cancelado'.";
    } else {
        echo "Estado no válido.";
    }

  
    header("Location:listarpedidosHoy.php");
    exit();
} else {
    echo "Parámetros incorrectos.";
}
?>
