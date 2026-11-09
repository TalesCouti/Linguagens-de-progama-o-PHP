<?php
require_once __DIR__ . '/../config/autenticacao.php';
require_once __DIR__ . '/../models/carteiras.php';

exigirLogin('../../login.php?redirect=carteira.php');
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

$valor = $_POST['valor'] ?? null;
$adicionado = is_string($valor) && (new Carteira())->adicionarSaldo((int) $_SESSION['usuario_id'], $valor);
$jogoId = filter_input(INPUT_POST, 'jogo', FILTER_VALIDATE_INT);
$destino = '../../carteira.php?recarga=' . ($adicionado ? 'sucesso' : 'invalida');
if ($jogoId && $jogoId > 0) {
    $destino .= '&jogo=' . $jogoId;
}
header('Location: ' . $destino);
exit;
