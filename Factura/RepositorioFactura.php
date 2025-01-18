<?php
require_once "Factura.php";

class RepositorioFactura {
    public $conexion;

    public function __construct($conexion2) {
        $this->conexion = $conexion2;
    }

    
    public function save(Factura $factura) {

        $factura->id === null || $factura->id == 0;
        $sql = "INSERT INTO factura (user_id, monto, fecha) VALUES (?, ?, ?)";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$factura->userId, $factura->monto, $factura->fecha]);
        $factura->id = $this->conexion->lastInsertId();

        return $factura;
    }

    
    public function findLastInvoicesByUserId($userId, $limit = 5) {
        $sql = "SELECT * FROM factura WHERE user_id = :user_id ORDER BY fecha DESC LIMIT :limit";
        $consulta = $this->conexion->prepare($sql);
        $consulta->bindParam(":user_id", $userId, PDO::PARAM_INT);
        $consulta->bindValue(":limit", $limit, PDO::PARAM_INT);
        $consulta->setFetchMode(PDO::FETCH_OBJ);
        $consulta->execute();

        $listaFacturas = [];
        while ($fila = $consulta->fetch()) {
            $factura = new Factura();
            $factura->setProperties($fila->id, $fila->user_id, $fila->monto, $fila->fecha);
            $listaFacturas[] = $factura;
        }

        return $listaFacturas;
    }
}
?>
