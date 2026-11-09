<?php
require_once __DIR__ . '/../config/autenticacao.php';
exigirAdministrador('../../login.php');
require_once __DIR__ . '/../models/taxonomias.php';

$tipo = (string) ($_GET['tipo'] ?? 'categorias');
if (!in_array($tipo, ['categorias', 'plataformas'], true)) {
    http_response_code(404);
    exit('Página não encontrada.');
}
$modelo = $tipo === 'categorias' ? new Categoria() : new Plataforma();
$rotulo = $tipo === 'categorias' ? 'Categorias' : 'Plataformas';
$url = 'gerenciar_taxonomia.php?tipo=' . $tipo;
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!tokenCsrfValido(is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : null)) {
            throw new RuntimeException('A sessão do formulário expirou.');
        }
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
        $acao = (string) ($_POST['acao'] ?? '');
        if ($acao === 'criar') {
            $ok = $modelo->criar((string) ($_POST['nome'] ?? ''), (string) ($_POST['descricao'] ?? ''));
        } elseif ($acao === 'atualizar') {
            $ok = $modelo->atualizar($id, (string) ($_POST['nome'] ?? ''), (string) ($_POST['descricao'] ?? ''));
        } elseif ($acao === 'excluir') {
            $ok = $modelo->excluir($id);
        } else {
            throw new RuntimeException('Ação inválida.');
        }
        if (!$ok) {
            throw new RuntimeException('Confira os dados e tente novamente.');
        }
        header('Location: ' . $url . '&salvo=1');
        exit;
    } catch (PDOException $e) {
        $erro = $e->getCode() === '23000' ? 'Esse nome já está cadastrado.' : 'Não foi possível salvar.';
    } catch (RuntimeException $e) {
        $erro = $e->getMessage();
    }
}
$edicaoId = filter_input(INPUT_GET, 'editar', FILTER_VALIDATE_INT) ?: 0;
$edicao = $edicaoId ? $modelo->buscar($edicaoId) : false;
$itens = $modelo->listar();
function h(string $valor): string { return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($rotulo) ?> | Administração</title>
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body>
<a class="skip-link" href="#conteudo">Ir para o conteúdo</a>
<header class="site-header"><nav class="nav" aria-label="Navegação administrativa">
    <a class="brand" href="../../index.php">PlayStation Store</a>
    <div class="nav-links"><a class="nav-link" href="ver_games.php">Jogos</a><a class="nav-link" href="ver_usuarios.php">Usuários</a><a class="nav-link" href="gerenciar_taxonomia.php?tipo=categorias">Categorias</a><a class="nav-link" href="gerenciar_taxonomia.php?tipo=plataformas">Plataformas</a><a class="nav-link" href="gerenciar_avaliacoes.php">Avaliações</a><a class="nav-link nav-primary" href="../../logout.php">Sair</a></div>
</nav></header>
<main class="container page" id="conteudo">
    <h1 class="page-title"><?= h($rotulo) ?></h1>
    <p class="page-intro">Cadastre e organize as opções disponíveis para os jogos.</p>
    <?php if ($erro !== ''): ?><div class="message message-error" role="alert"><?= h($erro) ?></div><?php endif; ?>
    <?php if (isset($_GET['salvo'])): ?><div class="message" role="status">Alteração salva com sucesso.</div><?php endif; ?>
    <section class="form-card">
        <h2><?= $edicao ? 'Editar' : 'Cadastrar' ?> <?= h(mb_strtolower(rtrim($rotulo, 's'))) ?></h2>
        <form method="post" action="<?= h($url) ?>">
            <input type="hidden" name="csrf_token" value="<?= h(gerarTokenCsrf()) ?>">
            <input type="hidden" name="acao" value="<?= $edicao ? 'atualizar' : 'criar' ?>">
            <?php if ($edicao): ?><input type="hidden" name="id" value="<?= (int) $edicao['id'] ?>"><?php endif; ?>
            <div class="form-grid">
                <div class="field"><label for="nome">Nome</label><input id="nome" name="nome" maxlength="50" value="<?= h($edicao['nome'] ?? '') ?>" required></div>
                <div class="field"><label for="descricao">Descrição</label><input id="descricao" name="descricao" maxlength="255" value="<?= h($edicao['descricao'] ?? '') ?>"></div>
            </div>
            <div class="form-actions"><button type="submit">Salvar</button><?php if ($edicao): ?><a class="button button-secondary" href="<?= h($url) ?>">Cancelar</a><?php endif; ?></div>
        </form>
    </section>
    <div class="table-shell"><table><thead><tr><th>Nome</th><th>Descrição</th><th>Ações</th></tr></thead><tbody>
        <?php foreach ($itens as $item): ?><tr>
            <td><?= h($item['nome']) ?></td><td><?= h($item['descricao']) ?></td>
            <td><div class="row-actions"><a class="button button-secondary" href="<?= h($url) ?>&editar=<?= (int) $item['id'] ?>">Editar</a>
            <form method="post" action="<?= h($url) ?>" onsubmit="return confirm('Excluir este item? Os jogos associados ficarão sem esta informação.')">
                <input type="hidden" name="csrf_token" value="<?= h(gerarTokenCsrf()) ?>"><input type="hidden" name="acao" value="excluir"><input type="hidden" name="id" value="<?= (int) $item['id'] ?>"><button class="button button-danger" type="submit">Excluir</button>
            </form></div></td>
        </tr><?php endforeach; ?>
    </tbody></table></div>
</main>
</body>
</html>
