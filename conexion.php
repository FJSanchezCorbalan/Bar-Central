<?php
$host = 'localhost';
$dbname = 'h0122u0007_fernando';
$username = 'fernando';
$password = 'Murcia2024';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    avatar VARCHAR(255),
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    saldo DECIMAL(10,2) DEFAULT 0,
    confirmado BOOLEAN,
    clave_confirmacion varchar(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

try {
    $pdo->exec($sql);
} catch(PDOException $e) {
    die("Error creando tabla: " . $e->getMessage());
}

$sql = "CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    descripcion TEXT,
    tamanio VARCHAR(50),
    tipo VARCHAR(50),
    imagen VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$pdo->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha DATETIME,
    cantidad INT,
    precio DECIMAL(10, 2) NOT NULL,
    id_user INT NOT NULL,
    id_producto INT NOT NULL,
    estado ENUM('pendiente', 'completado', 'cancelado') DEFAULT 'pendiente',  -- Nueva columna 'estado'
    FOREIGN KEY (id_user) REFERENCES users(id),
    FOREIGN KEY (id_producto) REFERENCES productos(id)
)";
$pdo->exec($sql);


$sql = "CREATE TABLE IF NOT EXISTS factura (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    monto DECIMAL(10, 2) NOT NULL,
    fecha DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);";

$pdo->exec($sql);

