# Roteiro para apresentação em sala

Abra [apresentacao.html](apresentacao.html) no navegador e use as setas do teclado para avançar ou voltar. Há também uma [versão em PDF](apresentacao.pdf). A apresentação tem sete telas e pode ser usada sem conexão com a internet; as imagens estão na pasta do projeto.

## Fala sugerida (5 a 7 minutos)

1. **Projeto:** apresente a PlayStation Store como uma loja de jogos feita em PHP 8 e MySQL. Mostre o catálogo público.
2. **Fluxo do cliente:** abra um jogo, mostre preço, estoque, wishlist e avaliações. Explique que o cliente se cadastra pela própria página.
3. **Estrutura:** aponte os modelos em `admin/models`, controladores em `admin/controllers` e páginas em `admin/views` e na raiz. Mostre que o acesso ao MySQL usa PDO e que o login usa sessão e hash de senha.
4. **CRUDs:** mostre o painel administrativo. Cite as cinco classes com cadastro, consulta, edição e exclusão: `Usuario`, `Game`, `Categoria`, `Plataforma` e `Avaliacao`. Abra o [diagrama UML](diagrama-classes.svg) se pedirem os atributos e métodos.
5. **Compra:** mostre a carteira. Explique que saldo e estoque são conferidos antes da compra. Após a compra, o saldo diminui, o estoque cai e o jogo aparece na biblioteca.
6. **Banco:** apresente o [modelo de dados](../README.md#modelo-do-banco-de-dados) e o [script SQL](../banco%20de%20dados/schema.txt), que criam banco, tabelas, chaves e dados de exemplo.
7. **Fechamento:** mostre o [repositório público](https://github.com/TalesCouti/Linguagens-de-progama-o-PHP) e o README com instalação, conta de teste, funcionalidades e capturas.

## Demonstração rápida

1. Abra `http://localhost/loja_games/` e entre com `evandro@gmail.com` / `admin123`.
2. No painel, abra Jogos, Usuários, Categorias, Plataformas e Avaliações.
3. Volte à loja, abra um jogo, acesse wishlist e carteira.
4. Mostre a biblioteca em **Meus jogos** e a mensagem de saldo insuficiente quando o valor do jogo ultrapassa o saldo disponível.

Se a internet não estiver disponível, a apresentação e o site local continuam acessíveis no XAMPP. O link do GitHub fica no último slide e no README.
