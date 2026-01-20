<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="dashboard-header" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; padding: 30px; border-radius: 12px; margin-bottom: 30px;">
    <h1>🏢 Gestão de Usuários SEMED</h1>
    <p>Visualize e gerencie os técnicos e administradores da SEMED.</p>
</div>

<!-- Tip Block -->
<div style="background: #eff6ff; border-left: 4px solid #3b82f6; color: #1e40af; padding: 15px; border-radius: 4px; margin-bottom: 30px;">
    <p style="margin: 0;"><i class="fas fa-info-circle"></i> Usuários SEMED podem estar vinculados a múltiplas escolas ou ter visão global.</p>
</div>

<div class="list-section">
    <h3 style="margin-bottom: 20px; color: #1e293b;">Usuários SEMED Cadastrados</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Escolas Vinculadas</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($semedUsers)): ?>
                <tr><td colspan="3">Nenhum usuário SEMED encontrado.</td></tr>
            <?php else: ?>
                <?php foreach($semedUsers as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['name']) ?></td>
                        <td>
                            <?php 
                                echo htmlspecialchars($u['school_name'] ?? '-');
                            ?>
                        </td>
                        <td>
                            <a href="<?= url('admin/user/reset-password?id='.$u['id']) ?>" class="btn-icon" title="Resetar Senha para '123456'" onclick="return confirm('Tem certeza que deseja resetar a senha deste usuário para 123456?')"><i class="fas fa-key"></i></a>
                            <a href="<?= url('admin/user/delete?id='.$u['id']) ?>" class="btn-icon" style="color: red;" title="Excluir Usuário" onclick="return confirm('Tem certeza que deseja excluir este usuário permanentemente?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
