<?php require __DIR__ . '/../layouts/header.php'; ?>

<style>
    .registration-hero {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%); /* Green theme for Cadastros */
        padding: 40px 30px;
        border-radius: 20px;
        margin-bottom: 30px;
        box-shadow: 0 10px 40px rgba(16, 185, 129, 0.2);
        color: white;
    }
    
    .registration-hero h1 {
        font-size: 2.2rem;
        font-weight: 700;
        margin: 0 0 10px 0;
        text-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .registration-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }
    
    .reg-card {
        background: white;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
        text-decoration: none;
        color: inherit;
    }
    
    .reg-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .reg-card::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: var(--card-color);
    }
    
    .reg-card.blue { --card-color: #3b82f6; }
    .reg-card.purple { --card-color: #8b5cf6; }
    .reg-card.orange { --card-color: #f97316; }
    .reg-card.green { --card-color: #10b981; }
    
    .reg-icon-wrapper {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 15px;
        background: linear-gradient(135deg, var(--card-color), white); /* Subtle gradient */
        background: rgba(var(--card-rgb), 0.1); /* Fallback if variable usage was complex, keeping simple */
        color: var(--card-color);
        background-color: #f3f4f6; /* simple bg to start */
    }
    
    .reg-card.blue .reg-icon-wrapper { background: #eff6ff; color: #3b82f6; }
    .reg-card.purple .reg-icon-wrapper { background: #f5f3ff; color: #8b5cf6; }
    .reg-card.orange .reg-icon-wrapper { background: #ffedd5; color: #f97316; }
    .reg-card.green .reg-icon-wrapper { background: #d1fae5; color: #10b981; }

    .reg-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1f2937;
        line-height: 1.2;
    }
    
    .reg-label {
        font-size: 0.9rem;
        color: #6b7280;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 5px;
    }
    
    .action-btn {
        margin-top: 15px;
        padding: 10px;
        border-radius: 8px;
        text-align: center;
        background: #f9fafb;
        color: #4b5563;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.2s;
        border: 1px solid #e5e7eb;
    }
    
    .reg-card:hover .action-btn {
        background: var(--card-color);
        color: white;
        border-color: var(--card-color);
    }
    
    .nav-shortcuts {
        display: flex;
        gap: 15px;
        margin-bottom: 30px;
        flex-wrap: wrap;
    }
    
    .shortcut-btn {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 20px;
        background: white;
        border-radius: 10px;
        text-decoration: none;
        color: #374151;
        font-weight: 600;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        transition: all 0.2s;
        border: 1px solid #e5e7eb;
    }
    
    .shortcut-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        border-color: #10b981;
        color: #10b981;
    }
    
    .shortcut-icon {
        color: #10b981;
    }
</style>

<div class="main-container">
    <div class="registration-hero">
        <div style="display: flex; align-items: center; gap: 20px;">
            <div style="background: rgba(255,255,255,0.2); width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-folder-open" style="font-size: 28px;"></i>
            </div>
            <div>
                <h1>Painel de Cadastros</h1>
                <p style="opacity: 0.9;">Gerencie escolas, colaboradores e departamentos da rede.</p>
            </div>
        </div>
    </div>

    <!-- Shortcut Navigation (Optional, duplicated in cards but good for quick access without stats context) 
         Actually, let's make the cards themselves clickable to the list pages as requested ("para dentro... vamos mandar os botoes").
         So the cards act as the buttons.
    -->
    
    <div class="nav-shortcuts">
        <?php if (isAdminSemed()): ?>
        <a href="<?= url('admin/schools') ?>" class="shortcut-btn">
            <i class="fas fa-school shortcut-icon"></i> Escolas
        </a>
        <?php endif; ?>
        <a href="<?= url('semed/coordinators') ?>" class="shortcut-btn">
            <i class="fas fa-user-tie shortcut-icon"></i> Coordenadores
        </a>
        <a href="<?= url('semed/directors') ?>" class="shortcut-btn">
            <i class="fas fa-user-check shortcut-icon"></i> Diretores
        </a>
        <?php if (isAdminSemed()): ?>
        <a href="<?= url('admin/semed-users') ?>" class="shortcut-btn">
            <i class="fas fa-users-cog shortcut-icon"></i> DEAPS
        </a>
        <?php endif; ?>
    </div>

    <div class="registration-grid">
        <?php 
            // Define routes based on role
            $schoolsUrl = isAdminSemed() ? url('adminsemed/escolas') : url('admin/schools'); // Note: Team uses admin/schools? Or semed/schools? 
            // Original code had: admin/schools for schools. For Team, probably restricted in AdminController? 
            // Let's stick to what was there for team (admin/schools) or check if we need to change it.
            // Wait, existing code: url('admin/schools')
            // Plan: AdminSemed -> adminsemed/escolas. Team -> admin/schools (filtered inside AdminController)
            
            $directorsUrl = isAdminSemed() ? url('adminsemed/diretores') : url('semed/directors');
            $coordinatorsUrl = isAdminSemed() ? url('adminsemed/coordenadores') : url('semed/coordinators');
            $teamUrl = url('adminsemed/equipe'); // Exclusive to Admin
        ?>

        <?php if (isAdminSemed()): ?>
        <!-- Schools Card - Only Admin SEMED -->
        <a href="<?= $schoolsUrl ?>" class="reg-card blue">
            <div class="reg-icon-wrapper">
                <i class="fas fa-school"></i>
            </div>
            <div>
                <div class="reg-value"><?= $stats['total_schools'] ?></div>
                <div class="reg-label">Escolas Ativas</div>
            </div>
            <div class="action-btn">Gerenciar Escolas <i class="fas fa-arrow-right" style="margin-left:5px; font-size: 0.8em;"></i></div>
        </a>
        <?php endif; ?>

        <!-- Directors Card -->
        <a href="<?= $directorsUrl ?>" class="reg-card purple">
            <div class="reg-icon-wrapper">
                <i class="fas fa-user-check"></i>
            </div>
            <div>
                <div class="reg-value"><?= $stats['total_directors'] ?></div>
                <div class="reg-label">Diretores</div>
            </div>
            <div class="action-btn">Gerenciar Diretores <i class="fas fa-arrow-right" style="margin-left:5px; font-size: 0.8em;"></i></div>
        </a>

        <!-- Coordinators Card -->
        <a href="<?= $coordinatorsUrl ?>" class="reg-card orange">
            <div class="reg-icon-wrapper">
                <i class="fas fa-user-tie"></i>
            </div>
            <div>
                <div class="reg-value"><?= $stats['total_coordinators'] ?></div>
                <div class="reg-label">Coordenadores</div>
            </div>
            <div class="action-btn">Gerenciar Coordenadores <i class="fas fa-arrow-right" style="margin-left:5px; font-size: 0.8em;"></i></div>
        </a>

        <?php if (isAdminSemed()): ?>
        <!-- DEAPS Users Card - Only Admin SEMED -->
        <a href="<?= $teamUrl ?>" class="reg-card green">
            <div class="reg-icon-wrapper">
                <i class="fas fa-users-cog"></i>
            </div>
            <div>
                <div class="reg-value"><?= $stats['total_semed'] ?></div>
                <div class="reg-label">Equipe DEAPS</div>
            </div>
            <div class="action-btn">Gerenciar Equipe <i class="fas fa-arrow-right" style="margin-left:5px; font-size: 0.8em;"></i></div>
        </a>
        <?php endif; ?>
    </div>
</div>

<script>
    // Add active class to header link if this page is active (handled by PHP in header usually, but good to ensure)
</script>
