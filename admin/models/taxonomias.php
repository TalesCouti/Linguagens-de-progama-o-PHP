<?php
require_once __DIR__ . '/../config/conexao.php';

abstract class Taxonomia
{
    protected PDO $pdo;
    protected string $tabela;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function listar(): array
    {
        return $this->pdo->query("SELECT id, nome, descricao FROM {$this->tabela} ORDER BY nome")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscar(int $id): array|false
    {
        $stmt = $this->pdo->prepare("SELECT id, nome, descricao FROM {$this->tabela} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function criar(string $nome, string $descricao): bool
    {
        if (!$this->validar($nome, $descricao)) {
            return false;
        }
        $stmt = $this->pdo->prepare("INSERT INTO {$this->tabela} (nome, descricao) VALUES (?, ?)");
        return $stmt->execute([trim($nome), trim($descricao)]);
    }

    public function atualizar(int $id, string $nome, string $descricao): bool
    {
        if ($id < 1 || !$this->validar($nome, $descricao)) {
            return false;
        }
        $stmt = $this->pdo->prepare("UPDATE {$this->tabela} SET nome = ?, descricao = ? WHERE id = ?");
        $stmt->execute([trim($nome), trim($descricao), $id]);
        return $stmt->rowCount() > 0 || $this->buscar($id) !== false;
    }

    public function excluir(int $id): bool
    {
        if ($id < 1) {
            return false;
        }
        $stmt = $this->pdo->prepare("DELETE FROM {$this->tabela} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    private function validar(string $nome, string $descricao): bool
    {
        return trim($nome) !== '' && mb_strlen(trim($nome)) <= 50
            && mb_strlen(trim($descricao)) <= 255;
    }
}

final class Categoria extends Taxonomia
{
    protected string $tabela = 'categorias';
}

final class Plataforma extends Taxonomia
{
    protected string $tabela = 'plataformas';
}
