<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="list-section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2><i class="fas fa-file-alt"></i> Meus Planejamentos</h2>
        <a href="<?= url('school/planning/create') ?>" class="btn btn-primary" style="width: auto;">
            <i class="fas fa-plus"></i> Novo Planejamento
        </a>
    </div>
    
    <?php $showSchool = isset($schools) && count($schools) > 1; ?>
    
    <div class="tabs-container" style="margin-bottom: 20px; display: flex; gap: 10px;">
        <button class="btn btn-primary" id="tab-active" onclick="filterPlannings('active')" style="border-radius: 20px; padding: 8px 20px;">Ativos (Em Andamento)</button>
        <button class="btn" id="tab-closed" onclick="filterPlannings('closed')" style="background: transparent; border: 1px solid var(--border-color); color: var(--text-color); border-radius: 20px; padding: 8px 20px;">Encerrados (Histórico)</button>
    </div>
    
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
                <tr id="empty-row"><td colspan="<?= $showSchool ? 5 : 4 ?>">Nenhum planejamento encontrado.</td></tr>
            <?php else: ?>
                <tr id="empty-row" style="display: none;"><td colspan="<?= $showSchool ? 5 : 4 ?>">Nenhum planejamento encontrado.</td></tr>
                <?php foreach($plannings as $p): ?>
                    <?php 
                        $isClosed = strtotime($p['deadline']) < strtotime('today'); 
                        $status = $isClosed ? 'closed' : 'active';
                    ?>
                    <tr class="planning-row" data-status="<?= $status ?>">
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

<script>
function filterPlannings(status) {
    const tabActive = document.getElementById('tab-active');
    const tabClosed = document.getElementById('tab-closed');
    
    if (status === 'active') {
        tabActive.className = 'btn btn-primary';
        tabActive.style.background = '';
        tabActive.style.color = '';
        tabActive.style.border = '';
        
        tabClosed.className = 'btn';
        tabClosed.style.background = 'transparent';
        tabClosed.style.border = '1px solid var(--border-color)';
        tabClosed.style.color = 'var(--text-color)';
    } else {
        tabClosed.className = 'btn btn-primary';
        tabClosed.style.background = '';
        tabClosed.style.color = '';
        tabClosed.style.border = '';

        tabActive.className = 'btn';
        tabActive.style.background = 'transparent';
        tabActive.style.border = '1px solid var(--border-color)';
        tabActive.style.color = 'var(--text-color)';
    }

    const rows = document.querySelectorAll('.planning-row');
    let visibleCount = 0;
    rows.forEach(row => {
        if (row.getAttribute('data-status') === status) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    const emptyRow = document.getElementById('empty-row');
    if (emptyRow) {
        if (visibleCount === 0 && rows.length > 0) {
            emptyRow.style.display = '';
            emptyRow.querySelector('td').textContent = status === 'active' ? 'Nenhum planejamento em andamento.' : 'Nenhum planejamento encerrado.';
        } else if (rows.length > 0) {
            emptyRow.style.display = 'none';
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    filterPlannings('active');
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
