<?php
require_once '../config/conexao.php';

class Game
{
    public $id;
    public $titulo;
    public $descricao;
    public $preco;
    public $estoque;
    public $categoria;
    public $imagem;

    private $pdo;

    public function __construct($atrib = [])
    {
        global $pdo;
        $this->pdo = $pdo;

        if (!empty($atrib)) {
            $this->id = $atrib['id'] ?? null;
            $this->titulo = $atrib['titulo'] ?? null;
            $this->descricao = $atrib['descricao'] ?? null;
            $this->preco = $atrib['preco'] ?? null;
            $this->estoque = $atrib['estoque'] ?? null;
            $this->categoria = $atrib['categoria'] ?? null;
            $this->imagem = $atrib['imagem'] ?? null;
        }
    }

    // Listar todos os games
    public function listarGames()
    {
        $sth = $this->pdo->query(
            "SELECT * FROM games ORDER BY titulo ASC"
        );

        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar games por nome
    public function buscarPorNome($nome)
    {
        $sth = $this->pdo->prepare(
            "SELECT * FROM games
             WHERE titulo LIKE :nome
             ORDER BY titulo ASC"
        );

        $nome = "%" . $nome . "%";

        $sth->bindValue(
            ':nome',
            $nome,
            PDO::PARAM_STR
        );

        $sth->execute();

        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cadastrar um novo game
    public function cadastrarGame()
    {
        $sth = $this->pdo->prepare(
            "INSERT INTO games
            (titulo, descricao, preco, estoque, categoria, imagem, created_at, updated_at)
            VALUES
            (:titulo, :descricao, :preco, :estoque, :categoria, :imagem, NOW(), NOW())"
        );

        $sth->bindValue(':titulo', $this->titulo, PDO::PARAM_STR);
        $sth->bindValue(':descricao', $this->descricao, PDO::PARAM_STR);
        $sth->bindValue(':preco', $this->preco);
        $sth->bindValue(':estoque', $this->estoque, PDO::PARAM_INT);
        $sth->bindValue(':categoria', $this->categoria, PDO::PARAM_STR);
        $sth->bindValue(':imagem', $this->imagem, PDO::PARAM_STR);

        return $sth->execute();
    }

    // Editar um game
    public function editarGame()
    {
        $sth = $this->pdo->prepare(
            "UPDATE games SET
                titulo = :titulo,
                descricao = :descricao,
                preco = :preco,
                estoque = :estoque,
                categoria = :categoria,
                imagem = :imagem,
                updated_at = NOW()
             WHERE id = :id"
        );

        $sth->bindValue(':id', $this->id, PDO::PARAM_INT);
        $sth->bindValue(':titulo', $this->titulo, PDO::PARAM_STR);
        $sth->bindValue(':descricao', $this->descricao, PDO::PARAM_STR);
        $sth->bindValue(':preco', $this->preco);
        $sth->bindValue(':estoque', $this->estoque, PDO::PARAM_INT);
        $sth->bindValue(':categoria', $this->categoria, PDO::PARAM_STR);
        $sth->bindValue(':imagem', $this->imagem, PDO::PARAM_STR);

        return $sth->execute();
    }

    // Deletar um game
    public function deletarGame()
    {
        $sth = $this->pdo->prepare(
            "DELETE FROM games WHERE id = :id"
        );

        $sth->bindValue(
            ':id',
            $this->id,
            PDO::PARAM_INT
        );

        return $sth->execute();
    }

    // Buscar um game por ID
    public function buscarGamePorId($id)
    {
        $sth = $this->pdo->prepare(
            "SELECT * FROM games WHERE id = :id"
        );

        $sth->bindValue(
            ':id',
            $id,
            PDO::PARAM_INT
        );

        $sth->execute();

        return $sth->fetch(PDO::FETCH_ASSOC);
    }
}
?>
