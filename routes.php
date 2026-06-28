<?php

declare(strict_types=1);

require_once __DIR__ . '/app/Middleware/auth.php';

// ============================================================
// AtendeLab - routes.php
// Roteador central: transforma ?controller=X&action=Y em
// chamadas aos controllers correspondentes.
// ============================================================

$controller = $_GET['controller'] ?? 'auth';
$action     = $_GET['action']     ?? 'login';

// ----------------------------------------------------------
// Função utilitária para rota não encontrada
// ----------------------------------------------------------
function responderRotaNaoEncontrada(string $mensagem = 'Rota não encontrada.'): void
{
    http_response_code(404);
    if (
        isset($_SERVER['HTTP_ACCEPT'])
        && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')
    ) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['erro' => $mensagem]);
    } else {
        echo '<h1>404 - ' . htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') . '</h1>';
    }
    exit;
}

// ----------------------------------------------------------
// Roteamento principal
// ----------------------------------------------------------
switch ($controller) {

    // --------------------------------------------------------
    // AUTH
    // --------------------------------------------------------
    case 'auth':
        require_once __DIR__ . '/app/Controllers/AuthController.php';
        $authController = new AuthController();

        switch ($action) {
            case 'login':
                $authController->exibirLogin();
                break;
            case 'entrar':
                $authController->entrar();
                break;
            case 'dashboard':
                $authController->dashboard();
                break;
            case 'logout':
                $authController->logout();
                break;
            default:
                responderRotaNaoEncontrada('Ação de autenticação não encontrada.');
        }
        break;

    // --------------------------------------------------------
    // DASHBOARD (endpoint JSON de resumo)
    // --------------------------------------------------------
    case 'dashboard':
        exigirAutenticacao();
        require_once __DIR__ . '/app/Controllers/DashboardController.php';
        $dashboardController = new DashboardController();

        switch ($action) {
            case 'resumo':
                $dashboardController->resumo();
                break;
            default:
                responderRotaNaoEncontrada('Ação de dashboard não encontrada.');
        }
        break;

    // --------------------------------------------------------
    // FRONTEND (páginas visuais)
    // --------------------------------------------------------
    case 'frontend':
        exigirAutenticacao();
        require_once __DIR__ . '/app/Controllers/FrontendController.php';
        $frontendController = new FrontendController();

        switch ($action) {
            case 'pessoas':
                $frontendController->pessoas();
                break;
            case 'tipos':
                $frontendController->tiposAtendimentos();
                break;
            case 'atendimentos':
                $frontendController->atendimentos();
                break;
            default:
                responderRotaNaoEncontrada('Página não encontrada.');
        }
        break;

    // --------------------------------------------------------
    // PESSOAS
    // --------------------------------------------------------
    case 'pessoas':
        exigirAutenticacao();
        require_once __DIR__ . '/app/Controllers/PessoasController.php';
        $pessoasController = new PessoasController();

        switch ($action) {
            case 'listar':
                $pessoasController->listar();
                break;
            case 'buscar':
            case 'buscarPorId':
                $pessoasController->buscar();
                break;
            case 'criar':
                $pessoasController->criar();
                break;
            case 'atualizar':
                $pessoasController->atualizar();
                break;
            case 'inativar':
                $pessoasController->inativar();
                break;
            default:
                responderRotaNaoEncontrada('Ação de pessoas não encontrada.');
        }
        break;

    // --------------------------------------------------------
    // TIPOS DE ATENDIMENTO
    // --------------------------------------------------------
    case 'tipos':
        exigirAutenticacao();
        require_once __DIR__ . '/app/Controllers/TiposAtendimentosController.php';
        $tiposController = new TiposAtendimentosController();

        switch ($action) {
            case 'listar':
                $tiposController->listar();
                break;
            case 'buscar':      // compatibilidade com a view (Aula 05)
            case 'buscarPorId':
                $tiposController->buscarPorId();
                break;
            case 'criar':
                $tiposController->criar();
                break;
            case 'atualizar':
                $tiposController->atualizar();
                break;
            case 'inativar':
                $tiposController->inativar();
                break;
            default:
                responderRotaNaoEncontrada('Ação de tipos de atendimento não encontrada.');
        }
        break;

    // --------------------------------------------------------
    // ATENDIMENTOS
    // --------------------------------------------------------
    case 'atendimentos':
        exigirAutenticacao();
        require_once __DIR__ . '/app/Controllers/AtendimentosController.php';
        $atendimentosController = new AtendimentosController();

        switch ($action) {
            case 'listar':
                $atendimentosController->listar();
                break;
            case 'visualizar':
                $atendimentosController->visualizar();
                break;
            case 'criar':
                $atendimentosController->criar();
                break;
            case 'alterarStatus':
            case 'atualizarStatus':
                $atendimentosController->alterarStatus();
                break;
            default:
                responderRotaNaoEncontrada('Ação de atendimentos não encontrada.');
        }
        break;

    // --------------------------------------------------------
    // USUARIOS
    // --------------------------------------------------------
    case 'usuarios':
        exigirAutenticacao();
        require_once __DIR__ . '/app/Controllers/UsuariosController.php';
        $usuariosController = new UsuariosController();

        switch ($action) {
            case 'listar':
                $usuariosController->listar();
                break;
            case 'criar':
                $usuariosController->criar();
                break;
            case 'inativar':
                $usuariosController->inativar();
                break;
            default:
                responderRotaNaoEncontrada('Ação de usuários não encontrada.');
        }
        break;

    // --------------------------------------------------------
    // RELATORIOS
    // --------------------------------------------------------
    case 'relatorios':
        exigirAutenticacao();
        require_once __DIR__ . '/app/Controllers/RelatoriosController.php';
        $relatoriosController = new RelatoriosController();

        switch ($action) {
            case 'atendimentos':
                $relatoriosController->atendimentos();
                break;
            default:
                responderRotaNaoEncontrada('Ação de relatórios não encontrada.');
        }
        break;

    // --------------------------------------------------------
    // DEFAULT
    // --------------------------------------------------------
    default:
        responderRotaNaoEncontrada('Controller não encontrado.');
}
