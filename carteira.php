<?php
require_once __DIR__ . '/admin/config/autenticacao.php';
require_once __DIR__ . '/admin/models/carteiras.php';
require_once __DIR__ . '/admin/config/navegacao.php';

exigirLogin('login.php?redirect=carteira.php');
$carteira = new Carteira();
$usuarioId = (int) $_SESSION['usuario_id'];
$saldo = $carteira->consultarSaldo($usuarioId);
$movimentacoes = $carteira->listarMovimentacoes($usuarioId);
$jogoId = filter_input(INPUT_GET, 'jogo', FILTER_VALIDATE_INT);
function h(string $valor): string { return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha carteira | PlayStation Store</title>
    <link rel="icon" type="image/png" href="admin/public/imagens/logo.png">
    <link rel="stylesheet" href="admin/public/css/style.css">
</head>
<body class="storefront-page">
    <a class="skip-link" href="#conteudo">Ir para o conteúdo</a>
    <div class="sony-ribbon" aria-hidden="true"><span>SONY</span></div>
    <header class="site-header"><nav class="nav" aria-label="Navegação principal">
        <a class="brand store-brand" href="index.php"><span class="brand-mark"><img src="admin/public/imagens/logo.png" alt=""></span><span>PlayStation <strong>Store</strong></span></a>
        <div class="nav-links"><a class="nav-link" href="index.php#catalogo">Catálogo</a><a class="nav-link" href="meus_jogos.php">Meus jogos</a><a class="nav-link" href="wishlist.php">Wishlist</a><?= linkCarteiraComSaldo($saldo, true) ?><a class="nav-link nav-primary" href="logout.php">Sair</a></div>
    </nav></header>
    <main class="container page" id="conteudo">
        <header class="page-header"><div><p class="eyebrow">Sua conta</p><h1 class="page-title">Minha carteira</h1><p class="page-intro">Adicione saldo e acompanhe suas movimentações.</p></div>
            <?php if ($jogoId && $jogoId > 0): ?><a class="button button-secondary" href="game.php?id=<?= $jogoId ?>">Voltar ao jogo</a><?php else: ?><a class="button button-secondary" href="index.php#catalogo">Explorar catálogo</a><?php endif; ?>
        </header>
        <?php if (($_GET['recarga'] ?? '') === 'sucesso'): ?><div class="message" role="status">Saldo adicionado à carteira.</div><?php endif; ?>
        <?php if (($_GET['recarga'] ?? '') === 'invalida'): ?><div class="message message-error" role="alert">Informe um valor entre R$ 0,01 e R$ 10.000,00.</div><?php endif; ?>
        <div class="wallet-layout">
            <section class="wallet-panel" aria-labelledby="saldo-titulo">
                <h2 id="saldo-titulo">Saldo disponível</h2>
                <strong class="wallet-balance">R$ <?= number_format((float) $saldo, 2, ',', '.') ?></strong>
                <form class="wallet-form" method="post" action="admin/controllers/adicionar_saldo.php">
                    <input type="hidden" name="csrf_token" value="<?= h(gerarTokenCsrf()) ?>">
                    <?php if ($jogoId && $jogoId > 0): ?><input type="hidden" name="jogo" value="<?= $jogoId ?>"><?php endif; ?>
                    <label for="valor">Valor para adicionar</label>
                    <input id="valor" type="number" name="valor" min="0.01" max="10000" step="0.01" placeholder="0,00" required>
                    <button type="submit">Adicionar saldo</button>
                </form>
            </section>
            <section class="wallet-panel" aria-labelledby="historico-titulo">
                <h2 id="historico-titulo">Movimentações recentes</h2>
                <?php if ($movimentacoes): ?>
                    <ul class="wallet-history">
                        <?php foreach ($movimentacoes as $movimento): ?>
                            <li><div><strong><?= h($movimento['descricao']) ?></strong><time datetime="<?= h($movimento['criado_em']) ?>"><?= date('d/m/Y H:i', strtotime($movimento['criado_em'])) ?></time></div><span class="<?= $movimento['tipo'] === 'compra' ? 'wallet-debit' : 'wallet-credit' ?>"><?= $movimento['tipo'] === 'compra' ? '−' : '+' ?> R$ <?= number_format((float) $movimento['valor'], 2, ',', '.') ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?><p class="page-intro">Nenhuma movimentação até agora.</p><?php endif; ?>
            </section>
        </div>
    </main>
</body>
</html>
