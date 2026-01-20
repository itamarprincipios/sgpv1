<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="dashboard-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Editar Planejamento</h2>
        <a href="<?= url('school/plannings') ?>" class="btn btn-secondary" style="width: auto; background-color: #6c757d;">Voltar</a>
    </div>
</div>

<div class="content-row">
    <div class="upload-section" style="width: 100%; max-width: 800px; margin: 0 auto;">
        <form action="<?= url('school/planning/update') ?>" method="POST">
            <input type="hidden" name="id" value="<?= $planning['id'] ?>">
            
            <div class="form-group">
                <label for="name">Nome do Planejamento</label>
                <input type="text" name="name" id="name" required value="<?= htmlspecialchars($planning['name']) ?>" class="form-control">
            </div>
            
            <div class="form-group">
                <label for="description">Descrição do Período</label>
                <input type="text" name="description" id="description" required value="<?= htmlspecialchars($planning['description']) ?>" class="form-control">
            </div>

            <div class="form-group">
                <label for="start_date">Início da Vigência</label>
                <input type="date" name="start_date" id="start_date" required value="<?= date('Y-m-d', strtotime($planning['start_date'])) ?>" class="form-control">
                <small style="color: #666;">O sistema recalculará o prazo final e abertura baseado nesta data.</small>
            </div>

            <div class="form-group" style="display: flex; align-items: center; gap: 10px; margin-top: 10px;">
                <input type="checkbox" name="is_physical_education" id="is_physical_education" value="1" <?= $planning['is_physical_education'] ? 'checked' : '' ?> style="width: 18px; height: 18px;">
                <label for="is_physical_education" style="margin: 0; cursor: pointer;">Este planejamento é exclusivo para <strong>Educação Física</strong>?</label>
            </div>
            
            <div class="form-group" style="display: flex; align-items: center; gap: 10px; margin-top: 10px;">
                <input type="checkbox" name="is_monitor" id="is_monitor" value="1" <?= ($planning['is_monitor'] ?? 0) ? 'checked' : '' ?> style="width: 18px; height: 18px;">
                <label for="is_monitor" style="margin: 0; cursor: pointer;">Este planejamento é exclusivo para <strong>Professor Monitor</strong>?</label>
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top: 20px;">Salvar Alterações</button>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
