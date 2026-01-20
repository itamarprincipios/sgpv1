<?php require __DIR__ . '/../layouts/header.php'; ?>

<style>
    .settings-header {
        background: linear-gradient(135deg, #475569 0%, #334155 100%);
        color: white;
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 30px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .settings-card {
        background: #fff;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border: 1px solid #f1f5f9;
        margin-bottom: 25px;
        transition: transform 0.2s;
    }
    .settings-card:hover {
        transform: translateY(-2px);
    }
    .settings-section-title {
        color: #1e293b;
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .danger-zone {
        border-left: 4px solid #ef4444;
        background: #fef2f2;
    }
    .info-zone {
        border-left: 4px solid #3b82f6;
    }
    .action-btn {
        width: 100%;
        padding: 12px;
        border-radius: 8px;
        font-weight: 600;
        text-align: center;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        font-size: 1rem;
    }
    .btn-danger-custom {
        background-color: #ef4444;
        color: white;
    }
    .btn-danger-custom:hover {
        background-color: #dc2626;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }
    .btn-primary-custom {
        background-color: #3b82f6;
        color: white;
    }
    .btn-primary-custom:hover {
        background-color: #2563eb;
    }
    .btn-secondary-custom {
        background-color: #64748b;
        color: white;
    }
    .btn-secondary-custom:hover {
        background-color: #475569;
    }
    .settings-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
        margin-top: 20px;
    }
</style>

<div class="settings-header">
    <h1>⚙️ Configurações do Sistema</h1>
    <p>Manutenção avançada, controle de ano letivo e acesso rápido a ferramentas administrativas.</p>
</div>

<div class="settings-grid">
    <!-- Card: Ano Letivo (Danger Zone) -->
    <div class="settings-card danger-zone">
        <div class="settings-section-title">
            <i class="fas fa-exclamation-triangle" style="color: #ef4444;"></i>
            Manutenção de Ano Letivo
        </div>
        <p style="color: #64748b; margin-bottom: 20px; line-height: 1.5;">
            Esta ação irá desvincular TODAS as turmas dos professores atuais. Utilize esta função apenas ao final do ano letivo para preparar o sistema para o próximo ciclo.<br>
            <strong>Atenção: O histórico de planejamentos será preservado.</strong>
        </p>
        <form action="<?= url('admin/reset-year') ?>" method="POST" onsubmit="return confirm('ATENÇÃO CRÍTICA:\n\nIsso irá remover TODOS os vínculos de professores com suas turmas atuais.\nEsta ação NÃO pode ser desfeita.\n\nTem certeza absoluta que deseja iniciar um novo ano letivo?');">
            <button type="submit" class="action-btn btn-danger-custom">
                <i class="fas fa-calendar-times"></i> Iniciar Novo Ano Letivo
            </button>
        </form>
    </div>

    <!-- Card: Acesso Rápido -->
    <div class="settings-card info-zone">
        <div class="settings-section-title">
            <i class="fas fa-rocket" style="color: #3b82f6;"></i>
            Acesso Rápido
        </div>
        <p style="color: #64748b; margin-bottom: 20px;">
            Atalhos para ferramentas de monitoramento e auditoria do sistema.
        </p>
        
        <div style="display: flex; flex-direction: column; gap: 15px;">
            <a href="<?= url('admin/reports') ?>" class="action-btn btn-primary-custom">
                <i class="fas fa-chart-bar"></i> Ver Relatórios Gerenciais
            </a>
            
            <a href="<?= url('admin/history') ?>" class="action-btn btn-secondary-custom">
                <i class="fas fa-archive"></i> Banco de Planejamentos (Histórico)
            </a>
        </div>
    </div>
</div>

<!-- IANNE Widget for admin help -->
<?php require __DIR__ . '/../partials/superadmin_ai_widget.php'; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
