<?php
require_once("Producto.php");
//require_once("conexion.php");

class RepositorioProducto{
    public $conexion;

    public function __construct($conexion2){
        $this->conexion = $conexion2;
    }

    public function findById($id){
        $sql = "SELECT * FROM productos WHERE id=:id";
        $consulta = $this->conexion->prepare($sql);
        $consulta->setFetchMode(PDO::FETCH_OBJ);
        $consulta->bindParam(":id", $id);
        $consulta->execute();

        if($fila = $consulta->fetch()){
            $producto = new Producto();
            $producto->setProperties($fila->id, $fila->nombre, $fila->descripcion, $fila->tamanio, $fila->precio, $fila->tipo, $fila->imagen);

        }else{
            $producto = null;
        }

        return $producto;
    }

    public function findAll(){
        $sql = "SELECT * FROM productos";
        $consulta = $this->conexion->prepare($sql);
        $consulta->setFetchMode(PDO::FETCH_OBJ);
        $consulta->execute();

        $listaProductos = [];
        while ($fila = $consulta->fetch()){
            $producto = new Producto();
            $producto->setProperties($fila->id, $fila->nombre, $fila->descripcion, $fila->tamanio, $fila->precio, $fila->tipo, $fila->imagen);
            $listaProductos[]=$producto;
            //array_push($listaProductos, $producto);            
        }
        return $listaProductos;
    }

    public function findAllBocadillos() {
        $sql = "SELECT * FROM productos WHERE tipo = 'Comida' ORDER BY nombre ASC, tamanio DESC";
        return $this->fetchProductos($sql);
    }
    
    public function findAllBebidas() {
        $sql = "SELECT * FROM productos WHERE tipo = 'Bebida' ORDER BY nombre ASC";
        return $this->fetchProductos($sql);
    }
    
    public function findAllOtros() {
        $sql = "SELECT * FROM productos WHERE tipo = 'Otro' ORDER BY nombre ASC";
        return $this->fetchProductos($sql);
    }

    private function fetchProductos($sql) {
        $consulta = $this->conexion->prepare($sql);
        $consulta->setFetchMode(PDO::FETCH_OBJ);
        $consulta->execute();
    
        $listaProductos = [];
        while ($fila = $consulta->fetch()) {
            $producto = new Producto();
            $producto->setProperties($fila->id, $fila->nombre, $fila->descripcion, $fila->tamanio, $fila->precio, $fila->tipo, $fila->imagen);
            $listaProductos[] = $producto;
        }
        return $listaProductos;
    }
    


    



    public function save($p){
        if($p->id == null || $p->id == 0){
            $sql = "INSERT INTO productos (nombre, descripcion, tamanio, precio, tipo, imagen) VALUES (?, ?, ?, ?, ?, ?)";
            $consulta = $this->conexion->prepare($sql);
            $consulta->execute([$p->nombre, $p->descripcion, $p->tamanio, $p->precio, $p->tipo, $p->imagen]);
            $p->id = $this->conexion->lastInsertId();            
        }else{
            
            if($this->findById($p->id)){
                if($p->imagen == ""){
                    $sql = "UPDATE productos SET nombre=?, descripcion=?, tamanio=?, precio=?, tipo=? WHERE id=? ";
                    $consulta = $this->conexion->prepare($sql);
                    $nombre = $p->nombre;
                    $consulta->execute([$nombre, $p->descripcion, $p->tamanio, $p->precio, $p->tipo, $p->id]);
                }else{
                    $sql = "UPDATE productos SET nombre=?, descripcion=?, tamanio=?, precio=?, tipo=?, imagen=? WHERE id=? ";
                    $consulta = $this->conexion->prepare($sql);
                    $nombre = $p->nombre;
                    $consulta->execute([$nombre, $p->descripcion, $p->tamanio, $p->precio, $p->tipo, $p->imagen, $p->id]);
                }
                

            }
        }

        return $p;
    }



    public function getImagenById($id){
        $sql = "SELECT * FROM productos WHERE id=:id";
        $consulta = $this->conexion->prepare($sql);
        $consulta->setFetchMode(PDO::FETCH_OBJ);
        $consulta->bindParam(":id", $id);
        $consulta->execute();

        if($fila = $consulta->fetch()){
            $producto = new Producto();
            $producto->setProperties($fila->id, $fila->nombre, $fila->descripcion, $fila->tamanio, $fila->precio, $fila->tipo, $fila->imagen);

        }else{
            $producto = null;
        }

        return $producto->imagen;
    }


    public function deleteById($id){
        $sql = "DELETE FROM productos WHERE id = :id";
        $consulta = $this ->conexion->prepare($sql);
        $consulta->bindParam(":id", $id);
        $consulta->execute();
    }
}
