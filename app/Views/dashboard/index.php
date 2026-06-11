<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - AtendeLab</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-secondary bg-opacity-10"> <nav class="navbar navbar-dark bg-primary shadow-sm"> <div class="container">
            <span class="navbar-brand fw-bold">AtendeLab Workspace</span>
            <a class="btn btn-light btn-sm fw-semibold text-primary" href="?controller=auth&action=logout">
                Sair do Sistema
            </a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="card shadow border-0 rounded-4"> <div class="card-body p-5"> <h1 class="h3 text-primary mb-3">Painel de Controle</h1>
                
                <p class="fs-5">
                    Olá, <strong><?= htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8') ?></strong>!
                </p>

                <?php if ($usuario['perfil'] === 'admin'): ?>
                    <div class="alert alert-warning border-start border-4 border-warning shadow-sm">
                        <strong>👑 Área do Administrador:</strong> Você tem acesso total às configurações e gestão de usuários do sistema.
                    </div>
                <?php else: ?>
                    <div class="alert alert-info border-start border-4 border-info shadow-sm">
                        <strong>ℹ️ Aviso de Acesso:</strong> Seu perfil atual é de <b><?= htmlspecialchars($usuario['perfil'], ENT_QUOTES, 'UTF-8') ?></b>. Algumas funções podem estar restritas.
                    </div>
                <?php endif; ?>

                <div class="mt-5 mb-4">
                    <h5 class="text-secondary border-bottom pb-2">Próximos módulos que serão desenvolvidos:</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item bg-transparent">🚀 Módulo de Cadastro de Pessoas</li>
                        <li class="list-group-item bg-transparent">🚀 Módulo de Tipos de Atendimento</li>
                        <li class="list-group-item bg-transparent">🚀 Tela de Registro de Atendimentos</li>
                        <li class="list-group-item bg-transparent">📊 Dashboard com Indicadores Reais</li>
                    </ul>
                </div>

                <a class="btn btn-outline-primary" href="?controller=usuarios&action=listar">
                    Testar rota protegida de usuários (JSON)
                </a>
            </div>
        </div>
    </div>

</body>

</html>