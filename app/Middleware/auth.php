<?php

declare(strict_types=1);

// ============================================================
// AtendeLab - Middleware de Autenticação
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function usuarioAutenticado(): bool
{
    return isset($_SESSION['usuario']['id']);
}

function exigirAutenticacao(): void
{
    if (!usuarioAutenticado()) {
        header('Location: /atendelab/public/?controller=auth&action=login');
        exit;
    }
}

function usuarioAtual(): array
{
    return $_SESSION['usuario'] ?? [];
}
