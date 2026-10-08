<?php

function iniciarSessaoSegura(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}

function gerarTokenCsrf(): string
{
    iniciarSessaoSegura();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function tokenCsrfValido(?string $token): bool
{
    iniciarSessaoSegura();

    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function usuarioAdministradorAutenticado(): bool
{
    iniciarSessaoSegura();

    return isset($_SESSION['usuario_id'], $_SESSION['usuario_tipo'])
        && $_SESSION['usuario_tipo'] === 'admin';
}

function autenticarAdministrador(PDO $pdo, string $email, string $senhaInformada): bool
{
    $stmt = $pdo->prepare(
        'SELECT id, nome, email, senha, tipo
         FROM usuarios
         WHERE email = :email
         LIMIT 1'
    );
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario || $usuario['tipo'] !== 'admin') {
        return false;
    }

    $senhaArmazenada = (string) $usuario['senha'];
    $senhaValida = password_verify($senhaInformada, $senhaArmazenada);
    $hashLegado = preg_match('/^[a-f0-9]{64}$/i', $senhaArmazenada) === 1;

    if (!$senhaValida && $hashLegado) {
        $senhaValida = hash_equals(
            strtolower($senhaArmazenada),
            hash('sha256', $senhaInformada)
        );
    }

    if (!$senhaValida) {
        return false;
    }

    if ($hashLegado || password_needs_rehash($senhaArmazenada, PASSWORD_DEFAULT)) {
        $novoHash = password_hash($senhaInformada, PASSWORD_DEFAULT);
        $atualizar = $pdo->prepare(
            'UPDATE usuarios
             SET senha = :nova_senha
             WHERE id = :id AND senha = :senha_anterior'
        );
        $atualizar->execute([
            ':nova_senha' => $novoHash,
            ':id' => (int) $usuario['id'],
            ':senha_anterior' => $senhaArmazenada,
        ]);
    }

    iniciarSessaoSegura();
    session_regenerate_id(true);
    $_SESSION['usuario_id'] = (int) $usuario['id'];
    $_SESSION['usuario_nome'] = (string) $usuario['nome'];
    $_SESSION['usuario_email'] = (string) $usuario['email'];
    $_SESSION['usuario_tipo'] = (string) $usuario['tipo'];
    unset($_SESSION['csrf_token']);

    return true;
}

function exigirAdministrador(string $urlLogin = '../../login.php'): void
{
    if (usuarioAdministradorAutenticado()) {
        return;
    }

    header('Location: ' . $urlLogin);
    exit;
}

function encerrarSessao(): void
{
    iniciarSessaoSegura();
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $parametros = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 42000,
            'path' => $parametros['path'],
            'domain' => $parametros['domain'],
            'secure' => $parametros['secure'],
            'httponly' => $parametros['httponly'],
            'samesite' => $parametros['samesite'] ?? 'Lax',
        ]);
    }

    session_destroy();
}
