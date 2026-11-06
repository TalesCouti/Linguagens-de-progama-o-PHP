<?php

require_once __DIR__ . '/../config/conexao.php';

class Interacoes
{
    private PDO $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function estaNaWishlist(int $usuarioId, int $gameId): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM wishlist WHERE usuario_id = ? AND game_id = ?');
        $stmt->execute([$usuarioId, $gameId]);
        return (bool) $stmt->fetchColumn();
    }

    public function adicionarWishlist(int $usuarioId, int $gameId): void
    {
        $stmt = $this->pdo->prepare('INSERT IGNORE INTO wishlist (usuario_id, game_id) VALUES (?, ?)');
        $stmt->execute([$usuarioId, $gameId]);
    }

    public function removerWishlist(int $usuarioId, int $gameId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM wishlist WHERE usuario_id = ? AND game_id = ?');
        $stmt->execute([$usuarioId, $gameId]);
    }

    public function listarWishlist(int $usuarioId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT g.id, g.titulo, g.descricao, g.preco, g.estoque, g.categoria, g.imagem
             FROM wishlist w INNER JOIN games g ON g.id = w.game_id
             WHERE w.usuario_id = ? ORDER BY w.criado_em DESC'
        );
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarAvaliacoes(int $gameId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT a.usuario_id, a.nota, a.comentario, a.atualizado_em, u.nome
             FROM avaliacoes a INNER JOIN usuarios u ON u.id = a.usuario_id
             WHERE a.game_id = ? ORDER BY a.atualizado_em DESC'
        );
        $stmt->execute([$gameId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function salvarAvaliacao(int $usuarioId, int $gameId, int $nota, string $comentario): bool
    {
        $comentario = trim($comentario);
        if ($nota < 1 || $nota > 5 || mb_strlen($comentario) > 1000) {
            return false;
        }
        $stmt = $this->pdo->prepare(
            'INSERT INTO avaliacoes (usuario_id, game_id, nota, comentario)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE nota = VALUES(nota), comentario = VALUES(comentario)'
        );
        return $stmt->execute([$usuarioId, $gameId, $nota, $comentario]);
    }

    public function removerAvaliacao(int $usuarioId, int $gameId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM avaliacoes WHERE usuario_id = ? AND game_id = ?');
        $stmt->execute([$usuarioId, $gameId]);
    }
}
