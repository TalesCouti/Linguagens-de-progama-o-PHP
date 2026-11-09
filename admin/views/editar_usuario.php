<?php
require_once __DIR__ . '/../config/autenticacao.php';
exigirAdministrador('../../login.php');
require_once __DIR__ . '/../models/usuarios.php';

$modelo = new Usuario();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
$usuario = $modelo->buscarUsuarioPorId($id);
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!tokenCsrfValido(is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : null)) {
            throw new RuntimeException('A sessão do formulário expirou.');
        }
        $tipo = (string) ($_POST['tipo'] ?? 'user');
        if ($id === (int) $_SESSION['usuario_id'] && $tipo !== 'admin') {
            throw new RuntimeException('Você não pode remover seu próprio acesso administrativo.');
        }
        if (!$modelo->editarUsuario($id, (string) ($_POST['nome'] ?? ''), (string) ($_POST['email'] ?? ''), $tipo, (string) ($_POST['telefone'] ?? ''), (string) ($_POST['senha'] ?? ''))) {
            throw new RuntimeException('Confira os dados. A nova senha deve ter pelo menos 8 caracteres.');
        }
        if ($id === (int) $_SESSION['usuario_id']) {
            $_SESSION['usuario_nome'] = trim((string) $_POST['nome']);
            $_SESSION['usuario_email'] = strtolower(trim((string) $_POST['email']));
        }
        header('Location: ver_usuarios.php?editado=1');
        exit;
    } catch (PDOException $e) {
        $erro = $e->getCode() === '23000' ? 'Este e-mail já está em uso.' : 'Não foi possível atualizar o usuário.';
    } catch (RuntimeException $e) {
        $erro = $e->getMessage();
    }
}
function h(string $valor): string { return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Editar usuário | PlayStation</title><link rel="stylesheet" href="../public/css/style.css"></head>
<body><a class="skip-link" href="#conteudo">Ir para o conteúdo</a><header class="site-header"><nav class="nav" aria-label="Navegação administrativa"><a class="brand" href="../../index.php">PlayStation Store</a><div class="nav-links"><a class="nav-link" href="ver_usuarios.php">Usuários</a><a class="nav-link nav-primary" href="../../logout.php">Sair</a></div></nav></header>
<main class="container page" id="conteudo"><div class="form-layout"><section><h1 class="page-title">Editar usuário</h1><p class="page-intro">Atualize os dados da conta.</p></section>
<?php if (!$usuario): ?><div class="empty-state">Usuário não encontrado. <a href="ver_usuarios.php">Voltar</a></div><?php else: ?>
<section class="form-card"><?php if ($erro !== ''): ?><div class="message message-error" role="alert"><?= h($erro) ?></div><?php endif; ?>
<form method="post"><input type="hidden" name="csrf_token" value="<?= h(gerarTokenCsrf()) ?>"><input type="hidden" name="id" value="<?= (int) $usuario['id'] ?>"><div class="form-grid">
<div class="field field-full"><label for="nome">Nome</label><input id="nome" name="nome" maxlength="100" value="<?= h($usuario['nome']) ?>" required></div>
<div class="field field-full"><label for="email">E-mail</label><input id="email" type="email" name="email" maxlength="150" value="<?= h($usuario['email']) ?>" required></div>
<div class="field"><label for="telefone">Telefone</label><input id="telefone" type="tel" name="telefone" maxlength="20" value="<?= h($usuario['telefone'] ?? '') ?>"></div>
<div class="field"><label for="tipo">Tipo</label><select id="tipo" name="tipo"><option value="user" <?= $usuario['tipo'] === 'user' ? 'selected' : '' ?>>Usuário</option><option value="admin" <?= $usuario['tipo'] === 'admin' ? 'selected' : '' ?>>Administrador</option></select></div>
<div class="field field-full"><label for="senha">Nova senha (opcional)</label><input id="senha" type="password" name="senha" minlength="8" autocomplete="new-password"></div>
</div><div class="form-actions"><button type="submit">Salvar alterações</button><a class="button button-secondary" href="ver_usuarios.php">Cancelar</a></div></form></section><?php endif; ?></div></main></body></html>
