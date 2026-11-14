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
    $quantidades = [
        'usuarios' => 2,
        'carteiras' => 2,
        'movimentacoes_carteira' => 2,
        'categorias' => 3,
        'plataformas' => 2,
        'games' => 3,
        'compras' => 1,
        'wishlist' => 1,
        'avaliacoes' => 1,
    ];
    foreach ($quantidades as $tabela => $esperado) {
        if ((int) $servidor->query('SELECT COUNT(*) FROM `' . $tabela . '`')->fetchColumn() !== $esperado) {
            throw new RuntimeException('Dados iniciais incompletos em ' . $tabela . '.');
        }
    }
    $saldo = $servidor->query(
        "SELECT c.saldo FROM carteiras c JOIN usuarios u ON u.id = c.usuario_id
         WHERE u.email = 'pedro@gmail.com'"
    )->fetchColumn();
    $estoque = $servidor->query("SELECT estoque FROM games WHERE titulo = 'God of War'")->fetchColumn();
    if ($saldo !== '50.01' || (int) $estoque !== 14) {
        throw new RuntimeException('Saldo ou estoque inicial não corresponde à compra de exemplo.');
    }
    echo "Schema novo, relações e dados iniciais: OK\n";
} finally {
    $servidor->exec('DROP DATABASE IF EXISTS `' . $bancoTemporario . '`');
}
