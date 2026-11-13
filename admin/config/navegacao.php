<?php

require_once __DIR__ . '/autenticacao.php';
require_once __DIR__ . '/../models/carteiras.php';

function linkCarteiraComSaldo(?string $saldo = null, bool $paginaAtual = false, string $caminhoCarteira = 'carteira.php'): string
{
    if (!usuarioAutenticado()) {
        return '';
    }

    $saldo ??= (new Carteira())->consultarSaldo((int) $_SESSION['usuario_id']);
    $valor = 'R$ ' . number_format((float) $saldo, 2, ',', '.');
    $valorSeguro = htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
    $paginaAtualAtributo = $paginaAtual ? ' aria-current="page"' : '';

    return '<a class="nav-link nav-wallet" href="' . htmlspecialchars($caminhoCarteira, ENT_QUOTES, 'UTF-8') . '"' . $paginaAtualAtributo
        . ' aria-label="Carteira, saldo disponível: ' . $valorSeguro . '">'
        . '<span class="nav-wallet-label">Saldo</span><strong>' . $valorSeguro . '</strong></a>';
}
