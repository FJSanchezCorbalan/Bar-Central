<?php
require_once("Pedido.php");
//require_once("conexion.php");

class RepositorioPedido{
    public $conexion;

    public function __construct($conexion2){
        $this->conexion = $conexion2;
    }

    public function findById($id){
        $sql = "SELECT * FROM pedidos WHERE id=:id";
        $consulta = $this->conexion->prepare($sql);
        $consulta->setFetchMode(PDO::FETCH_OBJ);
        $consulta->bindParam(":id", $id);
        $consulta->execute();

        if($fila = $consulta->fetch()){
            $pedido = new Pedido();
            $pedido->setProperties($fila->id, $fila->id_user, $fila->id_producto, $fila->fecha, $fila->cantidad,$fila->precio);

        }else{
            $pedido= null;
        }

        return $pedido;
    }

    public function findAll(){
        $sql = "SELECT * FROM pedidos";
        $consulta = $this->conexion->prepare($sql);
        $consulta->setFetchMode(PDO::FETCH_OBJ);
        $consulta->execute();

        $listaPedidos = [];
        while ($fila = $consulta->fetch()){
            $pedido = new Pedido();
            $pedido->setProperties($fila->id, $fila->id_user, $fila->id_producto, $fila->fecha, $fila->cantidad,$fila->precio);
            $listaPedidos[]=$pedido;
            //array_push($listaProductos, $producto);            
        }
        return $listaPedidos;
    }

    public function save($p){
        if($p->id == null || $p->id == 0){
            $sql = "INSERT INTO pedidos (id_user, id_producto, fecha, cantidad,precio) VALUES (?, ?, ?, ?,?)";
            $consulta = $this->conexion->prepare($sql);
            $consulta->execute([$p->id_user, $p->id_producto, $p->fecha, $p->cantidad,$p->precio]);
            $p->id = $this->conexion->lastInsertId();            
        }else{
            
            if($this->findById($p->id)){
               
                $sql = "UPDATE pedidos SET id_user=?, id_producto=?, fecha=?, cantidad=? , precio = ?  WHERE id=? ";
                $consulta = $this->conexion->prepare($sql);
                $consulta->execute([$p->id_user,$p->id_producto,$p->fecha,$p->cantidad,$p->precio,$p->id]);
               
            }
        }

        return $p;
    }


    public function findPendientesByFecha($fecha){
        $sql = "SELECT * FROM pedidos WHERE DATE(fecha) = :fecha AND estado = 'pendiente'"; // Agregamos el filtro de estado
        $consulta = $this->conexion->prepare($sql);
        $consulta->setFetchMode(PDO::FETCH_OBJ);
        $consulta->bindParam(':fecha', $fecha);
        $consulta->execute();
    
        $listaPedidos = [];
        while ($fila = $consulta->fetch()){
            $pedido = new Pedido();
            $pedido->setProperties($fila->id, $fila->id_user, $fila->id_producto, $fila->fecha, $fila->cantidad, $fila->precio);
            $listaPedidos[] = $pedido;
        }
        return $listaPedidos;
    }

    public function marcarComoCompletado($id) {
        $sql = "UPDATE pedidos SET estado = 'completado' WHERE id = :id";
        $consulta = $this->conexion->prepare($sql);
        $consulta->bindParam(":id", $id);
        $consulta->execute();
    }

    public function marcarComoCancelado($id) {
        $sql = "UPDATE pedidos SET estado = 'cancelado' WHERE id = :id";
        $consulta = $this->conexion->prepare($sql);
        $consulta->bindParam(":id", $id);
        $consulta->execute();
    }
    
    

    public function findByFecha($fecha){
        $sql = "SELECT * FROM pedidos WHERE DATE(fecha)";
        $consulta = $this->conexion->prepare($sql);
        $consulta->setFetchMode(PDO::FETCH_OBJ);
        $consulta->bindParam(':fecha', $fecha);
        $consulta->execute();

        $listaPedidos = [];
        while ($fila = $consulta->fetch()){
            $pedido = new Pedido();
            $pedido->setProperties($fila->id, $fila->id_user, $fila->id_producto, $fila->fecha, $fila->cantidad,$fila->precio);
            $listaPedidos[]=$pedido;
            //array_push($listaProductos, $producto);            
        }
        return $listaPedidos;
    }

    public function deleteById($id){
        $sql = "DELETE FROM pedidos WHERE id = :id";
        $consulta = $this ->conexion->prepare($sql);
        $consulta->bindParam(":id", $id);
        $consulta->execute();
    }


    public function findLastByUser($id_user) {
        $sql = "SELECT p.id, p.id_user, p.id_producto, p.fecha, p.cantidad, p.precio 
                FROM pedidos p
                WHERE p.id_user = :id_user
                ORDER BY p.fecha DESC
                LIMIT 5";
        $consulta = $this->conexion->prepare($sql);
        $consulta->setFetchMode(PDO::FETCH_OBJ);
        $consulta->bindParam(':id_user', $id_user);
        $consulta->execute();
    
        $listaPedidos = [];
        while ($fila = $consulta->fetch()) {
            $pedido = new Pedido();
            $pedido->setProperties($fila->id, $fila->id_user, $fila->id_producto, $fila->fecha, $fila->cantidad, $fila->precio);
            $listaPedidos[] = $pedido;
        }
        return $listaPedidos;
    }

    //Metodo para obtener el producto favorito (el que mas veces se ha pedido)

    public function getFavoriteProduct($user_id) {
        $sql = "SELECT id_producto
                FROM pedidos
                WHERE id_user = :user_id
                GROUP BY id_producto
                ORDER BY COUNT(*) DESC
                LIMIT 1";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        
        if ($result) {
            return $result->id_producto;
        } else {

            return null;
        }
    }
    


    public function repetirPedido($idPedido) {

        $pedidoOriginal = $this->findById($idPedido);
        
        if ($pedidoOriginal) {
            $nuevoPedido = new Pedido();
            $nuevoPedido->setProperties(
                null, 
                $pedidoOriginal->id_user,
                $pedidoOriginal->id_producto,
                date('Y-m-d H:i:s'), 
                $pedidoOriginal->cantidad,
                $pedidoOriginal->precio
            );
            
            return $this->save($nuevoPedido);
        }
    
        return null;
}
    
    
}