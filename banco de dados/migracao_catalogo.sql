-- Execute uma vez após migracao_interacoes.sql e migracao_carteira.sql.
USE loja_games;
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE,
    descricao VARCHAR(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS plataformas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE,
    descricao VARCHAR(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT IGNORE INTO categorias (nome)
SELECT DISTINCT categoria FROM games WHERE categoria IS NOT NULL AND categoria <> '';
INSERT IGNORE INTO plataformas (nome, descricao) VALUES
('PlayStation 4', 'Jogos para PS4'), ('PlayStation 5', 'Jogos para PS5');
ALTER TABLE games
    ADD COLUMN plataforma VARCHAR(50) DEFAULT NULL AFTER categoria,
    ADD CONSTRAINT fk_games_categoria FOREIGN KEY (categoria)
        REFERENCES categorias(nome) ON UPDATE CASCADE ON DELETE SET NULL,
    ADD CONSTRAINT fk_games_plataforma FOREIGN KEY (plataforma)
        REFERENCES plataformas(nome) ON UPDATE CASCADE ON DELETE SET NULL;
UPDATE games SET plataforma = 'PlayStation 5' WHERE plataforma IS NULL;
