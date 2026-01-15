<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="list-section">
    <h2><i class="fas fa-upload"></i> Últimos Documentos Recebidos</h2>
    
    <?php $showSchool = isset($schools) && count($schools) > 1; ?>
    
    <form method="GET" action="<?= url('school/uploads') ?>" class="filter-container">
        <div class="filter-group">
            <label class="filter-label">Planejamento/Bimestre</label>
            <select name="period_id" class="filter-select" onchange="this.form.submit()">
                <option value="">Todos</option>
                <?php foreach($plannings as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= ($filters['period_id'] == $p['id']) ? 'selected' : '' ?>>
                         <?php if($showSchool) echo '['.htmlspecialchars($p['school_name']).'] '; ?>
                        <?= htmlspecialchars($p['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filter-group">
            <label class="filter-label">Professor</label>
            <select name="professor_id" class="filter-select" onchange="this.form.submit()">
                <option value="">Todos</option>
                <?php foreach($professors as $prof): ?>
                    <option value="<?= $prof['id'] ?>" <?= ($filters['professor_id'] == $prof['id']) ? 'selected' : '' ?>>
                        <?php if($showSchool) echo '['.htmlspecialchars($prof['school_name']).'] '; ?>
                        <?= htmlspecialchars($prof['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filter-group" style="flex: 0 0 150px; min-width: 150px;">
            <label class="filter-label">Status</label>
            <select name="status" class="filter-select" onchange="this.form.submit()">
                <option value="">Todos</option>
                <option value="enviado" <?= ($filters['status'] == 'enviado') ? 'selected' : '' ?>>Enviado</option>
                <option value="aprovado" <?= ($filters['status'] == 'aprovado') ? 'selected' : '' ?>>Aprovado</option>
                <option value="ajustado" <?= ($filters['status'] == 'ajustado') ? 'selected' : '' ?>>Ajustado</option>
                <option value="rejeitado" <?= ($filters['status'] == 'rejeitado') ? 'selected' : '' ?>>Rejeitado</option>
                <option value="atrasado" <?= ($filters['status'] == 'atrasado') ? 'selected' : '' ?>>Atrasado</option>
            </select>
        </div>
        
        <div class="filter-actions">
             <a href="<?= url('school/uploads') ?>" class="btn-filter-clear">
                <i class="fas fa-times"></i> Limpar
             </a>
        </div>
    </form>

    <table class="data-table">
       <thead>
           <tr>
               <?php if($showSchool): ?><th>Escola</th><?php endif; ?>
               <th>Professor</th>
               <th>Turma</th>
               <th>Documento</th>
               <th>Status</th>
               <th>Ação</th>
           </tr>
       </thead>
       <tbody>
           <?php if(empty($documents)): ?>
               <tr><td colspan="<?= $showSchool ? 6 : 5 ?>">Nenhum documento.</td></tr>
           <?php else: ?>
               <?php foreach($documents as $doc): ?>
                   <tr>
                       <?php if($showSchool): ?>
                            <td><small class="badge" style="background: #e2e8f0; color: #333;"><?= htmlspecialchars($doc['school_name']) ?></small></td>
                       <?php endif; ?>
                       <td><?= htmlspecialchars($doc['professor_name']) ?></td>
                       <td>-</td>
                       <td><?= htmlspecialchars($doc['title']) ?></td>
                       <td><span class="status-badge status-<?= $doc['status'] ?>"><?= ucfirst($doc['status']) ?></span></td>
                       <td><a href="<?= url('uploads/' . $doc['file_path']) ?>" target="_blank" class="btn-icon"><i class="fas fa-eye"></i></a></td>
                   </tr>
               <?php endforeach; ?>
           <?php endif; ?>
       </tbody>
    </table>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
