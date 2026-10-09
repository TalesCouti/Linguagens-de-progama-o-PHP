<?php
require_once __DIR__ . '/admin/config/conexao.php';
require_once __DIR__ . '/admin/config/autenticacao.php';

iniciarSessaoSegura();

function destinoLoginSeguro(mixed $destino): ?string
{
    if (!is_string($destino)) {
        return null;
    }

    return preg_match('/\Agame\.php\?id=[1-9]\d*\z/D', $destino) === 1 ? $destino : null;
}

$destinoAposLogin = destinoLoginSeguro($_POST['redirect'] ?? $_GET['redirect'] ?? null);

if (usuarioAutenticado()) {
    header('Location: ' . ($destinoAposLogin ?? (usuarioAdministradorAutenticado() ? 'admin/views/ver_games.php' : 'index.php')));
    exit;
}

$mensagem = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    $senhaInformada = (string) ($_POST['senha'] ?? '');
    $token = $_POST['csrf_token'] ?? null;

    if (
        tokenCsrfValido(is_string($token) ? $token : null)
        && filter_var($email, FILTER_VALIDATE_EMAIL)
        && $senhaInformada !== ''
        && autenticarUsuario($pdo, $email, $senhaInformada)
    ) {
        header('Location: ' . ($destinoAposLogin ?? (usuarioAdministradorAutenticado() ? 'admin/views/ver_games.php' : 'index.php')));
        exit;
    }

    $mensagem = 'E-mail ou senha inválidos.';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Acesso à sua conta da PlayStation.">
    <title>Entrar | PlayStation</title>
    <link rel="icon" type="image/png" href="admin/public/imagens/logo.png">
    <link rel="stylesheet" href="admin/public/css/style.css">
</head>
<body>
    <a class="skip-link" href="#conteudo">Ir para o conteúdo</a>
    <main class="auth-page" id="conteudo">
        <div class="auth-layout">
            <section>
                <a class="brand" href="index.php"><span class="brand-mark"><img src="admin/public/imagens/logo.png" alt=""></span> PlayStation</a>
                <h1 class="auth-title" style="margin-top: 48px;">Bem-vindo de volta.</h1>
                <p class="auth-copy">Entre na sua conta. Administradores também têm acesso ao gerenciamento da loja.</p>
            </section>

            <form class="auth-card" method="post" action="login.php">
                <?php if ($mensagem !== ''): ?>
                    <div class="message message-error" role="alert"><?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?></div>
                <?php elseif (isset($_GET['logout'])): ?>
                    <div class="message" role="status">Sessão encerrada com sucesso.</div>
                <?php endif; ?>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(gerarTokenCsrf(), ENT_QUOTES, 'UTF-8') ?>">
                <?php if ($destinoAposLogin !== null): ?>
                    <input type="hidden" name="redirect" value="<?= htmlspecialchars($destinoAposLogin, ENT_QUOTES, 'UTF-8') ?>">
                <?php endif; ?>
                <div class="field">
                    <label for="email">E-mail</label>
                    <input id="email" name="email" type="email" autocomplete="username" placeholder="voce@exemplo.com" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>" required autofocus>
                </div>
                <div class="field">
                    <label for="senha-login">Senha</label>
                    <input id="senha-login" name="senha" type="password" autocomplete="current-password" placeholder="Digite sua senha" required>
                </div>
                <button type="submit">Entrar</button>
                <p class="field-help" style="margin-top: 18px; text-align: center;">
                    <a href="index.php">Voltar para a loja</a>
                </p>
            </form>
        </div>
    </main>
</body>
</html>
