<?php
require_once '../models/games.php';

if (isset($_GET['id'])) {
    $game = new Game(['id' => $_GET['id']]);
    if ($game->deletarGame()) {
        header('Location: ../views/ver_games.php?deletado=1');
        exit;
    } else {
        echo "Erro ao excluir o jogo.";
    }
} else {
    echo "ID do jogo não fornecido.";
}
?>
