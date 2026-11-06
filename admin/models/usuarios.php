<?php

require_once __DIR__ . '/../config/conexao.php';

class Usuario
{
    private PDO $pdo;

    public function __construct()
    {
        global $pdo;

        if (!isset($pdo) || !$pdo instanceof PDO) {
            throw new RuntimeException('A conexão PDO não está disponível.');
        }

        $this->pdo = $pdo;
    }

    public function listarUsuarios(): array
    {
        $stmt = $this->pdo->query(
            'SELECT id, nome, email, tipo, created_at
             FROM usuarios
             ORDER BY nome ASC, id ASC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cadastrarUsuario(string $nome, string $email, string $senha, string $tipo, ?string $telefone = null): bool
    {
        $nome = trim($nome);
        $email = strtolower(trim($email));
        $telefone = $telefone === null ? null : trim($telefone);

        if (
            $nome === ''
            || strlen($nome) > 100
            || !filter_var($email, FILTER_VALIDATE_EMAIL)
            || strlen($email) > 150
            || strlen($senha) < 8
            || !in_array($tipo, ['admin', 'user'], true)
            || ($telefone !== null && !preg_match('/^[0-9()+. -]{8,20}$/', $telefone))
        ) {
            return false;
        }

        $hash = password_hash($senha, PASSWORD_DEFAULT);
        if ($hash === false) {
            throw new RuntimeException('Não foi possível proteger a senha informada.');
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO usuarios (nome, email, telefone, senha, tipo)
             VALUES (:nome, :email, :telefone, :senha, :tipo)'
        );

        return $stmt->execute([
            ':nome' => $nome,
            ':email' => $email,
            ':telefone' => $telefone,
            ':senha' => $hash,
            ':tipo' => $tipo,
        ]);
    }
}
