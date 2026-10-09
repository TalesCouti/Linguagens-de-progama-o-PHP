<?php
require_once __DIR__ . '/../config/autenticacao.php';
exigirAdministrador('../../login.php');

require_once '../models/games.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Método não permitido.';
    exit;
}

$token = $_POST['csrf_token'] ?? null;
if (!tokenCsrfValido(is_string($token) ? $token : null)) {
    http_response_code(403);
    echo 'A sessão do formulário expirou.';
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id || $id <= 0) {
    http_response_code(400);
    echo 'ID do jogo inválido.';
    exit;
}

$game = new Game(['id' => $id]);
if (!$game->deletarGame()) {
    http_response_code(500);
    echo 'Erro ao excluir o jogo.';
    exit;
}

header('Location: ../views/ver_games.php?deletado=1');
exit;
