# Graph Report - loja_games  (2026-11-09)

## Corpus Check
- cluster-only mode — file stats not available

## Summary
- 105 nodes · 98 edges · 32 communities (5 shown, 27 thin omitted)
- Extraction: 100% EXTRACTED · 0% INFERRED · 0% AMBIGUOUS
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `c3b2d065`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- usuarios table
- Game
- autenticacao.php
- Interacoes
- Carteira
- Usuario
- PlayStation Store
- Q: pode criar models compativeis?
- imagens.php

## God Nodes (most connected - your core abstractions)
1. `Game` - 12 edges
2. `Interacoes` - 10 edges
3. `iniciarSessaoSegura()` - 7 edges
4. `Carteira` - 6 edges
5. `usuarios table` - 6 edges
6. `Usuario` - 5 edges
7. `usuarioAutenticado()` - 5 edges
8. `PlayStation Store` - 5 edges
9. `usuarioAdministradorAutenticado()` - 4 edges
10. `Q: pode criar models compativeis?` - 4 edges

## Surprising Connections (you probably didn't know these)
- None detected - all connections are within the same source files.

## Import Cycles
- None detected.

## Communities (32 total, 27 thin omitted)

### Community 0 - "usuarios table"
Cohesion: 0.24
Nodes (10): avaliacoes table, carteiras table, compras table, games table, movimentacoes_carteira table, Game Seed Data, User Seed Data, bcrypt Password Hashes (+2 more)

### Community 15 - "autenticacao.php"
Cohesion: 0.36
Nodes (10): autenticarUsuario(), encerrarSessao(), exigirAdministrador(), exigirLogin(), gerarTokenCsrf(), iniciarSessaoSegura(), PDO, tokenCsrfValido() (+2 more)

### Community 19 - "PlayStation Store"
Cohesion: 0.33
Nodes (5): Entrega acadêmica, Funcionalidades, Instalação, Modelo de dados, PlayStation Store

### Community 20 - "Q: pode criar models compativeis?"
Cohesion: 0.40
Nodes (4): Answer, Outcome, Q: pode criar models compativeis?, Source Nodes

### Community 21 - "imagens.php"
Cohesion: 0.83
Nodes (3): diretorioImagensGames(), imagemGameOuPlaceholder(), salvarImagemGame()

## Knowledge Gaps
- **11 isolated node(s):** `Entrega acadêmica`, `Funcionalidades`, `Instalação`, `Modelo de dados`, `Answer` (+6 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 62 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **27 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **What connects `Entrega acadêmica`, `Funcionalidades`, `Instalação` to the rest of the system?**
  _11 weakly-connected nodes found - possible documentation gaps or missing edges._