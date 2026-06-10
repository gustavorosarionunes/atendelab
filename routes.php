<?php
// Carrega todos os controllers
require_once __DIR__ . '/app/Controllers/UsuariosController.php';
require_once __DIR__ . '/app/Controllers/PessoasController.php';
require_once __DIR__ . '/app/Controllers/TiposAtendimentosController.php';
require_once __DIR__ . '/app/Controllers/AtendimentosController.php';

$controller = $_GET['controller'] ?? 'home';
$action = $_GET['action'] ?? 'index';

if ($controller === 'usuarios') {
    $usuariosController = new UsuariosController();
    switch ($action) {
        case 'listar': $usuariosController->listar(); break; 
        case 'buscar': $usuariosController->buscarPorId(); break;
        case 'criar': $usuariosController->criar(); break;
        case 'atualizar': $usuariosController->atualizar(); break;
        case 'excluir': $usuariosController->excluir(); break;
        default: echo 'Ação não encontrada.'; break;
    }
} 
elseif ($controller === 'pessoas') {
    $pessoasController = new PessoasController();
    switch ($action) {
        case 'listar': $pessoasController->listar(); break; 
        case 'buscar': $pessoasController->buscarPorId(); break;
        case 'criar': $pessoasController->criar(); break;
        case 'atualizar': $pessoasController->atualizar(); break;
        case 'excluir': $pessoasController->excluir(); break;
        default: echo 'Ação não encontrada.'; break;
    }
}
elseif ($controller === 'tipos_atendimentos') {
    $tiposController = new TiposAtendimentosController();
    switch ($action) {
        case 'listar': $tiposController->listar(); break; 
        case 'buscar': $tiposController->buscarPorId(); break;
        case 'criar': $tiposController->criar(); break;
        case 'atualizar': $tiposController->atualizar(); break;
        case 'excluir': $tiposController->excluir(); break;
        default: echo 'Ação não encontrada.'; break;
    }
}
elseif ($controller === 'atendimentos') {
    $atendimentosController = new AtendimentosController();
    switch ($action) {
        case 'listar': $atendimentosController->listar(); break; 
        case 'visualizar': $atendimentosController->visualizar(); break;
        case 'criar': $atendimentosController->criar(); break;
        case 'atualizarStatus': $atendimentosController->atualizarStatus(); break;
        default: echo 'Ação não encontrada.'; break;
    }
}
else {
    echo '<h1>AtendeLab</h1>';
    echo '<p>Projeto atualizado com 4 controllers: usuarios, pessoas, tipos_atendimentos e atendimentos.</p>';
}