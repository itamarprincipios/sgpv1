<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="dashboard-header">
    <h2>Editar Diretor</h2>
    <a href="<?= url('semed/directors') ?>" class="btn btn-secondary" style="width: auto;">Voltar</a>
</div>

<div class="upload-section" style="max-width: 600px; margin: 0 auto;">
    <form action="<?= url('semed/director/update') ?>" method="POST">
        <input type="hidden" name="id" value="<?= $director['id'] ?>">
        <div class="form-group">
            <label>Nome Completo</label>
            <input type="text" name="name" value="<?= htmlspecialchars($director['name']) ?>" required>
        </div>
        <div class="form-group">
            <label>E-mail (Login)</label>
            <input type="email" name="email" value="<?= htmlspecialchars($director['email']) ?>" required>
        </div>
        <div class="form-group">
            <label>Escola</label>
            <select name="school_id" required>
                <?php foreach($schools as $school): ?>
                    <option value="<?= $school['id'] ?>" <?= ($director['school_id'] == $school['id']) ? 'selected' : '' ?>><?= htmlspecialchars($school['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Telefone/WhatsApp</label>
            <input type="text" name="whatsapp" value="<?= htmlspecialchars($director['whatsapp'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Endereço</label>
            <input type="text" name="address" value="<?= htmlspecialchars($director['address'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="is_vice_director" value="1" <?= !empty($director['is_vice_director']) ? 'checked' : '' ?> style="width: 18px; height: 18px;">
                <span style="font-weight: 600; color: #6366f1;">
                    <i class="fas fa-user-shield" style="color: #6366f1;"></i> Vice-Diretor
                </span>
            </label>
        </div>
        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
