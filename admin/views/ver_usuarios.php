<?php
require_once __DIR__ . '/../config/autenticacao.php';
exigirAdministrador('../../login.php');

require_once __DIR__ . '/../models/usuarios.php';

$usuarioModel = new Usuario();
$usuarios = $usuarioModel->listarUsuarios();

function h(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar usuários | PlayStation</title>
    <link rel="icon" type="image/png" href="../public/imagens/logo.png">
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body>
    <a class="skip-link" href="#conteudo">Ir para o conteúdo</a>
    <header class="site-header">
        <nav class="nav" aria-label="Navegação administrativa">
            <a class="brand" href="../../index.php"><span class="brand-mark"><img src="../public/imagens/logo.png" alt=""></span> PlayStation</a>
            <div class="nav-links">
                <a class="nav-link" href="../../index.php">Ver loja</a>
                <a class="nav-link" href="ver_games.php">Jogos</a>
                <a class="nav-link" aria-current="page" href="ver_usuarios.php">Usuários</a>
                <a class="nav-link nav-primary" href="../../logout.php">Sair</a>
            </div>
        </nav>
    </header>

    <main class="container page" id="conteudo">
        <header class="page-header">
            <div>
                <h1 class="page-title">Usuários</h1>
                <p class="page-intro">Consulte as contas cadastradas e seus níveis de acesso.</p>
            </div>
            <a class="button" href="cadastrar_usuario.php">Cadastrar usuário</a>
        </header>

        <?php if (isset($_GET['cadastrado'])): ?>
            <div class="message" role="status">Usuário cadastrado com sucesso.</div>
        <?php endif; ?>

        <div class="table-shell">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Tipo</th>
                        <th>Cadastrado em</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><div class="table-title"><?= h($usuario['nome']) ?></div></td>
                            <td><?= h($usuario['email']) ?></td>
                            <td><span class="category"><?= $usuario['tipo'] === 'admin' ? 'Administrador' : 'Usuário' ?></span></td>
                            <td><?= h(date('d/m/Y H:i', strtotime($usuario['created_at']))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
