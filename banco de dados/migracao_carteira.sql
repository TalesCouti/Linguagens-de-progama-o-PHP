-- Execute uma vez no banco loja_games existente.
CREATE TABLE IF NOT EXISTS carteiras (
    usuario_id INT NOT NULL PRIMARY KEY,
    saldo DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    CONSTRAINT chk_carteiras_saldo CHECK (saldo >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS movimentacoes_carteira (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo ENUM('recarga', 'compra') NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    descricao VARCHAR(150) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_movimentacoes_usuario (usuario_id, id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    CONSTRAINT chk_movimentacoes_valor CHECK (valor > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO carteiras (usuario_id)
SELECT id FROM usuarios;
