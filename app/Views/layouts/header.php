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
            if($role == 'coordinator') echo 'Coordenador';
            elseif($role == 'director') echo 'Diretor';
            elseif($role == 'semed') echo 'SEMED';
            elseif($role == 'professor') echo 'Professor';
            elseif($role == 'admin') echo 'Admin';
        ?></div>
        
        <!-- Hamburger Menu Button (Mobile Only) -->
        <button class="hamburger-menu" id="hamburger-btn" aria-label="Menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <div class="nav-tabs" id="nav-menu" style="display: flex; gap: 15px; flex-wrap: wrap;">
            <?php if ($role === 'admin'): ?>
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
                <a href="<?= url('admin/professors') ?>" class="semed-nav-btn <?= strpos($_SERVER['REQUEST_URI'], 'admin/professors') !== false ? 'active' : '' ?>">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Professores</span>
                </a>
                <a href="<?= url('admin/reports') ?>" class="semed-nav-btn <?= strpos($_SERVER['REQUEST_URI'], 'admin/reports') !== false ? 'active' : '' ?>">
                    <i class="fas fa-chart-pie"></i>
                    <span>Relatórios</span>
                </a>
            <?php elseif ($role === 'semed'): ?>
                <a href="<?= url('semed/dashboard') ?>" class="semed-nav-btn <?= strpos($_SERVER['REQUEST_URI'], 'semed/dashboard') !== false ? 'active' : '' ?>">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard Global</span>
                </a>
                <a href="<?= url('semed/schools') ?>" class="semed-nav-btn <?= strpos($_SERVER['REQUEST_URI'], 'semed/schools') !== false ? 'active' : '' ?>">
                    <i class="fas fa-school"></i>
                    <span>Escolas</span>
                </a>
                <a href="<?= url('semed/directors') ?>" class="semed-nav-btn <?= strpos($_SERVER['REQUEST_URI'], 'semed/directors') !== false ? 'active' : '' ?>">
                    <i class="fas fa-user-tie"></i>
                    <span>Diretores</span>
                </a>
                <a href="<?= url('semed/coordinators') ?>" class="semed-nav-btn <?= strpos($_SERVER['REQUEST_URI'], 'semed/coordinators') !== false ? 'active' : '' ?>">
                    <i class="fas fa-user-tie"></i>
                    <span>Coordenadores</span>
                </a>
                <a href="<?= url('semed/plannings') ?>" class="semed-nav-btn <?= strpos($_SERVER['REQUEST_URI'], 'semed/plannings') !== false ? 'active' : '' ?>">
                    <i class="fas fa-file-alt"></i>
                    <span>Planejamentos</span>
                </a>
                <a href="<?= url('semed/reports') ?>" class="semed-nav-btn <?= strpos($_SERVER['REQUEST_URI'], 'semed/reports') !== false ? 'active' : '' ?>">
                    <i class="fas fa-file-invoice"></i>
                    <span>Relatórios</span>
                </a>
            <?php elseif ($role === 'coordinator'): ?>
                <a href="<?= url('school/dashboard') ?>" class="semed-nav-btn <?= strpos($_SERVER['REQUEST_URI'], 'school/dashboard') !== false ? 'active' : '' ?>">
                    <i class="fas fa-th-large"></i>
                    <span>Painel da Escola</span>
                </a>
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
                
                <?php if(in_array(auth()['role'], ['semed', 'professor', 'coordinator'])): ?>
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
