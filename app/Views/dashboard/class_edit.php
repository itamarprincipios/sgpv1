<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="dashboard-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Editar Turma</h2>
        <a href="<?= url('school/classes') ?>" class="btn btn-secondary" style="width: auto; background-color: #6c757d;">Voltar</a>
    </div>
</div>

<div class="content-row">
    <div class="upload-section" style="width: 100%; max-width: 600px; margin: 0 auto;">
        <form action="<?= url('school/class/update') ?>" method="POST">
            <input type="hidden" name="id" value="<?= $class['id'] ?>">
            
            <div class="form-group">
                <label for="name">Nome da Turma</label>
                <input type="text" name="name" id="name" required value="<?= htmlspecialchars($class['name']) ?>" class="form-control">
            </div>
            
            <button type="submit" class="btn btn-primary" style="margin-top: 20px;">Salvar Alterações</button>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
