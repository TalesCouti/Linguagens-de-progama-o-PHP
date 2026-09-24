<?php
require_once '../models/games.php';
$game = new Game();

$nome = $_GET['buscar'] ?? '';
$games = $nome ? $game->buscarPorNome($nome) : $game->listarGames();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Jogos</title>
    <link rel="stylesheet" href="../public/css/style.css">

    <style>
        .top-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .top-bar form {
            display: flex;
            gap: 10px;
        }

        .actions a {
            margin: 0 5px;
            text-decoration: none;
            font-size: 20px;
        }
    </style>

    <script>
        function confirmarExclusao(titulo, id) {
            if (confirm("Tem certeza que deseja excluir o jogo '" + titulo + "'?")) {
                window.location.href = '../controllers/deletar_game.php?id=' + id;
            }
        }
    </script>
</head>

<body>

    <h2>Lista de Jogos</h2>

    <div class="top-bar">
        <form method="GET" action="">
            <input type="text" name="buscar" placeholder="Buscar por nome..."
                   value="<?= htmlspecialchars($nome) ?>">
            <button type="submit">Buscar</button>
        </form>

        <a href="cadastrar_game.php">+ Cadastrar Novo Jogo</a>
    </div>

    <table>
        <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Descrição</th>
            <th>Preço</th>
            <th>Estoque</th>
            <th>Categoria</th>
            <th>Imagem</th>
            <th>Ações</th>
        </tr>

        <?php if (count($games) > 0): ?>
            <?php foreach ($games as $g): ?>
                <tr>
                    <td><?= $g['id'] ?></td>
                    <td><?= $g['titulo'] ?></td>
                    <td><?= $g['descricao'] ?></td>
                    <td>R$ <?= number_format($g['preco'], 2, ',', '.') ?></td>
                    <td><?= $g['estoque'] ?></td>
                    <td><?= $g['categoria'] ?></td>
                    <td><?= $g['imagem'] ?></td>

                    <td class="actions">
                        <a href="editar_game.php?id=<?= $g['id'] ?>">&#9998;</a>

                        <a href="#"
                           onclick="confirmarExclusao('<?= addslashes($g['titulo']) ?>',
                           <?= $g['id'] ?>)">&#10060;</a>
                    </td>
                </tr>
            <?php endforeach; ?>

        <?php else: ?>
            <tr>
                <td colspan="8">Nenhum jogo encontrado.</td>
            </tr>
        <?php endif; ?>
    </table>

</body>
</html>