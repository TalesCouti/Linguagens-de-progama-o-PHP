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
            'SELECT id, nome, email, telefone, tipo, created_at
             FROM usuarios
             ORDER BY nome ASC, id ASC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarUsuarioPorId(int $id): array|false
    {
        $stmt = $this->pdo->prepare('SELECT id, nome, email, telefone, tipo FROM usuarios WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function editarUsuario(int $id, string $nome, string $email, string $tipo, ?string $telefone, string $novaSenha = ''): bool
    {
        $nome = trim($nome);
        $email = strtolower(trim($email));
        $telefone = trim((string) $telefone);
        if ($id < 1 || !$this->buscarUsuarioPorId($id) || $nome === '' || mb_strlen($nome) > 100
            || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150
            || !in_array($tipo, ['admin', 'user'], true)
            || ($telefone !== '' && !preg_match('/^[0-9()+. -]{8,20}$/', $telefone))
            || ($novaSenha !== '' && strlen($novaSenha) < 8)) {
            return false;
        }
        $sql = 'UPDATE usuarios SET nome = :nome, email = :email, telefone = :telefone, tipo = :tipo';
        $params = [':nome' => $nome, ':email' => $email, ':telefone' => $telefone ?: null, ':tipo' => $tipo, ':id' => $id];
        if ($novaSenha !== '') {
            $sql .= ', senha = :senha';
            $params[':senha'] = password_hash($novaSenha, PASSWORD_DEFAULT);
        }
        $stmt = $this->pdo->prepare($sql . ' WHERE id = :id');
        return $stmt->execute($params);
    }

    public function deletarUsuario(int $id): bool
    {
        if ($id < 1) {
            return false;
        }
        $stmt = $this->pdo->prepare('DELETE FROM usuarios WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
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
