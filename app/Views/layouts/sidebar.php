<aside class="sidebar" id="sidebar">
    <div class="brand">
        <span class="brand-mark">
            <i class="bi bi-chat-square-text"></i>
        </span>
        <div>
            <strong>AtendeLab</strong>
            <small>Academic Desk</small>
        </div>
    </div>

    <nav class="nav flex-column gap-1">

        <?php
            // Pega o controller atual (mesmo que já exista no config-view.php)
            $controllerAtual = $_GET['controller'] ?? 'dashboard';
        ?>

        <a class="nav-link <?= $controllerAtual === 'dashboard' ? 'active' : '' ?>" 
           href="?controller=dashboard&action=index">
            <i class="bi bi-grid"></i> Dashboard
        </a>

        <a class="nav-link <?= $controllerAtual === 'pessoas' ? 'active' : '' ?>" 
           href="?controller=pessoas&action=index">
            <i class="bi bi-people"></i> Pessoas atendidas
        </a>

        <a class="nav-link <?= $controllerAtual === 'tipos-atendimentos' ? 'active' : '' ?>" 
           href="?controller=tipos-atendimentos&action=index">
            <i class="bi bi-tags"></i> Tipos de atendimento
        </a>

        <a class="nav-link <?= $controllerAtual === 'atendimentos' ? 'active' : '' ?>" 
           href="?controller=atendimentos&action=index">
            <i class="bi bi-journal-check"></i> Atendimentos
        </a>

        <?php if (($usuario['perfil'] ?? $_SESSION['usuario']['perfil'] ?? '') === 'administrador'): ?>
            <a class="nav-link <?= $controllerAtual === 'usuarios' ? 'active' : '' ?>" 
               href="?controller=usuarios&action=index">
                <i class="bi bi-person-gear"></i> Usuários
            </a>
        <?php endif; ?>

        <a class="nav-link logout-link" 
           href="?controller=auth&action=logout">
            <i class="bi bi-box-arrow-left"></i> Sair
        </a>
    </nav>
</aside>
