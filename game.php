<?php
require_once __DIR__ . '/admin/config/autenticacao.php';
require_once __DIR__ . '/admin/models/games.php';
require_once __DIR__ . '/admin/config/imagens.php';

iniciarSessaoSegura();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$gameModel = new Game();
$game = $id ? $gameModel->buscarGamePorId($id) : false;

if (!$game) {
    http_response_code(404);
}

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$tituloPagina = $game ? $game['titulo'] . ' | PlayStation Store' : 'Jogo não encontrado | PlayStation Store';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Veja os detalhes do jogo e compre na PlayStation Store.">
    <title><?= h($tituloPagina) ?></title>
    <link rel="icon" type="image/png" href="admin/public/imagens/logo.png">
    <link rel="stylesheet" href="admin/public/css/style.css">
</head>
<body class="storefront-page game-detail-page">
    <a class="skip-link" href="#conteudo">Ir para o conteúdo</a>
    <div class="sony-ribbon" aria-hidden="true"><span>SONY</span></div>
    <header class="site-header">
        <nav class="nav" aria-label="Navegação principal">
            <a class="brand store-brand" href="index.php">
                <span class="brand-mark"><img src="admin/public/imagens/logo.png" alt=""></span>
                <span>PlayStation <strong>Store</strong></span>
            </a>
            <div class="nav-links">
                <a class="nav-link" href="index.php#catalogo">Catálogo</a>
                <?php if (usuarioAdministradorAutenticado()): ?>
                    <a class="nav-link" href="admin/views/ver_games.php">Painel</a>
                    <a class="nav-link nav-primary" href="logout.php">Sair</a>
                <?php elseif (usuarioAutenticado()): ?>
                    <a class="nav-link nav-primary" href="logout.php">Sair</a>
                <?php else: ?>
                    <a class="nav-link nav-primary" href="login.php<?= $game ? '?redirect=' . rawurlencode('game.php?id=' . (int) $game['id']) : '' ?>">Entrar</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main id="conteudo" class="page">
        <div class="container">
            <a class="game-detail-back" href="index.php#catalogo">Voltar ao catálogo</a>

            <?php if (!$game): ?>
                <div class="empty-state game-not-found">
                    <p class="eyebrow">Erro 404</p>
                    <h1>Jogo não encontrado</h1>
                    <p>Este item não existe ou não está mais disponível no catálogo.</p>
                    <a class="button" href="index.php#catalogo">Explorar jogos</a>
                </div>
            <?php else: ?>
                <?php $disponivel = (int) $game['estoque'] > 0; ?>
                <article class="game-detail">
                    <div class="game-detail-media">
                        <img class="game-detail-cover" src="admin/public/imagens/<?= rawurlencode(imagemGameOuPlaceholder($game['imagem'] ?? '')) ?>" alt="Capa de <?= h($game['titulo']) ?>" width="638" height="480">
                    </div>

                    <div class="game-detail-content">
                        <p class="game-category"><?= h($game['categoria'] ?: 'Sem categoria') ?></p>
                        <h1><?= h($game['titulo']) ?></h1>
                        <p class="game-detail-description"><?= h($game['descricao'] ?: 'Uma nova experiência espera por você.') ?></p>

                        <?php if (($_GET['compra'] ?? '') === 'sucesso'): ?>
                            <div class="message" role="status">Compra realizada. O estoque foi atualizado.</div>
                        <?php elseif (($_GET['compra'] ?? '') === 'sem_estoque'): ?>
                            <div class="message message-error" role="alert">Este jogo ficou sem estoque antes da compra.</div>
                        <?php endif; ?>

                        <div class="purchase-panel">
                            <div>
                                <span class="purchase-label">Preço</span>
                                <strong class="game-detail-price">R$ <?= number_format((float) $game['preco'], 2, ',', '.') ?></strong>
                            </div>
                            <div class="stock-status<?= $disponivel ? '' : ' sold-out' ?>">
                                <?= $disponivel ? (int) $game['estoque'] . ' unidade(s) em estoque' : 'Produto esgotado' ?>
                            </div>

                            <?php if (!$disponivel): ?>
                                <button type="button" disabled>Esgotado</button>
                            <?php elseif (usuarioAutenticado()): ?>
                                <form class="purchase-form" method="post" action="admin/controllers/comprar_game.php">
                                    <input type="hidden" name="csrf_token" value="<?= h(gerarTokenCsrf()) ?>">
                                    <input type="hidden" name="id" value="<?= (int) $game['id'] ?>">
                                    <button type="submit">Comprar agora</button>
                                </form>
                            <?php else: ?>
                                <a class="button purchase-login" href="login.php?redirect=<?= rawurlencode('game.php?id=' . (int) $game['id']) ?>">Entrar para comprar</a>
                            <?php endif; ?>
                            <p class="purchase-note">A compra adiciona uma unidade à sua conta e atualiza o estoque imediatamente.</p>
                        </div>
                    </div>
                </article>
            <?php endif; ?>
        </div>
    </main>

    <footer class="site-footer">
        <div class="container">PlayStation. Feito para quem gosta de boas histórias.</div>
    </footer>
</body>
</html>
