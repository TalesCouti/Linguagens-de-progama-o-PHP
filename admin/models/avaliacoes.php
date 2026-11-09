<?php
require_once __DIR__ . '/../config/conexao.php';

class Avaliacao
{
    private PDO $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function listarPorJogo(int $gameId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT a.usuario_id, a.nota, a.comentario, a.atualizado_em, u.nome
             FROM avaliacoes a INNER JOIN usuarios u ON u.id = a.usuario_id
             WHERE a.game_id = ? ORDER BY a.atualizado_em DESC'
        );
        $stmt->execute([$gameId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarTodas(): array
    {
        return $this->pdo->query(
            'SELECT a.usuario_id, a.game_id, a.nota, a.comentario, a.atualizado_em,
                    u.nome AS usuario_nome, g.titulo AS game_titulo
             FROM avaliacoes a
             JOIN usuarios u ON u.id = a.usuario_id
             JOIN games g ON g.id = a.game_id
             ORDER BY a.atualizado_em DESC'
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscar(int $usuarioId, int $gameId): array|false
    {
        $stmt = $this->pdo->prepare('SELECT usuario_id, game_id, nota, comentario FROM avaliacoes WHERE usuario_id = ? AND game_id = ?');
        $stmt->execute([$usuarioId, $gameId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function salvar(int $usuarioId, int $gameId, int $nota, string $comentario): bool
    {
        $comentario = trim($comentario);
        if ($usuarioId < 1 || $gameId < 1 || $nota < 1 || $nota > 5 || mb_strlen($comentario) > 1000) {
            return false;
        }
        $stmt = $this->pdo->prepare(
            'INSERT INTO avaliacoes (usuario_id, game_id, nota, comentario)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE nota = VALUES(nota), comentario = VALUES(comentario)'
        );
        return $stmt->execute([$usuarioId, $gameId, $nota, $comentario]);
    }

    public function remover(int $usuarioId, int $gameId): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM avaliacoes WHERE usuario_id = ? AND game_id = ?');
        return $stmt->execute([$usuarioId, $gameId]);
    }
}
