<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGP - Dashboard</title>
    <link rel="stylesheet" href="<?= url('css/style.css') ?>?v=<?= time() ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        // Pre-load theme to avoid flicker
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">SGP - <?php
            $user = auth(); // Get the full user object
            $role = $user['role'] ?? '';
            
            // Fetch vacant class count for SEMED users (for badge)
            $vacantClassCount = 0;
            if ($role === 'semed') {
                require_once __DIR__ . '/../../app/Models/User.php';
                require_once __DIR__ . '/../../app/Models/ClassModel.php';
                $userModel = new User();
                $classModel = new ClassModel();
                $assignedSchoolIds = $userModel->getAssignedSchoolIds($user['id']);
                $vacantClassCount = $classModel->countVacantClasses($assignedSchoolIds);
            }
            
            if($role == 'coordinator') echo 'Coordenador';
            elseif($role == 'director') echo 'Diretor';
            elseif($role == 'semed') echo 'DEAPS';
            elseif($role == 'professor') echo 'Professor';
            elseif($role == 'admin' || $role == 'Administrador') echo 'Admin';
            elseif($role == 'supervisor_edfis') echo 'Supervisão de Educação Física';
            elseif($role == 'supervisor_monitor') echo 'Supervisão de Monitores';
        ?></div>
        
        <!-- Hamburger Menu Button (Mobile Only) - Hidden for Professors -->
        <?php if ($role !== 'professor'): ?>
        <button class="hamburger-menu" id="hamburger-btn" aria-label="Menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <?php endif; ?>
        <div class="nav-tabs" id="nav-menu" style="display: flex; gap: 10px; align-items: center; overflow-x: auto; white-space: nowrap; padding-bottom: 5px;">
            <?php if ($role === 'admin' || $role === 'Administrador'): ?>
                <a href="<?= url('admin/dashboard') ?>" class="semed-nav-btn <?= strpos($_SERVER['REQUEST_URI'], 'admin/dashboard') !== false ? 'active' : '' ?>">
                    <i class="fas fa-shield-alt"></i>
                    <span>Painel Admin</span>
                </a>
                <a href="<?= url('admin/schools') ?>" class="semed-nav-btn <?= strpos($_SERVER['REQUEST_URI'], 'admin/schools') !== false ? 'active' : '' ?>">
                    <i class="fas fa-school"></i>
                    <span>Escolas</span>
                </a>
                <a href="<?= url('admin/coordinators') ?>" class="semed-nav-btn <?= strpos($_SERVER['REQUEST_URI'], 'admin/coordinators') !== false ? 'active' : '' ?>">
                    <i class="fas fa-user-tie"></i>
                    <span>Coordenadores</span>
                </a>
                <a href="<?= url('admin/directors') ?>" class="semed-nav-btn <?= strpos($_SERVER['REQUEST_URI'], 'admin/directors') !== false ? 'active' : '' ?>">
                    <i class="fas fa-user-check"></i>
                    <span>Diretores</span>
                </a>
                <a href="<?= url('admin/professors') ?>" class="semed-nav-btn <?= strpos($_SERVER['REQUEST_URI'], 'admin/professors') !== false ? 'active' : '' ?>">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Professores</span>
                </a>
                 <a href="<?= url('admin/semed-users') ?>" class="semed-nav-btn <?= strpos($_SERVER['REQUEST_URI'], 'admin/semed-users') !== false ? 'active' : '' ?>">
                    <i class="fas fa-building"></i>
                    <span>SEMED</span>
                </a>
                <a href="<?= url('admin/reports') ?>" class="semed-nav-btn <?= strpos($_SERVER['REQUEST_URI'], 'admin/reports') !== false ? 'active' : '' ?>">
                    <i class="fas fa-chart-pie"></i>
                    <span>Relatórios</span>
                </a>
                <a href="<?= url('admin/settings') ?>" class="semed-nav-btn <?= strpos($_SERVER['REQUEST_URI'], 'admin/settings') !== false ? 'active' : '' ?>">
                    <i class="fas fa-cog"></i>
                    <span>Configurações</span>
                </a>
            <?php elseif ($role === 'semed'): ?>
                <a href="<?= url('semed/dashboard') ?>" class="semed-nav-btn <?= strpos($_SERVER['REQUEST_URI'], 'semed/dashboard') !== false ? 'active' : '' ?>">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
                <!-- Cadastros Hub -->
    <a href="<?= url('semed/cadastros') ?>" class="semed-nav-btn <?= strpos($_SERVER['REQUEST_URI'], 'semed/cadastros') !== false || strpos($_SERVER['REQUEST_URI'], 'semed/schools') !== false || strpos($_SERVER['REQUEST_URI'], 'semed/coordinators') !== false || strpos($_SERVER['REQUEST_URI'], 'semed/directors') !== false || strpos($_SERVER['REQUEST_URI'], 'admin/semed-users') !== false ? 'active' : '' ?>">
        <i class="fas fa-folder-open"></i>
        <span>Cadastros</span>
    </a>
    
    <!-- Admin-level access for Professors (Pesquisa Only) -->
    <a href="<?= url('admin/professors') ?>" class="semed-nav-btn <?= strpos($_SERVER['REQUEST_URI'], 'admin/professors') !== false ? 'active' : '' ?>">
        <i class="fas fa-chalkboard-teacher"></i>
        <span>Professores</span>
    </a>

    <!-- Admin-level access for Reports -->
    <a href="<?= url('admin/reports') ?>" class="semed-nav-btn <?= strpos($_SERVER['REQUEST_URI'], 'admin/reports') !== false ? 'active' : '' ?>">
        <i class="fas fa-chart-pie"></i>
        <span>Relatórios</span>
    </a>
    
    <!-- Lotação (Staffing Allocation) with Vacancy Alert Badge -->
    <a href="<?= url('semed/lotacao') ?>" class="semed-nav-btn <?= strpos($_SERVER['REQUEST_URI'], 'semed/lotacao') !== false ? 'active' : '' ?>" style="position: relative;">
        <i class="fas fa-users-cog"></i>
        <span>Lotação</span>
        <?php if ($vacantClassCount > 0): ?>
            <span style="position: absolute; top: -5px; right: -5px; background: #dc2626; color: white; border-radius: 50%; width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                <?= $vacantClassCount ?>
            </span>
        <?php endif; ?>
    </a>
                <a href="<?= url('semed/history') ?>" class="semed-nav-btn <?= strpos($_SERVER['REQUEST_URI'], 'semed/history') !== false ? 'active' : '' ?>">
                    <i class="fas fa-history"></i>
                    <span>Banco de Planejamentos</span>
                </a>
            <?php elseif ($role === 'coordinator' || $role === 'director'): ?>
                <a href="<?= url('school/dashboard') ?>" class="semed-nav-btn <?= strpos($_SERVER['REQUEST_URI'], 'school/dashboard') !== false ? 'active' : '' ?>">
                    <i class="fas fa-th-large"></i>
                    <span>Painel da Escola</span>
                </a>
                <?php if ($role === 'director'): ?>
                <a href="<?= url('school/reports') ?>" class="semed-nav-btn <?= strpos($_SERVER['REQUEST_URI'], 'school/reports') !== false ? 'active' : '' ?>">
                    <i class="fas fa-chart-pie"></i>
                    <span>Relatórios</span>
                </a>
                <?php endif; ?>
                <!-- Links abaixo removidos pois agora são abas no painel principal -->
            <?php elseif ($role === 'professor'): ?>
                <a href="<?= url('professor/dashboard') ?>" class="semed-nav-btn <?= strpos($_SERVER['REQUEST_URI'], 'professor/dashboard') !== false ? 'active' : '' ?>">
                    <i class="fas fa-home"></i>
                    <span>Início</span>
                </a>
            <?php endif; ?>
        </div>
        <div class="nav-user">
            <?php if(auth()): ?>
                <span class="d-none d-md-inline">Olá, <?= htmlspecialchars(auth()['name']) ?></span>
                
                <?php if(in_array(auth()['role'], ['semed', 'professor', 'coordinator', 'supervisor_edfis', 'supervisor_monitor'])): ?>
                    <button onclick="document.getElementById('modal-password-global').style.display='block'" class="btn btn-sm" style="background:transparent; border:1px solid var(--border-color); color: var(--text-color); cursor:pointer; margin-left:10px; width:35px; height:35px; display:inline-flex; align-items:center; justify-content:center; border-radius:50%;" title="Alterar Senha">
                        <i class="fas fa-key"></i>
                    </button>
                <?php endif; ?>

                <button id="theme-toggle" class="btn btn-sm" style="background:transparent; border:1px solid var(--border-color); color: var(--text-color); cursor:pointer; margin-left:10px; width:35px; height:35px; display:inline-flex; align-items:center; justify-content:center; border-radius:50%;" title="Alternar Tema">🌙</button>
                
                <a href="<?= url('logout') ?>" class="btn-logout" style="margin-left:10px;"><i class="fas fa-sign-out-alt"></i> Sair</a>
            <?php endif; ?>
        </div>
    </nav>
    <div class="main-container">

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleBtn = document.getElementById('theme-toggle');
        
        // Sync button icon
        const updateIcon = () => {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            if(toggleBtn) toggleBtn.textContent = currentTheme === 'dark' ? '☀️' : '🌙';
        };
        updateIcon();
        
        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                let theme = document.documentElement.getAttribute('data-theme');
                let newTheme = theme === 'light' ? 'dark' : 'light';
                
                document.documentElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateIcon();
            });
        }
        
        // Hamburger Menu Toggle
        const hamburgerBtn = document.getElementById('hamburger-btn');
        const navMenu = document.getElementById('nav-menu');
        
        if (hamburgerBtn && navMenu) {
            hamburgerBtn.addEventListener('click', () => {
                hamburgerBtn.classList.toggle('active');
                navMenu.classList.toggle('active');
            });
            
            // Close menu when clicking outside
            document.addEventListener('click', (e) => {
                if (!hamburgerBtn.contains(e.target) && !navMenu.contains(e.target)) {
                    hamburgerBtn.classList.remove('active');
                    navMenu.classList.remove('active');
                }
            });
            
            // Close menu when clicking on a link
            const navLinks = navMenu.querySelectorAll('a');
            navLinks.forEach(link => {
                link.addEventListener('click', () => {
                    hamburgerBtn.classList.remove('active');
                    navMenu.classList.remove('active');
                });
            });
        }
    });
</script>
