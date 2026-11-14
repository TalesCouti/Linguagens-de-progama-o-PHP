<?php
require_once __DIR__ . '/../admin/config/conexao.php';

$bancoTemporario = 'loja_games_teste_' . bin2hex(random_bytes(4));
$servidor = new PDO(
    'mysql:host=' . servidor . ';charset=utf8mb4',
    usuario,
    senha,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

try {
    $sql = file_get_contents(__DIR__ . '/../banco de dados/schema.txt');
    $sql = str_replace('loja_games', $bancoTemporario, $sql);
    foreach (explode(';', $sql) as $comando) {
        if (trim($comando) !== '') {
            $servidor->exec($comando);
        }
    }

    $tabelas = $servidor->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    if (count($tabelas) !== 9) {
        throw new RuntimeException('O schema não criou as nove tabelas esperadas.');
    }
    $chaves = $servidor->query(
        'SELECT COUNT(*) FROM information_schema.REFERENTIAL_CONSTRAINTS
         WHERE CONSTRAINT_SCHEMA = DATABASE()'
    )->fetchColumn();
    if ((int) $chaves < 9) {
        throw new RuntimeException('O schema não criou todas as relações esperadas.');
    }
    $usuarios = $servidor->query('SELECT email, senha FROM usuarios')->fetchAll(PDO::FETCH_KEY_PAIR);
    if (!password_verify('admin123', $usuarios['evandro@gmail.com'] ?? '')
        || !password_verify('user12345', $usuarios['pedro@gmail.com'] ?? '')) {
        throw new RuntimeException('As contas de teste não autenticam.');
    }
    if ((int) $servidor->query('SELECT COUNT(*) FROM games')->fetchColumn() !== 3) {
        throw new RuntimeException('O schema não inseriu os jogos de teste.');
    }
    echo "Schema novo, relações e dados iniciais: OK\n";
} finally {
    $servidor->exec('DROP DATABASE IF EXISTS `' . $bancoTemporario . '`');
}
