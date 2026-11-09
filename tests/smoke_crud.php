<?php
require_once __DIR__ . '/../admin/models/usuarios.php';
require_once __DIR__ . '/../admin/models/games.php';
require_once __DIR__ . '/../admin/models/taxonomias.php';
require_once __DIR__ . '/../admin/models/avaliacoes.php';

function conferir(bool $condicao, string $mensagem): void
{
    if (!$condicao) {
        throw new RuntimeException($mensagem);
    }
}

global $pdo;
$pdo->beginTransaction();
try {
    $sufixo = bin2hex(random_bytes(4));
    $categorias = new Categoria();
    $plataformas = new Plataforma();
    $usuarios = new Usuario();
    $avaliacoes = new Avaliacao();

    conferir($categorias->criar('Teste cat ' . $sufixo, 'Categoria de teste'), 'Criar categoria');
    $categoriaId = (int) $pdo->lastInsertId();
    conferir($categorias->buscar($categoriaId) !== false, 'Ler categoria');
    conferir($categorias->atualizar($categoriaId, 'Categoria ' . $sufixo, 'Editada'), 'Editar categoria');
    conferir($plataformas->criar('Teste plataforma ' . $sufixo, ''), 'Criar plataforma');
    $plataformaId = (int) $pdo->lastInsertId();
    conferir($plataformas->buscar($plataformaId) !== false, 'Ler plataforma');
    conferir($plataformas->atualizar($plataformaId, 'Plataforma ' . $sufixo, 'Editada'), 'Editar plataforma');

    conferir($usuarios->cadastrarUsuario('Teste ' . $sufixo, $sufixo . '@example.com', 'teste12345', 'user', '11999999999'), 'Criar usuário');
    $usuarioId = (int) $pdo->lastInsertId();
    conferir($usuarios->buscarUsuarioPorId($usuarioId) !== false, 'Ler usuário');
    conferir($usuarios->editarUsuario($usuarioId, 'Teste editado', $sufixo . '@example.com', 'user', '11999999999'), 'Editar usuário');

    $game = new Game(['titulo' => 'Teste ' . $sufixo, 'descricao' => 'Jogo de teste', 'preco' => '10.00', 'estoque' => 2, 'categoria' => 'Categoria ' . $sufixo, 'plataforma' => 'Plataforma ' . $sufixo]);
    conferir($game->cadastrarGame(), 'Criar jogo');
    $gameId = (int) $pdo->lastInsertId();
    conferir($game->buscarGamePorId($gameId) !== false, 'Ler jogo');
    $edicao = new Game(['id' => $gameId, 'titulo' => 'Teste editado', 'descricao' => 'Editado', 'preco' => '12.00', 'estoque' => 3, 'categoria' => 'Categoria ' . $sufixo, 'plataforma' => 'Plataforma ' . $sufixo]);
    conferir($edicao->editarGame(), 'Editar jogo');

    conferir($avaliacoes->salvar($usuarioId, $gameId, 4, 'Bom'), 'Criar avaliação');
    conferir($avaliacoes->buscar($usuarioId, $gameId) !== false, 'Ler avaliação');
    conferir($avaliacoes->salvar($usuarioId, $gameId, 5, 'Excelente'), 'Editar avaliação');
    conferir((int) $avaliacoes->buscar($usuarioId, $gameId)['nota'] === 5, 'Conferir edição da avaliação');
    conferir($avaliacoes->remover($usuarioId, $gameId), 'Excluir avaliação');
    conferir($avaliacoes->buscar($usuarioId, $gameId) === false, 'Conferir exclusão da avaliação');
    conferir($edicao->deletarGame(), 'Excluir jogo');
    conferir($usuarios->deletarUsuario($usuarioId), 'Excluir usuário');
    conferir($plataformas->excluir($plataformaId), 'Excluir plataforma');
    conferir($categorias->excluir($categoriaId), 'Excluir categoria');
    $pdo->rollBack();
    echo "CRUD de 5 classes: OK\n";
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    exit(1);
}
