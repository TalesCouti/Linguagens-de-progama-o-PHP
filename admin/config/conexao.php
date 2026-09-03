<?php
define('servidor', 'localhost');
define('usuario','root');
define('senha', ' ');
define('bd', 'loja_games');
try{
    $pdo = new PDO('mysql:host='.serivodr.'/dbname='.bd,usuario,senha);
} catch(PDOException $e){
    echo 'Erro! Nao foi possivel conectar ao banco. Erro: ' . $e -> getMessage();
}
?>