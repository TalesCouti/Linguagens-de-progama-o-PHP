<?php
require_once '../models/games.php';

$game = new Game();
$dados = null;

if (isset($_GET['id'])) {
    $dados = $game->buscarGamePorId($_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $editar = new Game([
        'id' => $_POST['id'],
        'titulo' => $_POST['titulo'],
        'descricao' => $_POST['descricao'],
        'preco' => $_POST['preco'],
        'estoque' => $_POST['estoque'],
        'categoria' => $_POST['categoria'],
        'imagem' => $_POST['imagem'] // temporariamente mantém a imagem atual
    ]);
    if ($editar->editarGame()) {
        echo "<script>alert('Jogo editado com sucesso!');
        window.location.href = 'ver_games.php';</script>";
        exit;
    } else {
        echo "Erro ao editar!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Jogo</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            background-color: #f4f4f4;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        label {
            font-weight: bold;
        }
        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        a {
            margin-left: 15px;
            text-decoration: none;
            color: #007bff;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h2>Editar Jogo</h2>

    <?php if ($dados): ?>
        <form method="POST">
            <input type="hidden" name="id" value="<?= $dados['id'] ?>">

            <label for="titulo">Título:</label>
            <input type="text" name="titulo" value="<?= $dados['titulo'] ?>" required>

            <label for="descricao">Descrição:</label>
            <textarea name="descricao" rows="4" required><?= $dados['descricao'] ?></textarea>

            <label for="preco">Preço:</label>
            <input type="number" step="0.01" name="preco" value="<?= $dados['preco'] ?>" required>

            <label for="estoque">Estoque:</label>
            <input type="number" name="estoque" value="<?= $dados['estoque'] ?>" required>

            <label for="categoria">Categoria:</label>
            <input type="text" name="categoria" value="<?= $dados['categoria'] ?>" required>

            <label>Imagem atual:</label>
            <p><?= $dados['imagem'] ?></p>
            <input type="hidden" name="imagem" value="<?= $dados['imagem'] ?>">

            <button type="submit">Salvar</button>
            <a href="ver_games.php">Cancelar</a>
        </form>
    <?php else: ?>
        <p>Jogo não encontrado.</p>
    <?php endif; ?>
</body>
</html>
