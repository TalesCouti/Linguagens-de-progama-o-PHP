<?php
require_once __DIR__ . '/../config/autenticacao.php';
exigirAdministrador('../../login.php');
require_once __DIR__ . '/../models/usuarios.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit;
}
if (!tokenCsrfValido(is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : null)) {
    http_response_code(403);
    exit('Solicitação inválida.');
}
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
if ($id === (int) $_SESSION['usuario_id']) {
    header('Location: ../views/ver_usuarios.php?erro=1');
    exit;
}
$ok = (new Usuario())->deletarUsuario($id);
header('Location: ../views/ver_usuarios.php?' . ($ok ? 'deletado=1' : 'erro=1'));
exit;
