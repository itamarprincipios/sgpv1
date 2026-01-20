<?php require __DIR__ . '/../layouts/header.php'; ?>

<style>
    @media (max-width: 768px) {
        .dashboard-header {
            padding: 20px 15px !important;
        }
        
        .dashboard-header h1 {
            font-size: 1.5rem !important;
        }
        
        .stats-grid {
            grid-template-columns: 1fr !important;
        }
        
        .tabs {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .tab-btn {
            white-space: nowrap;
            flex-shrink: 0;
            padding: 8px 12px;
            font-size: 0.85rem;
        }
        
        /* Formulários responsivos */
        form[style*="grid-template-columns"] {
            grid-template-columns: 1fr !important;
            gap: 10px !important;
        }
        
        form button[type="submit"] {
            width: 100%;
        }
    }
</style>

<div class="dashboard-header" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; padding: 30px; border-radius: 12px; margin-bottom: 30px;">
    <h1>🛡️ Painel do Super Admin</h1>
    <p>Controle total do sistema</p>
</div>


<!-- Stats -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px;">
    <div class="stat-card" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-left: 5px solid #2563eb;">
        <h3>Escolas</h3>
        <div style="font-size: 2rem; font-weight: bold;"><?= $stats['schools'] ?></div>
    </div>
    <div class="stat-card" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-left: 5px solid #16a34a;">
        <h3>SEMED</h3>
        <div style="font-size: 2rem; font-weight: bold;"><?= $stats['semed'] ?></div>
    </div>
    <div class="stat-card" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-left: 5px solid #9333ea;">
        <h3>Coordenadores</h3>
        <div style="font-size: 2rem; font-weight: bold;"><?= $stats['coordinators'] ?></div>
    </div>
    <div class="stat-card" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-left: 5px solid #ef4444;">
        <h3>Diretores</h3>
        <div style="font-size: 2rem; font-weight: bold;"><?= $stats['directors'] ?></div>
    </div>
    <div class="stat-card" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-left: 5px solid #ca8a04;">
        <h3>Professores</h3>
        <div style="font-size: 2rem; font-weight: bold;"><?= $stats['professors'] ?></div>
    </div>
</div>

<!-- Tab buttons removed -->

<!-- TAB SEMED -->
<!-- SEMED Tab Removed (Moved to dedicated page) -->
    
    <table class="data-table">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($semedUsers as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['name']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td>
                        <a href="<?= url('admin/user/reset-password?id='.$u['id']) ?>" class="btn-icon" title="Resetar Senha (123456)" onclick="return confirm('Resetar senha para 123456?')"><i class="fas fa-key"></i></a>
                        <a href="<?= url('admin/user/edit?id='.$u['id']) ?>" class="btn-icon" title="Editar Dados/Escolas" style="color: #3b82f6;"><i class="fas fa-edit"></i></a>
                        <a href="<?= url('admin/user/delete?id='.$u['id']) ?>" class="btn-icon" style="color: red;" title="Excluir" onclick="return confirm('Excluir este usuário?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    // No tabs needed anymore
</script>

<!-- IANNE AI Widget -->
<?php require __DIR__ . '/../partials/superadmin_ai_widget.php'; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
