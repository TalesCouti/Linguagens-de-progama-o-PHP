# Diagrama de classes — PlayStation Store

Os atributos apresentados representam os dados persistidos nas tabelas. Os modelos PHP acessam esses dados por PDO; apenas `Game` declara também os campos do jogo como propriedades. Os métodos e a herança abaixo correspondem ao código atual.

```mermaid
classDiagram
    direction LR

    class Usuario {
        -PDO pdo
        int id
        string nome
        string email
        string telefone
        string senha
        string tipo
        datetime created_at
        datetime updated_at
        +__construct()
        +listarUsuarios() array
        +buscarUsuarioPorId(id) arrayOuFalse
        +cadastrarUsuario(nome, email, senha, tipo, telefone) bool
        +editarUsuario(id, nome, email, tipo, telefone, novaSenha) bool
        +deletarUsuario(id) bool
    }

    class Game {
        -PDO pdo
        -int id
        -string titulo
        -string descricao
        -float preco
        -int estoque
        -string categoria
        -string plataforma
        -string imagem
        +__construct(dados)
        -preencher(dados) void
        -dadosValidos() bool
        +cadastrarGame() bool
        +listarGames() array
        +buscarGamePorId(id)
        +comprarGame(id, usuarioId) string
        +listarComprasDoUsuario(usuarioId) array
        +editarGame() bool
        +deletarGame() bool
    }

    class Taxonomia {
        <<abstract>>
        #PDO pdo
        #string tabela
        +__construct()
        +listar() array
        +buscar(id) arrayOuFalse
        +criar(nome, descricao) bool
        +atualizar(id, nome, descricao) bool
        +excluir(id) bool
        -validar(nome, descricao) bool
    }

    class Categoria {
        int id
        string nome
        string descricao
        #string tabela
        +__construct()
        +listar() array
        +buscar(id) arrayOuFalse
        +criar(nome, descricao) bool
        +atualizar(id, nome, descricao) bool
        +excluir(id) bool
    }

    class Plataforma {
        int id
        string nome
        string descricao
        #string tabela
        +__construct()
        +listar() array
        +buscar(id) arrayOuFalse
        +criar(nome, descricao) bool
        +atualizar(id, nome, descricao) bool
        +excluir(id) bool
    }

    class Avaliacao {
        -PDO pdo
        int usuario_id
        int game_id
        int nota
        string comentario
        datetime criado_em
        datetime atualizado_em
        +__construct()
        +listarPorJogo(gameId) array
        +listarTodas() array
        +buscar(usuarioId, gameId) arrayOuFalse
        +salvar(usuarioId, gameId, nota, comentario) bool
        +remover(usuarioId, gameId) bool
    }

    class Carteira {
        -PDO pdo
        int usuario_id
        decimal saldo
        datetime atualizado_em
        +__construct()
        +consultarSaldo(usuarioId) string
        +listarMovimentacoes(usuarioId) array
        +adicionarSaldo(usuarioId, valorInformado) bool
    }

    class Interacoes {
        -PDO pdo
        int usuario_id
        int game_id
        datetime criado_em
        +__construct()
        +estaNaWishlist(usuarioId, gameId) bool
        +adicionarWishlist(usuarioId, gameId) void
        +removerWishlist(usuarioId, gameId) void
        +listarWishlist(usuarioId) array
    }

    Taxonomia <|-- Categoria
    Taxonomia <|-- Plataforma
    Categoria "0..1" --> "0..*" Game : classifica
    Plataforma "0..1" --> "0..*" Game : disponibiliza
    Usuario "1" --> "0..*" Avaliacao : escreve
    Game "1" --> "0..*" Avaliacao : recebe
    Usuario "1" --> "0..1" Carteira : possui
    Interacoes ..> Usuario : wishlist
    Interacoes ..> Game : wishlist
```

`Categoria` e `Plataforma` herdam os métodos mostrados de `Taxonomia`; `arrayOuFalse` representa o tipo PHP `array|false`. `Interacoes` gerencia a tabela `wishlist`. A tabela `compras` não tem classe própria: a compra e a consulta da biblioteca são métodos de `Game`.
