<?php
define('servidor', 'localhost');
define('usuario', 'root');
define('senha', '3621');
define('bd', 'loja_games');

try {
    $pdo = new PDO(
        'mysql:host=' . servidor . ';dbname=' . bd . ';charset=utf8mb4',
        usuario,
        senha,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die('Erro! Nao foi possivel conectar ao banco. Erro: ' . $e->getMessage());
}
