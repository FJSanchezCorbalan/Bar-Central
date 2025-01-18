<?php
    class Producto{
        public $id; 
        public $nombre; 
        public $descripcion;
        public $tamanio;
        public $precio; 
        public $tipo;
        public $imagen; 
        public $created_at;

        public function __construct(){
            $this->id = 0;
        }

        public function setProperties($id2, $nombre2, $descripcion2, $tamanio2, $precio2, $tipo2, $imagen2){
            $this->id = $id2;
            $this->nombre = $nombre2;
            $this->descripcion = $descripcion2;
            $this->tamanio = $tamanio2;
            $this->precio = $precio2;
            $this->imagen = $imagen2;
            $this->tipo = $tipo2;
        }

        public function toString(){
            return $this->id . " - " . $this->nombre . " - " . $this->tipo;
        }



    
    }