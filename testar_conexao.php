<?php
require_once 'admin/config/conexao.php'
//Query para buscar todos os games
#sql = "SELECT * FROM games";
#stmt = $pdo -> prepare ($sql);
$stmt -> execute();
$games = $stmt -> fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang ="pt-br">
<head>
    <meta charset = "UTF-8">
    <meta name= "viewport" content "width = device-width, inital-scale=1.0>
    <title>Lista de Jogos </title>

<link real= "stylesheet" href = "admin/public/css/style.css> <!-- CSS Externo -->
</head>
<body>
        <h2>Lista de Jogos</h2>
        <table>
            <tr>
                <th>ID</TH>
                <th>Titulo</TH>
                <th>Descrição</TH>
                <th>Preço</TH>
                <th>Categoria</TH>
                <th>Imagem (nome)</TH>
            </tr>
            <?php if (count($games) > 0) : ? >
                <?php foreach ($games as $game): ?>
                    <tr>
                        <td><? = $game['id']; ?> </td>
                        <td><? = $game['titulo']; ?> </td>
                        <td><? = $game['descrição']; ?> </td>
                        <td> R$ <?= number_format ($game['preco'], 2, ',', '.' ) : ? ></td>
                        <td><? = $game['estoque']; ?> </td>
                        <td><? = $game['categoria']; ?> </td>
                        <td><? = $game['imagem']; ?> </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan= "7" Nenhum jogo cadastrado.</td>
                </tr>
            <?php endif; ?>
        </table>
    </body>
    </html>