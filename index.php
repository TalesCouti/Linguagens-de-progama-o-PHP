<?php
require_once __DIR__ . '/admin/config/autenticacao.php';
require_once __DIR__ . '/admin/models/games.php';
require_once __DIR__ . '/admin/config/imagens.php';

iniciarSessaoSegura();

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
    <meta name="description" content="Descubra jogos para viver novas histórias na PlayStation.">
    <title>PlayStation | Sua próxima aventura</title>
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
                <a class="nav-link" href="#catalogo">Catálogo</a>
                <?php if (usuarioAdministradorAutenticado()): ?>
                    <a class="nav-link" href="admin/views/ver_games.php">Painel</a>
                    <a class="nav-link nav-primary" href="logout.php">Sair</a>
                <?php elseif (usuarioAutenticado()): ?>
                    <a class="nav-link nav-primary" href="logout.php">Sair</a>
                <?php else: ?>
                    <a class="nav-link nav-primary" href="login.php">Entrar</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main id="conteudo">
        <?php if (isset($_GET['acesso'])): ?>
            <div class="container access-message">
                <div class="message message-error" role="alert">Esta área está disponível somente para administradores.</div>
            </div>
        <?php endif; ?>
        <section class="storefront-hero" aria-labelledby="destaque-titulo">
            <div class="container store-hero-stage">
                <img class="store-hero-art" src="admin/public/imagens/TheLastofUs.png" width="554" height="554" alt="Ellie diante de uma cidade tomada pela natureza em The Last of Us Part II Remastered">
                <div class="store-hero-content">
                    <div class="store-hero-copy">
                        <p class="store-kicker">PlayStation Studios</p>
                        <h1 id="destaque-titulo">The Last of Us Part II Remastered</h1>
                        <p>Uma jornada intensa de sobrevivência, escolhas e consequências em uma Seattle tomada pela natureza.</p>
                        <a class="button store-hero-button" href="#catalogo">Explorar catálogo</a>
                    </div>
                </div>
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
                            <?php $disponivel = (int) $game['estoque'] > 0; ?>
                            <a class="game-card" href="game.php?id=<?= (int) $game['id'] ?>" aria-label="Ver detalhes de <?= h($game['titulo']) ?>">
                                <span class="game-card-media">
                                    <img class="game-cover" src="admin/public/imagens/<?= rawurlencode(imagemGameOuPlaceholder($game['imagem'] ?? '')) ?>" alt="Capa de <?= h($game['titulo']) ?>" loading="lazy" width="638" height="480">
                                    <span class="game-availability<?= $disponivel ? '' : ' sold-out' ?>"><?= $disponivel ? 'Disponível' : 'Esgotado' ?></span>
                                </span>
                                <span class="game-card-body">
                                    <span class="game-category"><?= h($game['categoria'] ?: 'Sem categoria') ?></span>
                                    <strong class="game-card-title"><?= h($game['titulo']) ?></strong>
                                    <span class="game-card-description"><?= h($game['descricao'] ?: 'Uma nova experiência espera por você.') ?></span>
                                    <span class="game-card-footer">
                                        <strong class="price">R$ <?= number_format((float) $game['preco'], 2, ',', '.') ?></strong>
                                        <span class="game-card-link">Ver detalhes</span>
                                    </span>
                                </span>
                            </a>
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
        <div class="container">PlayStation. Feito para quem gosta de boas histórias.</div>
    </footer>
</body>
</html>
