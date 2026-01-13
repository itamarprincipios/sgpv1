<?php 
$pageTitle = 'Editar Supervisora';
require __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid">
    <h2><i class="fas fa-edit"></i> Editar Supervisora Ed. Física</h2>
    
    <div class="card" style="max-width: 600px; margin-top: 2rem;">
        <div class="card-body">
            <form method="POST" action="<?= url('admin/supervisor-edfis/update') ?>">
                <input type="hidden" name="id" value="<?= $supervisor['id'] ?>">
                
                <div class="form-group">
                    <label>Nome Completo *</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($supervisor['name']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($supervisor['email']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label>WhatsApp</label>
                    <input type="text" name="whatsapp" class="form-control" value="<?= htmlspecialchars($supervisor['whatsapp'] ?? '') ?>">
                </div>
                
                <div class="alert alert-info">
                    <strong>Nota:</strong> Para alterar a senha, use o botão "Resetar Senha" na listagem.
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar Alterações
                    </button>
                    <a href="<?= url('admin/supervisor-edfis') ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
