<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="list-section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2><i class="fas fa-file-alt"></i> Meus Planejamentos</h2>
        <a href="<?= url('school/planning/create') ?>" class="btn btn-primary" style="width: auto;">
            <i class="fas fa-plus"></i> Novo Planejamento
        </a>
    </div>
    
    <?php $showSchool = isset($schools) && count($schools) > 1; ?>
    
    <table class="data-table">
        <thead>
            <tr>
               <?php if($showSchool): ?><th>Escola</th><?php endif; ?>
               <th>Nome</th>
               <th>Descrição/Período</th>
               <th>Prazo Limite</th>
               <th>Área de envios</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($plannings)): ?>
                <tr><td colspan="<?= $showSchool ? 5 : 4 ?>">Nenhum planejamento criado.</td></tr>
            <?php else: ?>
                <?php foreach($plannings as $p): ?>
                    <tr>
                        <?php if($showSchool): ?>
                            <td><small class="badge" style="background: #e2e8f0; color: #333;"><?= htmlspecialchars($p['school_name']) ?></small></td>
                        <?php endif; ?>
                        <td><?= htmlspecialchars($p['name']) ?></td>
                        <td><?= htmlspecialchars($p['description']) ?></td>
                        <td><?= date('d/m/Y', strtotime($p['deadline'])) ?></td>
                        <td style="display: flex; gap: 20px; align-items: center;">
                            <a href="<?= url('school/planning/view?id=' . $p['id']) ?>" class="btn btn-primary" style="width: auto; padding: 5px 15px; font-size: 0.85rem;">
                                <i class="fas fa-list"></i> Controle de Envios
                            </a>
                            <div style="display: flex; gap: 10px; border-left: 1px solid #ddd; padding-left: 15px;">
                                <a href="<?= url('school/planning/edit?id=' . $p['id']) ?>" class="btn-icon" title="Editar"><i class="fas fa-edit"></i></a>
                                <a href="<?= url('school/planning/delete?id=' . $p['id']) ?>" class="btn-icon" style="color: red;" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este planejamento? Todos os envios relacionados também serão afetados.')"><i class="fas fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
