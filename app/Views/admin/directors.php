<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="dashboard-header" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; padding: 30px; border-radius: 12px; margin-bottom: 30px;">
    <h1>🎓 Gestão de Diretores</h1>
    <p>Visualize e gerencie os diretores das escolas.</p>
</div>

<!-- Tip Block -->
<div style="background: #eff6ff; border-left: 4px solid #3b82f6; color: #1e40af; padding: 15px; border-radius: 4px; margin-bottom: 30px;">
    <p style="margin: 0;"><i class="fas fa-info-circle"></i> Para vincular diretores às escolas, utilize a edição de escolas em <strong>Escolas > Editar</strong>.</p>
</div>

<div class="list-section">
    <h3 style="margin-bottom: 20px; color: #1e293b;">Diretores Cadastrados</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Escola (ID/Nome)</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($directors)): ?>
                <tr><td colspan="3">Nenhum diretor encontrado.</td></tr>
            <?php else: ?>
                <?php foreach($directors as $d): ?>
                    <tr>
                        <td><?= htmlspecialchars($d['name']) ?></td>
                        <td>
                            <?php 
                                echo htmlspecialchars($d['school_name'] ?? $d['school_id'] ?? '-');
                            ?>
                        </td>
                        <td>
                            <a href="<?= url('admin/user/reset-password?id='.$d['id']) ?>" class="btn-icon" title="Resetar Senha para '123456'" onclick="return confirm('Tem certeza que deseja resetar a senha deste usuário para 123456?')"><i class="fas fa-key"></i></a>
                            <a href="<?= url('admin/user/delete?id='.$d['id']) ?>" class="btn-icon" style="color: red;" title="Excluir Usuário" onclick="return confirm('Tem certeza que deseja excluir este usuário permanentemente?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
