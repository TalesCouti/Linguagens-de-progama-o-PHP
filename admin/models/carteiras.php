<?php

require_once __DIR__ . '/../config/conexao.php';

class Carteira
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

    public function consultarSaldo(int $usuarioId): string
    {
        $stmt = $this->pdo->prepare('SELECT saldo FROM carteiras WHERE usuario_id = :usuario_id');
        $stmt->execute([':usuario_id' => $usuarioId]);
        $saldo = $stmt->fetchColumn();
        return $saldo === false ? '0.00' : (string) $saldo;
    }

    public function listarMovimentacoes(int $usuarioId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT tipo, valor, descricao, criado_em
             FROM movimentacoes_carteira WHERE usuario_id = :usuario_id
             ORDER BY id DESC LIMIT 50'
        );
        $stmt->execute([':usuario_id' => $usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function adicionarSaldo(int $usuarioId, string $valorInformado): bool
    {
        if ($usuarioId <= 0 || !preg_match('/\A(\d{1,5})(?:[.,](\d{1,2}))?\z/D', trim($valorInformado), $partes)) {
            return false;
        }

        $centavos = (int) $partes[1] * 100 + (int) str_pad($partes[2] ?? '0', 2, '0');
        if ($centavos < 1 || $centavos > 1000000) {
            return false;
        }
        $valor = sprintf('%d.%02d', intdiv($centavos, 100), $centavos % 100);

        $this->pdo->beginTransaction();
        try {
            $this->pdo->prepare('INSERT IGNORE INTO carteiras (usuario_id) VALUES (:usuario_id)')
                ->execute([':usuario_id' => $usuarioId]);

            $atualizar = $this->pdo->prepare(
                'UPDATE carteiras SET saldo = saldo + :valor
                 WHERE usuario_id = :usuario_id AND saldo <= 99999999.99 - :limite'
            );
            $atualizar->execute([':valor' => $valor, ':usuario_id' => $usuarioId, ':limite' => $valor]);
            if ($atualizar->rowCount() !== 1) {
                $this->pdo->rollBack();
                return false;
            }

            $this->pdo->prepare(
                "INSERT INTO movimentacoes_carteira (usuario_id, tipo, valor, descricao)
                 VALUES (:usuario_id, 'recarga', :valor, 'Saldo adicionado')"
            )->execute([':usuario_id' => $usuarioId, ':valor' => $valor]);
            $this->pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }
}
