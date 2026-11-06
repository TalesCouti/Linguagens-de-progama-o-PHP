<?php
require_once __DIR__ . '/../config/autenticacao.php';
require_once __DIR__ . '/../models/games.php';
require_once __DIR__ . '/../models/interacoes.php';

exigirLogin('../../login.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit;
}
if (!tokenCsrfValido(is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : null)) {
    http_response_code(403);
    exit('Solicitação inválida.');
}
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id || !(new Game())->buscarGamePorId($id)) {
    http_response_code(404);
    exit('Jogo não encontrado.');
}
$interacoes = new Interacoes();
if (($_POST['acao'] ?? '') === 'remover') {
    $interacoes->removerAvaliacao((int) $_SESSION['usuario_id'], $id);
} elseif (($_POST['acao'] ?? '') === 'salvar') {
    $nota = filter_input(INPUT_POST, 'nota', FILTER_VALIDATE_INT);
    $comentario = $_POST['comentario'] ?? '';
    if (!$nota || !is_string($comentario) || !$interacoes->salvarAvaliacao((int) $_SESSION['usuario_id'], $id, $nota, $comentario)) {
        header('Location: ../../game.php?id=' . $id . '&avaliacao=invalida#avaliacoes');
        exit;
    }
} else {
    http_response_code(400);
    exit('Ação inválida.');
}
header('Location: ../../game.php?id=' . $id . '&avaliacao=salva#avaliacoes');
exit;
