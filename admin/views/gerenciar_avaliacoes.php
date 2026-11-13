<?php
require_once __DIR__ . '/../config/autenticacao.php';
require_once __DIR__ . '/../config/navegacao.php';
exigirAdministrador('../../login.php');
require_once __DIR__ . '/../models/avaliacoes.php';
require_once __DIR__ . '/../models/usuarios.php';
require_once __DIR__ . '/../models/games.php';

$modelo = new Avaliacao();
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!tokenCsrfValido(is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : null)) {
            throw new RuntimeException('A sessão do formulário expirou.');
        }
        $usuarioId = filter_input(INPUT_POST, 'usuario_id', FILTER_VALIDATE_INT) ?: 0;
        $gameId = filter_input(INPUT_POST, 'game_id', FILTER_VALIDATE_INT) ?: 0;
        $acao = (string) ($_POST['acao'] ?? '');
        if ($acao === 'salvar') {
            $nota = filter_input(INPUT_POST, 'nota', FILTER_VALIDATE_INT) ?: 0;
            $ok = $modelo->salvar($usuarioId, $gameId, $nota, (string) ($_POST['comentario'] ?? ''));
        } elseif ($acao === 'excluir') {
            $ok = $modelo->remover($usuarioId, $gameId);
        } else {
            throw new RuntimeException('Ação inválida.');
        }
        if (!$ok) {
            throw new RuntimeException('Confira os dados da avaliação.');
        }
        header('Location: gerenciar_avaliacoes.php?salvo=1');
        exit;
    } catch (PDOException $e) {
        $erro = 'Usuário ou jogo inválido.';
    } catch (RuntimeException $e) {
        $erro = $e->getMessage();
    }
}
$usuarioEdicao = filter_input(INPUT_GET, 'usuario_id', FILTER_VALIDATE_INT) ?: 0;
$gameEdicao = filter_input(INPUT_GET, 'game_id', FILTER_VALIDATE_INT) ?: 0;
$edicao = $usuarioEdicao && $gameEdicao ? $modelo->buscar($usuarioEdicao, $gameEdicao) : false;
$usuarios = (new Usuario())->listarUsuarios();
$games = (new Game())->listarGames();
$avaliacoes = $modelo->listarTodas();
function h(string $valor): string { return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Avaliações | Administração</title><link rel="stylesheet" href="../public/css/style.css"></head>
<body><a class="skip-link" href="#conteudo">Ir para o conteúdo</a><header class="site-header"><nav class="nav" aria-label="Navegação administrativa"><a class="brand" href="../../index.php">PlayStation Store</a><div class="nav-links"><a class="nav-link" href="ver_games.php">Jogos</a><a class="nav-link" href="ver_usuarios.php">Usuários</a><a class="nav-link" href="gerenciar_taxonomia.php?tipo=categorias">Categorias</a><a class="nav-link" href="gerenciar_taxonomia.php?tipo=plataformas">Plataformas</a><a class="nav-link" aria-current="page" href="gerenciar_avaliacoes.php">Avaliações</a><?= linkCarteiraComSaldo(null, false, '../../carteira.php') ?><a class="nav-link nav-primary" href="../../logout.php">Sair</a></div></nav></header>
<main class="container page" id="conteudo"><h1 class="page-title">Avaliações</h1><p class="page-intro">Gerencie as notas e comentários dos jogos.</p>
<?php if ($erro !== ''): ?><div class="message message-error" role="alert"><?= h($erro) ?></div><?php endif; ?>
<?php if (isset($_GET['salvo'])): ?><div class="message" role="status">Alteração salva com sucesso.</div><?php endif; ?>
<section class="form-card"><h2><?= $edicao ? 'Editar avaliação' : 'Nova avaliação' ?></h2><form method="post"><input type="hidden" name="csrf_token" value="<?= h(gerarTokenCsrf()) ?>"><input type="hidden" name="acao" value="salvar"><div class="form-grid">
<div class="field"><label for="usuario_id">Usuário</label><select id="usuario_id" name="usuario_id" required <?= $edicao ? 'disabled' : '' ?>><option value="">Selecione</option><?php foreach ($usuarios as $usuario): ?><option value="<?= (int) $usuario['id'] ?>" <?= $edicao && (int) $edicao['usuario_id'] === (int) $usuario['id'] ? 'selected' : '' ?>><?= h($usuario['nome']) ?> (<?= h($usuario['email']) ?>)</option><?php endforeach; ?></select><?php if ($edicao): ?><input type="hidden" name="usuario_id" value="<?= (int) $edicao['usuario_id'] ?>"><?php endif; ?></div>
<div class="field"><label for="game_id">Jogo</label><select id="game_id" name="game_id" required <?= $edicao ? 'disabled' : '' ?>><option value="">Selecione</option><?php foreach ($games as $game): ?><option value="<?= (int) $game['id'] ?>" <?= $edicao && (int) $edicao['game_id'] === (int) $game['id'] ? 'selected' : '' ?>><?= h($game['titulo']) ?></option><?php endforeach; ?></select><?php if ($edicao): ?><input type="hidden" name="game_id" value="<?= (int) $edicao['game_id'] ?>"><?php endif; ?></div>
<div class="field"><label for="nota">Nota</label><select id="nota" name="nota" required><?php for ($nota = 1; $nota <= 5; $nota++): ?><option value="<?= $nota ?>" <?= (int) ($edicao['nota'] ?? 5) === $nota ? 'selected' : '' ?>><?= $nota ?></option><?php endfor; ?></select></div>
<div class="field field-full"><label for="comentario">Comentário</label><textarea id="comentario" name="comentario" rows="3" maxlength="1000"><?= h($edicao['comentario'] ?? '') ?></textarea></div>
</div><div class="form-actions"><button type="submit">Salvar avaliação</button><?php if ($edicao): ?><a class="button button-secondary" href="gerenciar_avaliacoes.php">Cancelar</a><?php endif; ?></div></form></section>
<div class="table-shell"><table><thead><tr><th>Usuário</th><th>Jogo</th><th>Nota</th><th>Comentário</th><th>Ações</th></tr></thead><tbody><?php foreach ($avaliacoes as $avaliacao): ?><tr>
<td><?= h($avaliacao['usuario_nome']) ?></td><td><?= h($avaliacao['game_titulo']) ?></td><td><?= (int) $avaliacao['nota'] ?>/5</td><td><?= h($avaliacao['comentario']) ?></td><td><div class="row-actions"><a class="button button-secondary" href="gerenciar_avaliacoes.php?usuario_id=<?= (int) $avaliacao['usuario_id'] ?>&game_id=<?= (int) $avaliacao['game_id'] ?>">Editar</a><form method="post" onsubmit="return confirm('Excluir esta avaliação?')"><input type="hidden" name="csrf_token" value="<?= h(gerarTokenCsrf()) ?>"><input type="hidden" name="acao" value="excluir"><input type="hidden" name="usuario_id" value="<?= (int) $avaliacao['usuario_id'] ?>"><input type="hidden" name="game_id" value="<?= (int) $avaliacao['game_id'] ?>"><button class="button button-danger" type="submit">Excluir</button></form></div></td>
</tr><?php endforeach; ?></tbody></table></div></main></body></html>
