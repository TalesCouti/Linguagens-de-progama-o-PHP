<?php

require_once __DIR__ . '/../config/conexao.php';

class Game
{
    private PDO $pdo;

    private ?int $id = null;
    private string $titulo = '';
    private string $descricao = '';
    private float $preco = 0.0;
    private int $estoque = 0;
    private string $categoria = '';
    private string $imagem = '';

    public function __construct(array $dados = [])
    {
        global $pdo;

        if (!isset($pdo) || !$pdo instanceof PDO) {
            throw new RuntimeException('A conexão PDO não está disponível.');
        }

        $this->pdo = $pdo;
        $this->preencher($dados);
    }

    private function preencher(array $dados): void
    {
        if (isset($dados['id'])) {
            $this->id = (int) $dados['id'];
        }

        if (array_key_exists('titulo', $dados)) {
            $this->titulo = trim((string) $dados['titulo']);
        }

        if (array_key_exists('descricao', $dados)) {
            $this->descricao = trim((string) $dados['descricao']);
        }

        if (array_key_exists('preco', $dados)) {
            $this->preco = (float) str_replace(',', '.', (string) $dados['preco']);
        }

        if (array_key_exists('estoque', $dados)) {
            $this->estoque = (int) $dados['estoque'];
        }

        if (array_key_exists('categoria', $dados)) {
            $this->categoria = trim((string) $dados['categoria']);
        }

        if (array_key_exists('imagem', $dados)) {
            $this->imagem = basename((string) $dados['imagem']);
        }
    }

    private function dadosValidos(): bool
    {
        return $this->titulo !== ''
            && $this->preco >= 0
            && $this->estoque >= 0;
    }

    public function cadastrarGame(): bool
    {
        if (!$this->dadosValidos()) {
            return false;
        }

        $sql = 'INSERT INTO games
                    (titulo, descricao, preco, estoque, categoria, imagem)
                VALUES
                    (:titulo, :descricao, :preco, :estoque, :categoria, :imagem)';

        $stmt = $this->pdo->prepare($sql);
        $resultado = $stmt->execute([
            ':titulo' => $this->titulo,
            ':descricao' => $this->descricao,
            ':preco' => $this->preco,
            ':estoque' => $this->estoque,
            ':categoria' => $this->categoria,
            ':imagem' => $this->imagem,
        ]);

        if ($resultado) {
            $this->id = (int) $this->pdo->lastInsertId();
        }

        return $resultado;
    }

    public function listarGames(): array
    {
        $sql = 'SELECT id, titulo, descricao, preco, estoque, categoria, imagem,
                       created_at, updated_at
                FROM games
                ORDER BY id DESC';

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarGamePorId($id)
    {
        $id = (int) $id;

        if ($id <= 0) {
            return false;
        }

        $stmt = $this->pdo->prepare(
            'SELECT id, titulo, descricao, preco, estoque, categoria, imagem,
                    created_at, updated_at
             FROM games
             WHERE id = :id
             LIMIT 1'
        );
        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function comprarGame(int $id, int $usuarioId): string
    {
        if ($id <= 0 || $usuarioId <= 0) {
            return 'sem_estoque';
        }

        $this->pdo->beginTransaction();
        try {
            $buscar = $this->pdo->prepare(
                'SELECT titulo, preco, estoque FROM games WHERE id = :id FOR UPDATE'
            );
            $buscar->execute([':id' => $id]);
            $game = $buscar->fetch(PDO::FETCH_ASSOC);
            if (!$game || (int) $game['estoque'] < 1) {
                $this->pdo->rollBack();
                return 'sem_estoque';
            }

            $this->pdo->prepare('INSERT IGNORE INTO carteiras (usuario_id) VALUES (:usuario_id)')
                ->execute([':usuario_id' => $usuarioId]);
            if ((float) $game['preco'] > 0) {
                $descontar = $this->pdo->prepare(
                    'UPDATE carteiras SET saldo = saldo - :preco
                     WHERE usuario_id = :usuario_id AND saldo >= :saldo_minimo'
                );
                $descontar->execute([
                    ':preco' => $game['preco'],
                    ':usuario_id' => $usuarioId,
                    ':saldo_minimo' => $game['preco'],
                ]);
                if ($descontar->rowCount() !== 1) {
                    $this->pdo->rollBack();
                    return 'saldo_insuficiente';
                }
            }

            $atualizarEstoque = $this->pdo->prepare(
                'UPDATE games SET estoque = estoque - 1 WHERE id = :id AND estoque > 0'
            );
            $atualizarEstoque->execute([':id' => $id]);
            if ($atualizarEstoque->rowCount() !== 1) {
                throw new RuntimeException('Não foi possível atualizar o estoque.');
            }

            $registro = $this->pdo->prepare(
                'INSERT INTO compras (usuario_id, game_id, titulo, preco)
                 VALUES (:usuario_id, :game_id, :titulo, :preco)'
            );
            $registro->execute([
                ':usuario_id' => $usuarioId,
                ':game_id' => $id,
                ':titulo' => $game['titulo'],
                ':preco' => $game['preco'],
            ]);
            if ((float) $game['preco'] > 0) {
                $movimento = $this->pdo->prepare(
                    "INSERT INTO movimentacoes_carteira (usuario_id, tipo, valor, descricao)
                     VALUES (:usuario_id, 'compra', :valor, :descricao)"
                );
                $movimento->execute([
                    ':usuario_id' => $usuarioId,
                    ':valor' => $game['preco'],
                    ':descricao' => 'Compra: ' . $game['titulo'],
                ]);
            }
            $this->pdo->commit();
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }

        return 'sucesso';
    }

    public function listarComprasDoUsuario(int $usuarioId): array
    {
        if ($usuarioId <= 0) {
            return [];
        }

        $stmt = $this->pdo->prepare(
            'SELECT g.id, g.id AS catalogo_id, c.titulo, g.descricao, c.preco, g.categoria, g.imagem,
                    c.comprado_em
             FROM compras c
             LEFT JOIN games g ON g.id = c.game_id
             WHERE c.usuario_id = :usuario_id
             ORDER BY c.comprado_em DESC, c.id DESC'
        );
        $stmt->execute([':usuario_id' => $usuarioId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function editarGame(): bool
    {
        if ($this->id === null || $this->id <= 0 || !$this->dadosValidos()) {
            return false;
        }

        $sql = 'UPDATE games
                SET titulo = :titulo,
                    descricao = :descricao,
                    preco = :preco,
                    estoque = :estoque,
                    categoria = :categoria,
                    imagem = :imagem
                WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':id' => $this->id,
            ':titulo' => $this->titulo,
            ':descricao' => $this->descricao,
            ':preco' => $this->preco,
            ':estoque' => $this->estoque,
            ':categoria' => $this->categoria,
            ':imagem' => $this->imagem,
        ]);
    }

    public function deletarGame(): bool
    {
        if ($this->id === null || $this->id <= 0) {
            return false;
        }

        $stmt = $this->pdo->prepare('DELETE FROM games WHERE id = :id');

        return $stmt->execute([':id' => $this->id]);
    }
}
