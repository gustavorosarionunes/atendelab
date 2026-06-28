<?php

declare(strict_types=1);

require_once __DIR__ . '/../Middleware/auth.php';

// ============================================================
// AtendeLab - FrontendController
// Serve apenas as páginas visuais (HTML + PHP).
// Os dados são buscados pelo JavaScript via api.js.
// ============================================================

class FrontendController
{
    public function pessoas(): void
    {
        exigirAutenticacao();
        $tituloPagina = 'Pessoas atendidas';
        require __DIR__ . '/../Views/pessoas/index.php';
    }

    public function tiposAtendimentos(): void
    {
        exigirAutenticacao();
        $tituloPagina = 'Tipos de atendimento';
        require __DIR__ . '/../Views/tipos-atendimentos/index.php';
    }

    public function atendimentos(): void
    {
        exigirAutenticacao();
        $tituloPagina = 'Atendimentos';
        require __DIR__ . '/../Views/atendimentos/index.php';
    }
}
