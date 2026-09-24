<?php

define('servidor', 'localhost');
define('usuario', 'root');
define('senha', '');
define('bd', 'loja_games');

try {
    $pdo = new PDO(
        'mysql:host=' . servidor . ';dbname=' . bd . ';charset=utf8mb4',
        usuario,
        senha
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    echo 'Erro! Não foi possível conectar ao banco. Erro: ' . $e->getMessage();
}

?>
