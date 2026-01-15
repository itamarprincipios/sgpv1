<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="content-row">
    <div class="upload-section">
        <h3>Cadastrar Nova Turma</h3>
        <form action="<?= url('school/class/store') ?>" method="POST">
            <?php if(count($schools) > 1): ?>
                <div class="form-group">
                    <label>Escola</label>
                    <select name="school_id" required class="form-control" style="width: 100%; margin-bottom: 10px;">
                        <?php foreach($schools as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php else: ?>
                <input type="hidden" name="school_id" value="<?= $schools[0]['id'] ?>">
            <?php endif; ?>

            <div class="form-group">
                <label>Nome da Turma</label>
                <input type="text" name="name" required placeholder="Ex: 5º Ano A">
            </div>
            <button type="submit" class="btn btn-primary">Salvar Turma</button>
        </form>
    </div>
    <div class="list-section">
        <h3>Turmas Cadastradas</h3>
        <?php $showSchool = isset($schools) && count($schools) > 1; ?>
        <ul style="list-style: none;">
            <?php foreach($classes as $c): ?>
                <li style="padding: 10px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between;">
                    <div>
                        <span style="font-weight: bold;">
                            <?php if($showSchool): ?>
                                <span class="badge" style="background: #e2e8f0; color: #333; font-size: 0.8em; margin-right: 5px;"><?= htmlspecialchars($c['school_name'] ?? '') ?></span>
                            <?php endif; ?>
                            <?= htmlspecialchars($c['name']) ?>
                        </span>
                        <div style="font-size: 0.85rem; color: #666; margin-top: 4px;">
                            <?php if($c['professor_name']): ?>
                                <i class="fas fa-chalkboard-teacher"></i> Titular: <?= htmlspecialchars($c['professor_name']) ?>
                            <?php else: ?>
                                <span style="color: #e74c3c;"><i class="fas fa-exclamation-circle"></i> Sem professor titular</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <a href="<?= url('school/class/edit?id='.$c['id']) ?>" class="btn-icon" title="Editar"><i class="fas fa-edit"></i></a>
                        <a href="<?= url('school/class/delete?id='.$c['id']) ?>" class="btn-icon" style="color: red;" title="Excluir" onclick="return confirm('ATENÇÃO: Tem certeza que deseja excluir esta turma? Os professores vinculados ficarão sem turma.')"><i class="fas fa-trash"></i></a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
