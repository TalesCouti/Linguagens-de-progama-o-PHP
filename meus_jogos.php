<?php
require_once __DIR__ . '/admin/config/autenticacao.php';
require_once __DIR__ . '/admin/models/games.php';
require_once __DIR__ . '/admin/config/imagens.php';

iniciarSessaoSegura();
exigirLogin('login.php?redirect=meus_jogos.php');

$gameModel = new Game();
$compras = $gameModel->listarComprasDoUsuario((int) $_SESSION['usuario_id']);

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus jogos | PlayStation Store</title>
    <link rel="icon" type="image/png" href="admin/public/imagens/logo.png">
    <link rel="stylesheet" href="admin/public/css/style.css">
</head>
<body class="storefront-page">
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
                <a class="nav-link nav-active" href="meus_jogos.php" aria-current="page">Meus jogos</a>
                <?php if (usuarioAdministradorAutenticado()): ?>
                    <a class="nav-link" href="admin/views/ver_games.php">Painel</a>
                <?php endif; ?>
                <a class="nav-link nav-primary" href="logout.php">Sair</a>
            </div>
        </nav>
    </header>

    <main id="conteudo" class="page">
        <div class="container">
            <div class="page-header">
                <div>
                    <p class="eyebrow">Sua biblioteca</p>
                    <h1 class="page-title">Meus jogos</h1>
                    <p class="page-intro">Aqui ficam todas as aventuras compradas por você.</p>
                </div>
                <a class="button button-secondary" href="index.php#catalogo">Explorar catálogo</a>
            </div>

            <?php if ($compras): ?>
                <div class="game-grid purchased-games">
                    <?php foreach ($compras as $compra): ?>
                        <a class="game-card" href="game.php?id=<?= (int) $compra['id'] ?>">
                            <span class="game-card-media">
                                <img class="game-cover" src="admin/public/imagens/<?= rawurlencode(imagemGameOuPlaceholder($compra['imagem'] ?? '')) ?>" alt="Capa de <?= h($compra['titulo']) ?>" loading="lazy" width="638" height="480">
                            </span>
                            <span class="game-card-body">
                                <span>
                                    <span class="game-category"><?= h($compra['categoria'] ?: 'Sem categoria') ?></span>
                                    <strong class="game-card-title"><?= h($compra['titulo']) ?></strong>
                                    <span class="game-card-description">Comprado em <?= date('d/m/Y', strtotime($compra['comprado_em'])) ?></span>
                                </span>
                                <span class="game-card-footer">
                                    <strong class="price">R$ <?= number_format((float) $compra['preco'], 2, ',', '.') ?></strong>
                                    <span class="game-card-link">Ver jogo</span>
                                </span>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <p class="eyebrow">Biblioteca vazia</p>
                    <h2>Você ainda não comprou nenhum jogo</h2>
                    <p>Explore o catálogo e escolha a próxima aventura para sua biblioteca.</p>
                    <a class="button" href="index.php#catalogo">Ver catálogo</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="site-footer"><div class="container">PlayStation. Feito para quem gosta de boas histórias.</div></footer>
</body>
</html>
