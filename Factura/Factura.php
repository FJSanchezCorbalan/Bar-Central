<?php
class Factura {
    public $id;
    public $userId;
    public $monto;
    public $fecha;

    public function setProperties($id, $userId, $monto, $fecha) {
        $this->id = $id;
        $this->userId = $userId;
        $this->monto = $monto;
        $this->fecha = $fecha;
    }
}
?>
