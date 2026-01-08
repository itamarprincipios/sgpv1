<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="dashboard-header">
    <h2>Editar Coordenador</h2>
    <a href="<?= url('school/dashboard?tab=coordinators') ?>" class="btn btn-secondary" style="width: auto;">Voltar</a>
</div>

<div class="upload-section" style="max-width: 600px; margin: 0 auto;">
    <form action="<?= url('school/coordinator/update') ?>" method="POST">
        <input type="hidden" name="id" value="<?= $coordinator['id'] ?>">
        <div class="form-group">
            <label>Nome Completo</label>
            <input type="text" name="name" value="<?= htmlspecialchars($coordinator['name']) ?>" required>
        </div>
        <div class="form-group">
            <label>E-mail (Login)</label>
            <input type="email" name="email" value="<?= htmlspecialchars($coordinator['email']) ?>" required>
        </div>
        <div class="form-group">
            <label>Escola</label>
            <select name="school_id" required>
                <?php foreach($schools as $school): ?>
                    <option value="<?= $school['id'] ?>" <?= ($coordinator['school_id'] == $school['id']) ? 'selected' : '' ?>><?= htmlspecialchars($school['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>WhatsApp</label>
            <input type="text" name="whatsapp" value="<?= htmlspecialchars($coordinator['whatsapp'] ?? '') ?>">
        </div>
        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
