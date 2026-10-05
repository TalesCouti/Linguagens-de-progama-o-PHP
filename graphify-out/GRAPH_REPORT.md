# Graph Report - loja_games  (2026-10-05)

## Corpus Check
- Corpus is ~651 words - fits in a single context window. You may not need a graph.

## Summary
- 18 nodes · 3 edges · 15 communities (1 shown, 14 thin omitted)
- Extraction: 100% EXTRACTED · 0% INFERRED · 0% AMBIGUOUS
- Token cost: 0 input · 0 output

## Community Hubs (Navigation)
- Users and Passwords
- Games Seed Data

## God Nodes (most connected - your core abstractions)
1. `User Seed Data` - 2 edges
2. `usuarios table` - 1 edges
3. `games table` - 1 edges
4. `Game Seed Data` - 1 edges
5. `SHA2 Password Hashing` - 1 edges

## Surprising Connections (you probably didn't know these)
- None detected - all connections are within the same source files.

## Import Cycles
- None detected.

## Communities (15 total, 14 thin omitted)

### Community 0 - "Users and Passwords"
Cohesion: 0.67
Nodes (3): User Seed Data, SHA2 Password Hashing, usuarios table

## Knowledge Gaps
- **4 isolated node(s):** `usuarios table`, `games table`, `Game Seed Data`, `SHA2 Password Hashing`
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 17 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **14 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **What connects `usuarios table`, `games table`, `Game Seed Data` to the rest of the system?**
  _4 weakly-connected nodes found - possible documentation gaps or missing edges._