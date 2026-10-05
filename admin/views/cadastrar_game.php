<?php
require_once '../models/games.php';

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imagemNome = $_FILES['imagem']['name'];
    $imagemTmp = $_FILES['imagem']['tmp_name'];
    $caminhoDestino = '../public/imagens/' . $imagemNome;

    if (move_uploaded_file($imagemTmp, $caminhoDestino)) {
        $novo = new Game([
            'titulo' => $_POST['titulo'],
            'descricao' => $_POST['descricao'],
            'preco' => $_POST['preco'],
            'estoque' => $_POST['estoque'],
            'categoria' => $_POST['categoria'],
            'imagem' => $imagemNome
        ]);

        if ($novo->cadastrarGame()) {
            $mensagem = 'sucesso';
        } else {
            $mensagem = 'Erro ao cadastrar no banco de dados.';
        }
    } else {
        $mensagem = 'Erro ao fazer upload da imagem.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Jogo</title>
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body>
    <a class="skip-link" href="#conteudo">Ir para o conteúdo</a>
    <header class="site-header">
        <nav class="nav" aria-label="Navegação administrativa">
            <a class="brand" href="../../index.php"><span class="brand-mark">LG</span> Loja Games</a>
            <div class="nav-links"><a class="nav-link" href="ver_games.php">Voltar ao catálogo</a></div>
        </nav>
    </header>

    <main class="container page" id="conteudo">
        <div class="form-layout">
            <section>
                <h1 class="page-title">Novo jogo</h1>
                <p class="page-intro">Preencha os dados para adicionar um título ao catálogo da loja.</p>
            </section>

            <section class="form-card">
                <?php if ($mensagem === 'sucesso'): ?>
                    <div class="message" role="status">Jogo cadastrado com sucesso.</div>
                <?php elseif ($mensagem !== ''): ?>
                    <div class="message message-error" role="alert"><?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="form-grid">
                        <div class="field field-full">
                            <label for="titulo">Título</label>
                            <input id="titulo" type="text" name="titulo" placeholder="Nome do jogo" required>
                        </div>
                        <div class="field field-full">
                            <label for="descricao">Descrição</label>
                            <textarea id="descricao" name="descricao" rows="4" placeholder="Conte um pouco sobre o jogo" required></textarea>
                        </div>
                        <div class="field">
                            <label for="preco">Preço</label>
                            <input id="preco" type="number" step="0.01" min="0" name="preco" placeholder="0,00" required>
                        </div>
                        <div class="field">
                            <label for="estoque">Estoque</label>
                            <input id="estoque" type="number" min="0" name="estoque" placeholder="0" required>
                        </div>
                        <div class="field field-full">
                            <label for="categoria">Categoria</label>
                            <input id="categoria" type="text" name="categoria" placeholder="Aventura, ação, estratégia..." required>
                        </div>
                        <div class="field field-full">
                            <label for="imagem">Imagem</label>
                            <input id="imagem" type="file" name="imagem" accept="image/*" required>
                            <p class="field-help">Use uma imagem horizontal ou quadrada em JPG, PNG ou WebP.</p>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit">Cadastrar jogo</button>
                        <a class="button button-secondary" href="ver_games.php">Cancelar</a>
                    </div>
                </form>
            </section>
        </div>
    </main>
</body>
</html>
