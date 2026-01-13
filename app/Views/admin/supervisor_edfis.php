<?php 
$pageTitle = 'Gerenciar Supervisoras Ed. Física';
require __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid">
    <div class="row" style="margin-bottom: 2rem;">
        <div class="col-md-12">
            <h2><i class="fas fa-running"></i> Supervisoras SEMED - Educação Física</h2>
            <p class="text-muted">Gerencie as supervisoras pedagógicas de Educação Física</p>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <!-- Botão Cadastrar Nova -->
    <div class="row" style="margin-bottom: 1.5rem;">
        <div class="col-md-12">
            <button class="btn btn-primary" data-toggle="modal" data-target="#modalCadastrar">
                <i class="fas fa-plus"></i> Cadastrar Nova Supervisora
            </button>
            <a href="<?= url('admin/dashboard') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar ao Painel
            </a>
        </div>
    </div>

    <!-- Tabela de Supervisoras -->
    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>WhatsApp</th>
                        <th>Cadastrada em</th>
                        <th style="width: 200px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($supervisors)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-info-circle"></i> Nenhuma supervisora cadastrada ainda.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($supervisors as $sup): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($sup['name']) ?></strong></td>
                                <td><?= htmlspecialchars($sup['email']) ?></td>
                                <td><?= htmlspecialchars($sup['whatsapp'] ?? 'Não informado') ?></td>
                                <td><?= date('d/m/Y', strtotime($sup['created_at'])) ?></td>
                                <td>
                                    <a href="<?= url('admin/supervisor-edfis/edit?id=' . $sup['id']) ?>" 
                                       class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?= url('admin/supervisor-edfis/reset-password?id=' . $sup['id']) ?>" 
                                       class="btn btn-sm btn-info" title="Resetar Senha"
                                       onclick="return confirm('Resetar senha para \\'supervisor123\\'?')">
                                        <i class="fas fa-key"></i>
                                    </a>
                                    <a href="<?= url('admin/supervisor-edfis/delete?id=' . $sup['id']) ?>" 
                                       class="btn btn-sm btn-danger" title="Excluir"
                                       onclick="return confirm('Excluir supervisora?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Cadastrar -->
<div class="modal fade" id="modalCadastrar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?= url('admin/supervisor-edfis/store') ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Cadastrar Supervisora Ed. Física</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nome Completo *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>WhatsApp</label>
                        <input type="text" name="whatsapp" class="form-control" placeholder="Ex: 95991234567">
                    </div>
                    <div class="alert alert-info">
                        <strong>Senha padrão:</strong> supervisor123
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Cadastrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
