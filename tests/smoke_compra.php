<?php
require_once __DIR__ . '/../admin/models/usuarios.php';
require_once __DIR__ . '/../admin/models/games.php';
require_once __DIR__ . '/../admin/models/carteiras.php';
require_once __DIR__ . '/../admin/models/taxonomias.php';

function verificar(bool $condicao, string $mensagem): void
{
    if (!$condicao) {
        throw new RuntimeException($mensagem);
    }
}

global $pdo;
$usuarioId = 0;
$gameId = 0;
$erro = null;
try {
    $categoria = (new Categoria())->listar()[0]['nome'] ?? null;
    $plataforma = (new Plataforma())->listar()[0]['nome'] ?? null;
    verificar(is_string($categoria) && is_string($plataforma), 'Cadastre categoria e plataforma antes do teste.');

    $sufixo = bin2hex(random_bytes(5));
    $usuarios = new Usuario();
    verificar($usuarios->cadastrarUsuario('Teste compra', 'compra-' . $sufixo . '@example.com', 'senha-teste-123', 'user'), 'Cadastro do usuário de teste.');
    $usuarioId = (int) $pdo->lastInsertId();

    $game = new Game([
        'titulo' => 'Teste compra ' . $sufixo,
        'descricao' => 'Jogo temporário para conferir a compra.',
        'preco' => '10.00',
        'estoque' => 1,
        'categoria' => $categoria,
        'plataforma' => $plataforma,
    ]);
    verificar($game->cadastrarGame(), 'Cadastro do jogo de teste.');
    $gameId = (int) $pdo->lastInsertId();

    $carteira = new Carteira();
    verificar($game->comprarGame($gameId, $usuarioId) === 'saldo_insuficiente', 'Compra sem saldo deve ser recusada.');
    verificar((int) $game->buscarGamePorId($gameId)['estoque'] === 1, 'Compra recusada não pode reduzir o estoque.');
    verificar($carteira->adicionarSaldo($usuarioId, '20.00'), 'Adicionar saldo.');
    verificar($game->comprarGame($gameId, $usuarioId) === 'sucesso', 'Compra com saldo e estoque.');
    verificar($carteira->consultarSaldo($usuarioId) === '10.00', 'O saldo deve ser descontado.');
    verificar((int) $game->buscarGamePorId($gameId)['estoque'] === 0, 'O estoque deve ser reduzido.');
    verificar(count($game->listarComprasDoUsuario($usuarioId)) === 1, 'O jogo deve entrar na biblioteca.');
    verificar($game->comprarGame($gameId, $usuarioId) === 'sem_estoque', 'Compra sem estoque deve ser recusada.');
    echo "Carteira, compra, estoque e biblioteca: OK\n";
} catch (Throwable $e) {
    $erro = $e;
} finally {
    if ($usuarioId > 0) {
        $pdo->prepare('DELETE FROM usuarios WHERE id = ?')->execute([$usuarioId]);
    }
    if ($gameId > 0) {
        $pdo->prepare('DELETE FROM games WHERE id = ?')->execute([$gameId]);
    }
}
if ($erro !== null) {
    fwrite(STDERR, $erro->getMessage() . PHP_EOL);
    exit(1);
}
