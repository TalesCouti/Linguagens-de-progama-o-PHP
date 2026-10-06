<?php
require_once __DIR__ . '/admin/models/games.php';
require_once __DIR__ . '/admin/config/imagens.php';

$gameModel = new Game();
$games = $gameModel->listarGames();

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
    <meta name="description" content="Descubra jogos para viver novas histórias na Loja Games.">
    <title>Loja Games | Sua próxima aventura</title>
    <link rel="stylesheet" href="admin/public/css/style.css">
</head>
<body>
    <a class="skip-link" href="#conteudo">Ir para o conteúdo</a>
    <header class="site-header">
        <nav class="nav" aria-label="Navegação principal">
            <a class="brand" href="index.php"><span class="brand-mark">LG</span> Loja Games</a>
            <div class="nav-links">
                <a class="nav-link" href="#catalogo">Catálogo</a>
                <a class="nav-link nav-primary" href="login.php">Painel</a>
            </div>
        </nav>
    </header>

    <main id="conteudo">
        <section class="container hero">
            <div class="hero-copy-block">
                <p class="eyebrow">Jogue do seu jeito</p>
                <h1>Sua próxima aventura.</h1>
                <p class="hero-copy">Grandes mundos, escolhas inesquecíveis e jogos selecionados para todos os estilos.</p>
                <div class="actions">
                    <a class="button" href="#catalogo">Ver catálogo</a>
                    <a class="button button-secondary" href="testar_conexao.php">Status da loja</a>
                </div>
            </div>
            <div class="hero-media">
                <img src="admin/public/imagens/hero-controller.png" width="1536" height="1024" alt="Controle de videogame preto iluminado por uma luz verde suave">
            </div>
        </section>

        <section class="section" id="catalogo">
            <div class="container">
                <div class="section-heading">
                    <h2>Escolha seu próximo jogo</h2>
                    <p>Explore o catálogo disponível e encontre uma nova aventura.</p>
                </div>

                <?php if ($games): ?>
                    <div class="game-grid">
                        <?php foreach ($games as $game): ?>
                            <article class="game-card">
                                <img class="game-cover" src="admin/public/imagens/<?= rawurlencode(imagemGameOuPlaceholder($game['imagem'] ?? '')) ?>" alt="Capa de <?= h($game['titulo']) ?>" loading="lazy" width="638" height="480">
                                <div class="game-card-body">
                                    <div>
                                        <h3><?= h($game['titulo']) ?></h3>
                                        <p><?= h($game['descricao'] ?: 'Uma nova experiência espera por você.') ?></p>
                                        <div class="game-meta">
                                            <span><?= h($game['categoria'] ?: 'Sem categoria') ?></span>
                                            <span><?= (int) $game['estoque'] ?> em estoque</span>
                                        </div>
                                    </div>
                                    <strong class="price">R$ <?= number_format((float) $game['preco'], 2, ',', '.') ?></strong>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <h2>Catálogo em preparação</h2>
                        <p>Os jogos aparecerão aqui assim que forem cadastrados.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="container">Loja Games. Feito para quem gosta de boas histórias.</div>
    </footer>
</body>
</html>
