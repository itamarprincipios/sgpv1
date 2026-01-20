<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="dashboard-header">
    <h2>Editar Professor</h2>
    <a href="<?= url('school/professors') ?>" class="btn btn-secondary">Voltar</a>
</div>

<div class="content-row">
    <div class="upload-section" style="width: 100%; max-width: 600px; margin: 0 auto;">
        <form action="<?= url('school/professor/update') ?>" method="POST">
            <input type="hidden" name="id" value="<?= $professor['id'] ?>">
            
            <div class="form-group">
                <label>Nome Completo</label>
                <input type="text" name="name" required value="<?= htmlspecialchars($professor['name']) ?>">
            </div>
            
            <div class="form-group">
                <label>E-mail (Login)</label>
                <input type="email" name="email" required value="<?= htmlspecialchars($professor['email']) ?>">
            </div>
            
            <div class="form-group">
                <label>WhatsApp</label>
                <input type="text" name="whatsapp" value="<?= htmlspecialchars($professor['whatsapp']) ?>">
            </div>
            
            <div class="form-group">
                <label>Vincular a Turma</label>
                <select name="class_id">
                    <option value="">Selecione uma turma...</option>
                    <?php foreach($classes as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $professor['class_id'] == $c['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="display: flex; align-items: center; gap: 10px; margin-top: 10px;">
                <input type="checkbox" name="is_physical_education" id="prof_is_pe" value="1" <?= $professor['is_physical_education'] ? 'checked' : '' ?> style="width: 18px; height: 18px;">
                <label for="prof_is_pe" style="margin: 0; cursor: pointer;">Professor de Educação Física?</label>
            </div>
            <div class="form-group" style="display: flex; align-items: center; gap: 10px; margin-top: 10px;">
                <input type="checkbox" name="is_monitor" id="prof_is_monitor" value="1" <?= ($professor['is_monitor'] ?? 0) ? 'checked' : '' ?> style="width: 18px; height: 18px;">
                <label for="prof_is_monitor" style="margin: 0; cursor: pointer;">Professor Monitor?</label>
            </div>
            
            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
