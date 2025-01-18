<?php
require_once("Usuario.php");
class RepositorioUsuario {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }


    public function getAvatarById($id_user) {
        $sql = "SELECT avatar FROM users WHERE id = :id_user";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id_user', $id_user);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        
       
        if ($result) {
            return $result->avatar; 
        } else {
            return null; 
        }
    }

    public function getUserById($user_id) {
        $sql = "SELECT * FROM users WHERE id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_OBJ);
        
        if ($usuario) {
            $usuarioObj = new Usuario();
            $usuarioObj->setProperties($usuario->id, $usuario->username, $usuario->email, $usuario->avatar,$usuario->saldo);
            return $usuarioObj;
        } else {
            return null; 
        }
    }

    public function recargarSaldo($userId, $monto) {
    
        $sql = "UPDATE users SET saldo = saldo + :monto WHERE id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':monto', $monto);
        $stmt->bindParam(':user_id', $userId);
    
        return $stmt->execute();
    }
    
    public function pagar($userId, $monto) {

        $usuario = $this->getUserById($userId); 
        if ($usuario->saldo >= $monto && $monto > 0) {
           
            $sql = "UPDATE users SET saldo = saldo - :monto WHERE id = :user_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':monto', $monto);
            $stmt->bindParam(':user_id', $userId);
    

            if ($stmt->execute()) {

                $factura = new Factura();
                $factura->userId = $userId;
                $factura->monto = $monto;
                $factura->fecha = date("Y-m-d H:i:s");
    

                $repoFactura = new RepositorioFactura($this->pdo);
                $repoFactura->save($factura);
    
                return true; 
            }
        }
        return false; 
    }
     

}


?>
