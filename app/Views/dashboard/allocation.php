<?php require __DIR__ . '/../layouts/header.php'; ?>

<style>
    .allocation-hero {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        padding: 40px 30px;
        border-radius: 20px;
        margin-bottom: 30px;
        box-shadow: 0 10px 40px rgba(245, 158, 11, 0.2);
        color: white;
    }
    
    .allocation-hero h1 {
        font-size: 2.2rem;
        font-weight: 700;
        margin: 0 0 10px 0;
        text-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .filter-section {
        background: white;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        display: flex;
        gap: 15px;
        align-items: center;
        flex-wrap: wrap;
    }

    .filter-label {
        font-weight: 600;
        color: #374151;
    }

    .filter-select {
        padding: 10px 15px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 0.95rem;
        min-width: 250px;
        transition: all 0.2s;
    }

    .filter-select:focus {
        outline: none;
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
    }

    .allocation-table {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .allocation-table table {
        width: 100%;
        border-collapse: collapse;
    }

    .allocation-table thead {
        background: linear-gradient(135deg, #f9fafb, #f3f4f6);
    }

    .allocation-table th {
        padding: 15px 20px;
        text-align: left;
        font-weight: 600;
        color: #374151;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e5e7eb;
    }

    .allocation-table td {
        padding: 15px 20px;
        border-bottom: 1px solid #f3f4f6;
        color: #1f2937;
    }

    .allocation-table tbody tr {
        transition: all 0.2s;
    }

    .allocation-table tbody tr:hover {
        background: #f9fafb;
    }

    .allocation-table tbody tr.vacant {
        background: #fef2f2;
    }

    .allocation-table tbody tr.vacant:hover {
        background: #fee2e2;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .status-badge.assigned {
        background: #d1fae5;
        color: #065f46;
    }

    .status-badge.vacant {
        background: #fee2e2;
        color: #991b1b;
    }

    .vacant-alert {
        background: #fef2f2;
        border-left: 4px solid #dc2626;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .vacant-alert-icon {
        color: #dc2626;
        font-size: 1.5rem;
    }

    .vacant-alert-text {
        color: #991b1b;
        font-weight: 600;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6b7280;
    }

    .empty-state i {
        font-size: 4rem;
        color: #d1d5db;
        margin-bottom: 20px;
    }
</style>

<div class="main-container">
    <div class="allocation-hero">
        <div style="display: flex; align-items: center; gap: 20px;">
            <div style="background: rgba(255,255,255,0.2); width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-users-cog" style="font-size: 28px;"></i>
            </div>
            <div>
                <h1>Controle de Lotação</h1>
                <p style="opacity: 0.9;">Gerencie a alocação de professores titulares nas turmas da rede.</p>
            </div>
        </div>
    </div>

    <!-- DEBUG -->
    <div style="background: #333; color: #fff; padding: 10px; margin-bottom: 20px;">DEBUG: vacantCount = <?= var_export($vacantCount, true) ?></div>
    
    <?php if ($vacantCount > 0): ?>
        <div class="vacant-alert">
            <i class="fas fa-exclamation-triangle vacant-alert-icon"></i>
            <div>
                <div class="vacant-alert-text">
                    <?= $vacantCount ?> turma<?= $vacantCount > 1 ? 's' : '' ?> sem professor titular atribuído
                </div>
                <div style="font-size: 0.9rem; color: #6b7280; margin-top: 4px;">
                    É necessário contratar ou realocar professores para essas turmas.
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="filter-section">
        <span class="filter-label"><i class="fas fa-filter"></i> Filtrar por Escola:</span>
        <select class="filter-select" onchange="window.location.href='<?= url('semed/lotacao') ?>?school_id=' + this.value">
            <option value="">Todas as Escolas</option>
            <?php foreach ($schools as $school): ?>
                <option value="<?= $school['id'] ?>" <?= $selectedSchoolId == $school['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($school['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <?php if ($selectedSchoolId): ?>
            <a href="<?= url('semed/lotacao') ?>" style="color: #6b7280; text-decoration: none; font-size: 0.9rem;">
                <i class="fas fa-times-circle"></i> Limpar Filtro
            </a>
        <?php endif; ?>
    </div>

    <div class="allocation-table">
        <?php if (empty($allocations)): ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>Nenhuma turma encontrada</h3>
                <p>Não há turmas cadastradas <?= $selectedSchoolId ? 'nesta escola' : 'na rede' ?>.</p>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Escola</th>
                        <th>Turma</th>
                        <th>Professor Titular</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allocations as $allocation): ?>
                        <tr class="<?= $allocation['is_vacant'] ? 'vacant' : '' ?>">
                            <td><?= htmlspecialchars($allocation['school_name']) ?></td>
                            <td><strong><?= htmlspecialchars($allocation['class_name']) ?></strong></td>
                            <td>
                                <?php if ($allocation['is_vacant']): ?>
                                    <span style="color: #9ca3af; font-style: italic;">Sem professor atribuído</span>
                                <?php else: ?>
                                    <?= htmlspecialchars($allocation['professor_name']) ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($allocation['is_vacant']): ?>
                                    <span class="status-badge vacant">
                                        <i class="fas fa-exclamation-circle"></i>
                                        Vaga
                                    </span>
                                <?php else: ?>
                                    <span class="status-badge assigned">
                                        <i class="fas fa-check-circle"></i>
                                        Lotado
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
