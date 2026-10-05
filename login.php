<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Acesso à área administrativa da Loja Games.">
    <title>Entrar | Loja Games</title>
    <link rel="stylesheet" href="admin/public/css/style.css">
</head>
<body>
    <a class="skip-link" href="#conteudo">Ir para o conteúdo</a>
    <main class="auth-page" id="conteudo">
        <div class="auth-layout">
            <section>
                <a class="brand" href="index.php"><span class="brand-mark">LG</span> Loja Games</a>
                <h1 class="auth-title" style="margin-top: 48px;">Bem-vindo de volta.</h1>
                <p class="auth-copy">Acesse o painel para cadastrar, editar e organizar o catálogo.</p>
            </section>

            <form class="auth-card" method="post" action="admin/views/ver_games.php">
                <div class="field">
                    <label for="email">E-mail</label>
                    <input id="email" name="email" type="email" autocomplete="email" placeholder="voce@exemplo.com" required>
                </div>
                <div class="field">
                    <label for="senha-login">Senha</label>
                    <input id="senha-login" name="senha" type="password" autocomplete="current-password" placeholder="Digite sua senha" required>
                </div>
                <button type="submit">Entrar no painel</button>
                <p class="field-help" style="margin-top: 18px; text-align: center;">
                    <a href="index.php">Voltar para a loja</a>
                </p>
            </form>
        </div>
    </main>
</body>
</html>
