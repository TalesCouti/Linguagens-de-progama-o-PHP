<?php
require_once __DIR__ . '/../config/autenticacao.php';
require_once __DIR__ . '/../models/games.php';

exigirLogin('../../login.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Método não permitido.');
}

$token = $_POST['csrf_token'] ?? null;
if (!tokenCsrfValido(is_string($token) ? $token : null)) {
    http_response_code(403);
    exit('Não foi possível validar a solicitação.');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id || $id <= 0) {
    http_response_code(400);
    exit('Jogo inválido.');
}

$gameModel = new Game();
$comprado = $gameModel->comprarGame($id);
$resultado = $comprado ? 'sucesso' : 'sem_estoque';

header('Location: ../../game.php?id=' . $id . '&compra=' . $resultado);
exit;
