<?php
require_once __DIR__ . '/admin/config/autenticacao.php';
require_once __DIR__ . '/admin/models/interacoes.php';
require_once __DIR__ . '/admin/config/imagens.php';
exigirLogin('login.php?redirect=wishlist.php');
$jogos = (new Interacoes())->listarWishlist((int) $_SESSION['usuario_id']);
function h(string $valor): string { return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist | PlayStation Store</title>
    <link rel="stylesheet" href="admin/public/css/style.css">
</head>
<body class="storefront-page">
    <a class="skip-link" href="#conteudo">Ir para o conteúdo</a>
    <header class="site-header"><nav class="nav" aria-label="Navegação principal">
        <a class="brand store-brand" href="index.php"><span class="brand-mark"><img src="admin/public/imagens/logo.png" alt=""></span><span>PlayStation <strong>Store</strong></span></a>
        <div class="nav-links"><a class="nav-link" href="index.php#catalogo">Catálogo</a><a class="nav-link" href="meus_jogos.php">Meus jogos</a><a class="nav-link nav-active" aria-current="page" href="wishlist.php">Wishlist</a><a class="nav-link nav-wallet" href="carteira.php">Carteira</a><a class="nav-link nav-primary" href="logout.php">Sair</a></div>
    </nav></header>
    <main class="container page" id="conteudo">
        <header class="page-header"><div><p class="eyebrow">Seus favoritos</p><h1 class="page-title">Wishlist</h1><p class="page-intro">Jogos que você quer acompanhar.</p></div><a class="button button-secondary" href="index.php#catalogo">Explorar catálogo</a></header>
        <?php if ($jogos): ?>
            <div class="game-grid purchased-games">
                <?php foreach ($jogos as $jogo): ?>
                    <article class="game-card">
                        <a href="game.php?id=<?= (int) $jogo['id'] ?>"><img class="game-cover" src="admin/public/imagens/<?= rawurlencode(imagemGameOuPlaceholder($jogo['imagem'] ?? '')) ?>" alt="Capa de <?= h($jogo['titulo']) ?>" loading="lazy"></a>
                        <div class="game-card-body"><div><span class="game-category"><?= h($jogo['categoria'] ?: 'Sem categoria') ?></span><strong class="game-card-title"><?= h($jogo['titulo']) ?></strong><p class="game-card-description">R$ <?= number_format((float) $jogo['preco'], 2, ',', '.') ?></p></div>
                            <div class="wishlist-card-actions"><a class="button" href="game.php?id=<?= (int) $jogo['id'] ?>">Ver jogo</a><form method="post" action="admin/controllers/wishlist.php"><input type="hidden" name="csrf_token" value="<?= h(gerarTokenCsrf()) ?>"><input type="hidden" name="id" value="<?= (int) $jogo['id'] ?>"><input type="hidden" name="acao" value="remover"><input type="hidden" name="origem" value="wishlist"><button class="button button-secondary" type="submit">Remover</button></form></div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state"><h2>Sua wishlist está vazia</h2><p>Salve jogos pela página de detalhes para encontrá-los aqui.</p><a class="button" href="index.php#catalogo">Ver catálogo</a></div>
        <?php endif; ?>
    </main>
</body>
</html>
