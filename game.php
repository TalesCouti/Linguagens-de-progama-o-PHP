<?php
require_once __DIR__ . '/admin/config/autenticacao.php';
require_once __DIR__ . '/admin/models/games.php';
require_once __DIR__ . '/admin/models/carteiras.php';
require_once __DIR__ . '/admin/models/interacoes.php';
require_once __DIR__ . '/admin/config/imagens.php';

iniciarSessaoSegura();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$gameModel = new Game();
$game = $id ? $gameModel->buscarGamePorId($id) : false;
$saldoCarteira = usuarioAutenticado() ? (new Carteira())->consultarSaldo((int) $_SESSION['usuario_id']) : null;
$saldoInsuficiente = $game && $saldoCarteira !== null && (float) $saldoCarteira < (float) $game['preco'];
$interacoes = new Interacoes();
$avaliacoes = $game ? $interacoes->listarAvaliacoes((int) $game['id']) : [];
$naWishlist = $game && usuarioAutenticado() ? $interacoes->estaNaWishlist((int) $_SESSION['usuario_id'], (int) $game['id']) : false;
$minhaAvaliacao = null;
foreach ($avaliacoes as $avaliacao) {
    if (usuarioAutenticado() && (int) $avaliacao['usuario_id'] === (int) $_SESSION['usuario_id']) {
        $minhaAvaliacao = $avaliacao;
        break;
    }
}
$media = $avaliacoes ? array_sum(array_column($avaliacoes, 'nota')) / count($avaliacoes) : null;

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
                <?php if (usuarioAutenticado()): ?>
                    <a class="nav-link" href="meus_jogos.php">Meus jogos</a>
                    <a class="nav-link" href="wishlist.php">Wishlist</a>
                    <a class="nav-link nav-wallet" href="carteira.php">Carteira</a>
                <?php endif; ?>
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
                        <p class="rating-summary"><?= $media === null ? 'Ainda sem avaliações' : number_format($media, 1, ',', '.') . ' / 5 · ' . count($avaliacoes) . (count($avaliacoes) === 1 ? ' avaliação' : ' avaliações') ?></p>

                        <?php if (($_GET['compra'] ?? '') === 'sucesso'): ?>
                            <div class="message" role="status">Compra realizada. O jogo está na sua biblioteca e o saldo foi atualizado.</div>
                        <?php elseif (($_GET['compra'] ?? '') === 'sem_estoque'): ?>
                            <div class="message message-error" role="alert">Este jogo ficou sem estoque antes da compra.</div>
                        <?php elseif (($_GET['compra'] ?? '') === 'saldo_insuficiente'): ?>
                            <div class="message message-error" role="alert">Saldo insuficiente para esta compra. <a href="carteira.php?jogo=<?= (int) $game['id'] ?>">Adicione saldo à carteira</a> para continuar.</div>
                        <?php endif; ?>

                        <div class="purchase-panel">
                            <div>
                                <span class="purchase-label">Preço</span>
                                <strong class="game-detail-price">R$ <?= number_format((float) $game['preco'], 2, ',', '.') ?></strong>
                            </div>
                            <div class="stock-status<?= $disponivel ? '' : ' sold-out' ?>">
                                <?= $disponivel ? (int) $game['estoque'] . ' unidade(s) em estoque' : 'Produto esgotado' ?>
                            </div>
                            <?php if ($saldoCarteira !== null): ?>
                                <p class="wallet-available">Seu saldo: <strong>R$ <?= number_format((float) $saldoCarteira, 2, ',', '.') ?></strong> · <a href="carteira.php?jogo=<?= (int) $game['id'] ?>">Adicionar saldo</a></p>
                            <?php endif; ?>

                            <?php if (!$disponivel): ?>
                                <button type="button" disabled>Esgotado</button>
                            <?php elseif ($saldoInsuficiente): ?>
                                <a class="button" href="carteira.php?jogo=<?= (int) $game['id'] ?>">Adicionar saldo para comprar</a>
                            <?php elseif (usuarioAutenticado()): ?>
                                <form class="purchase-form" method="post" action="admin/controllers/comprar_game.php">
                                    <input type="hidden" name="csrf_token" value="<?= h(gerarTokenCsrf()) ?>">
                                    <input type="hidden" name="id" value="<?= (int) $game['id'] ?>">
                                    <button type="submit">Comprar agora</button>
                                </form>
                            <?php else: ?>
                                <a class="button purchase-login" href="login.php?redirect=<?= rawurlencode('game.php?id=' . (int) $game['id']) ?>">Entrar para comprar</a>
                            <?php endif; ?>
                            <p class="purchase-note">A compra reduz uma unidade do estoque imediatamente.</p>
                        </div>
                        <?php if (usuarioAutenticado()): ?>
                            <form class="wishlist-action" method="post" action="admin/controllers/wishlist.php">
                                <input type="hidden" name="csrf_token" value="<?= h(gerarTokenCsrf()) ?>">
                                <input type="hidden" name="id" value="<?= (int) $game['id'] ?>">
                                <input type="hidden" name="acao" value="<?= $naWishlist ? 'remover' : 'adicionar' ?>">
                                <button class="button button-secondary" type="submit"><?= $naWishlist ? 'Remover da wishlist' : 'Adicionar à wishlist' ?></button>
                            </form>
                        <?php else: ?>
                            <a class="button button-secondary" href="login.php?redirect=<?= rawurlencode('game.php?id=' . (int) $game['id']) ?>">Entre para salvar na wishlist</a>
                        <?php endif; ?>
                    </div>
                </article>
                <section class="reviews" id="avaliacoes" aria-labelledby="reviews-title">
                    <h2 id="reviews-title">Avaliações dos jogadores</h2>
                    <?php if (($_GET['avaliacao'] ?? '') === 'salva'): ?><div class="message" role="status">Avaliação atualizada.</div><?php endif; ?>
                    <?php if (($_GET['avaliacao'] ?? '') === 'invalida'): ?><div class="message message-error" role="alert">Informe uma nota de 1 a 5 e até 1000 caracteres.</div><?php endif; ?>
                    <?php if (usuarioAutenticado()): ?>
                        <form class="review-form" method="post" action="admin/controllers/avaliar_game.php">
                            <input type="hidden" name="csrf_token" value="<?= h(gerarTokenCsrf()) ?>">
                            <input type="hidden" name="id" value="<?= (int) $game['id'] ?>">
                            <input type="hidden" name="acao" value="salvar">
                            <label for="nota">Sua nota</label>
                            <select id="nota" name="nota" required>
                                <?php for ($nota = 1; $nota <= 5; $nota++): ?>
                                    <option value="<?= $nota ?>" <?= (int) ($minhaAvaliacao['nota'] ?? 5) === $nota ? 'selected' : '' ?>><?= $nota ?> estrela<?= $nota > 1 ? 's' : '' ?></option>
                                <?php endfor; ?>
                            </select>
                            <label for="comentario">Seu comentário (opcional)</label>
                            <textarea id="comentario" name="comentario" maxlength="1000"><?= h($minhaAvaliacao['comentario'] ?? '') ?></textarea>
                            <button type="submit"><?= $minhaAvaliacao ? 'Atualizar avaliação' : 'Publicar avaliação' ?></button>
                        </form>
                        <?php if ($minhaAvaliacao): ?>
                            <form method="post" action="admin/controllers/avaliar_game.php">
                                <input type="hidden" name="csrf_token" value="<?= h(gerarTokenCsrf()) ?>">
                                <input type="hidden" name="id" value="<?= (int) $game['id'] ?>">
                                <input type="hidden" name="acao" value="remover">
                                <button class="button button-secondary" type="submit">Excluir minha avaliação</button>
                            </form>
                        <?php endif; ?>
                    <?php else: ?>
                        <p><a href="login.php?redirect=<?= rawurlencode('game.php?id=' . (int) $game['id']) ?>">Entre na sua conta</a> para avaliar este jogo.</p>
                    <?php endif; ?>
                    <?php foreach ($avaliacoes as $avaliacao): ?>
                        <article class="review-item">
                            <strong><?= h($avaliacao['nome']) ?></strong>
                            <span><?= (int) $avaliacao['nota'] ?> / 5</span>
                            <?php if ($avaliacao['comentario'] !== ''): ?><p><?= nl2br(h($avaliacao['comentario'])) ?></p><?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                    <?php if (!$avaliacoes): ?><p>Seja a primeira pessoa a avaliar este jogo.</p><?php endif; ?>
                </section>
            <?php endif; ?>
        </div>
    </main>

    <footer class="site-footer">
        <div class="container">PlayStation. Feito para quem gosta de boas histórias.</div>
    </footer>
</body>
</html>
