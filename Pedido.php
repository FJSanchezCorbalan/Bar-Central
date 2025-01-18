<?php 

class Pedido{
    public $id;
    public $id_user; 
    public $id_producto; 
    public $fecha;
    public $cantidad;

    public $precio;

    public function __construct(){

    }


    public function setProperties($id,$id_user,$id_producto,$fecha,$cantidad, $precio){
        $this->id = $id;
        $this->id_user = $id_user;
        $this->id_producto = $id_producto;
        $this->fecha = $fecha;
        $this->cantidad = $cantidad;
        $this->precio = $precio;
    }


}