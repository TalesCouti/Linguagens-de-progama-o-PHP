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

}
