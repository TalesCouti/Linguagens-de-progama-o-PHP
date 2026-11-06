<?php
require_once __DIR__ . '/admin/config/autenticacao.php';
require_once __DIR__ . '/admin/models/usuarios.php';
iniciarSessaoSegura();
if (usuarioAutenticado()) { header('Location: index.php'); exit; }
$erro = '';
$nome = trim((string) ($_POST['nome'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$telefone = trim((string) ($_POST['telefone'] ?? ''));
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? null;
    if (!tokenCsrfValido(is_string($token) ? $token : null)) {
        $erro = 'A sessão do formulário expirou. Tente novamente.';
    } else {
        try {
            if ((new Usuario())->cadastrarUsuario($nome, $email, (string) ($_POST['senha'] ?? ''), 'user', $telefone)) {
                header('Location: login.php?cadastro=sucesso');
                exit;
            }
            $erro = 'Confira os dados. Use uma senha com ao menos 8 caracteres.';
        } catch (PDOException $e) {
            $erro = $e->getCode() === '23000' ? 'Este e-mail já está cadastrado.' : 'Não foi possível criar a conta.';
        }
    }
}
function h(string $v): string { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Criar conta | PlayStation Store</title><link rel="stylesheet" href="admin/public/css/style.css"></head>
<body><a class="skip-link" href="#conteudo">Ir para o conteúdo</a><main class="auth-page" id="conteudo"><div class="auth-layout"><section><a class="brand" href="index.php">PlayStation Store</a><h1 class="auth-title">Crie sua conta.</h1><p class="auth-copy">Salve jogos, monte sua biblioteca e compartilhe avaliações.</p></section>
<form class="auth-card" method="post"><h2>Cadastro</h2><?php if ($erro !== ''): ?><div class="message message-error" role="alert"><?= h($erro) ?></div><?php endif; ?><input type="hidden" name="csrf_token" value="<?= h(gerarTokenCsrf()) ?>">
<div class="field"><label for="nome">Nome</label><input id="nome" name="nome" maxlength="100" value="<?= h($nome) ?>" required></div>
<div class="field"><label for="email">E-mail</label><input id="email" type="email" name="email" maxlength="150" value="<?= h($email) ?>" required></div>
<div class="field"><label for="telefone">Telefone</label><input id="telefone" type="tel" name="telefone" maxlength="20" value="<?= h($telefone) ?>" required></div>
<div class="field"><label for="senha">Senha</label><input id="senha" type="password" name="senha" minlength="8" autocomplete="new-password" required></div>
<button type="submit">Criar conta</button><p>Já tem conta? <a href="login.php">Entrar</a></p></form></div></main></body></html>
