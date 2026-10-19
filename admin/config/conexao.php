<?php
define('servidor', 'localhost');
define('usuario', 'root');
define('senha', '3621');
define('bd', 'loja_games');

try {
    $opcoesPdo = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

    try {
        $pdo = new PDO(
            'mysql:host=' . servidor . ';dbname=' . bd . ';charset=utf8mb4',
            usuario,
            senha,
            $opcoesPdo
        );
    } catch (PDOException $e) {
        // Permite que a instalação inicial funcione mesmo sem o banco criado.
        if ((int) $e->errorInfo[1] !== 1049) {
            throw $e;
        }

        $pdoServidor = new PDO(
            'mysql:host=' . servidor . ';charset=utf8mb4',
            usuario,
            senha,
            $opcoesPdo
        );
        $pdoServidor->exec('CREATE DATABASE IF NOT EXISTS `' . bd . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $pdo = new PDO(
            'mysql:host=' . servidor . ';dbname=' . bd . ';charset=utf8mb4',
            usuario,
            senha,
            $opcoesPdo
        );
    }
} catch (PDOException $e) {
    die('Erro! Nao foi possivel conectar ao banco. Erro: ' . $e->getMessage());
}
