<?php

declare(strict_types=1);

// ============================================================
// AtendeLab - public/index.php
// Ponto de entrada único da aplicação.
// Inicia a sessão e delega ao roteador.
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../routes.php';
