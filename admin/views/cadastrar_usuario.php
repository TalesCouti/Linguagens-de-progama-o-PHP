<?php
require_once __DIR__ . '/../config/autenticacao.php';
exigirAdministrador('../../login.php');

require_once __DIR__ . '/../models/usuarios.php';

$mensagem = '';
$nome = '';
$email = '';
$tipo = 'user';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim((string) ($_POST['nome'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $senhaInformada = (string) ($_POST['senha'] ?? '');
    $tipo = (string) ($_POST['tipo'] ?? 'user');
    $token = $_POST['csrf_token'] ?? null;

    try {
        if (!tokenCsrfValido(is_string($token) ? $token : null)) {
            throw new RuntimeException('A sessão do formulário expirou. Tente novamente.');
        }

        $usuarioModel = new Usuario();
        if (!$usuarioModel->cadastrarUsuario($nome, $email, $senhaInformada, $tipo)) {
            throw new RuntimeException('Preencha os dados corretamente. A senha deve ter pelo menos 8 caracteres.');
        }

        header('Location: ver_usuarios.php?cadastrado=1');
        exit;
    } catch (PDOException $e) {
        $mensagem = $e->getCode() === '23000'
            ? 'Já existe um usuário cadastrado com este e-mail.'
            : 'Não foi possível cadastrar o usuário.';
    } catch (RuntimeException $e) {
        $mensagem = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar usuário | PlayStation</title>
    <link rel="icon" type="image/png" href="../public/imagens/logo.png">
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body>
    <a class="skip-link" href="#conteudo">Ir para o conteúdo</a>
    <header class="site-header">
        <nav class="nav" aria-label="Navegação administrativa">
            <a class="brand" href="../../index.php"><span class="brand-mark"><img src="../public/imagens/logo.png" alt=""></span> PlayStation</a>
            <div class="nav-links">
                <a class="nav-link" href="ver_games.php">Jogos</a>
                <a class="nav-link" href="ver_usuarios.php">Usuários</a>
                <a class="nav-link nav-primary" href="../../logout.php">Sair</a>
            </div>
        </nav>
    </header>

    <main class="container page" id="conteudo">
        <div class="form-layout">
            <section>
                <h1 class="page-title">Novo usuário</h1>
                <p class="page-intro">Crie uma conta e defina se ela terá acesso comum ou administrativo.</p>
            </section>

            <section class="form-card">
                <?php if ($mensagem !== ''): ?>
                    <div class="message message-error" role="alert"><?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(gerarTokenCsrf(), ENT_QUOTES, 'UTF-8') ?>">
                    <div class="form-grid">
                        <div class="field field-full">
                            <label for="nome">Nome</label>
                            <input id="nome" type="text" name="nome" maxlength="100" value="<?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?>" required autofocus>
                        </div>
                        <div class="field field-full">
                            <label for="email">E-mail</label>
                            <input id="email" type="email" name="email" maxlength="150" autocomplete="username" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <div class="field">
                            <label for="senha">Senha</label>
                            <input id="senha" type="password" name="senha" minlength="8" autocomplete="new-password" required>
                            <p class="field-help">Use pelo menos 8 caracteres.</p>
                        </div>
                        <div class="field">
                            <label for="tipo">Tipo de usuário</label>
                            <select id="tipo" name="tipo" required>
                                <option value="user" <?= $tipo === 'user' ? 'selected' : '' ?>>Usuário</option>
                                <option value="admin" <?= $tipo === 'admin' ? 'selected' : '' ?>>Administrador</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit">Cadastrar usuário</button>
                        <a class="button button-secondary" href="ver_usuarios.php">Cancelar</a>
                    </div>
                </form>
            </section>
        </div>
    </main>
</body>
</html>
