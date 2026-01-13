<?php require __DIR__ . '/../layouts/header.php'; ?>

<!-- CSS Específico Reuse -->
<style>
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }
    
    .dashboard-header h2 {
        font-size: 1.75rem;
        color: #333;
        margin: 0;
        font-weight: 700;
    }

    .list-section {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        padding: 25px;
        margin-bottom: 30px;
        border: 1px solid #f0f0f0;
    }

    .filter-container {
        display: flex;
        gap: 20px;
        align-items: flex-end;
        flex-wrap: wrap; 
    }

    .filter-group {
        flex: 1;
        min-width: 200px;
        display: flex;
        flex-direction: column;
    }

    .filter-label {
        font-size: 0.75rem;
        color: #8898aa;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .filter-select {
        width: 100%;
        padding: 10px 15px;
        font-size: 0.95rem;
        color: #495057;
        background-color: #f8f9fa;
        background-clip: padding-box;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        height: 45px;
    }

    .filter-select:focus {
        background-color: #fff;
        border-color: #667eea;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .btn-filter, .btn-clear {
        height: 45px;
        border-radius: 6px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: none;
        padding: 0 20px;
        cursor: pointer;
    }

    .btn-filter {
        background-color: #667eea;
        color: #fff;
    }
    .btn-filter:hover {
        background-color: #5a67d8;
        transform: translateY(-1px);
    }

    .btn-clear {
        background-color: #f8f9fa;
        color: #6c757d;
        border: 1px solid #e9ecef;
    }
    .btn-clear:hover {
        background-color: #e2e6ea;
        color: #333;
    }

    /* Table Styles */
    .data-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .data-table th {
        background: #f8f9fa;
        padding: 15px;
        color: #8898aa;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e9ecef;
        text-align: left;
    }
    
    .data-table td {
        padding: 15px;
        border-bottom: 1px solid #f0f0f0;
        vertical-align: middle;
        font-size: 0.9rem;
        color: #525f7f;
    }
    
    .data-table tr:last-child td {
        border-bottom: none;
    }
    
    .data-table tr:hover td {
        background-color: #fcfcfc;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        white-space: nowrap;
    }

    /* Cores de Status (mesmas do dashboard) */
    .status-aprovado { background: #e6fffa; color: #28a745; border: 1px solid #b2f5ea; }
    .status-ajustado { background: #fffaf0; color: #ffc107; border: 1px solid #fbd38d; }
    .status-rejeitado { background: #fff5f5; color: #dc3545; border: 1px solid #feb2b2; }
    .status-enviado { background: #ebf8ff; color: #007bff; border: 1px solid #bee3f8; }
    .status-atrasado { background: #fdfdfe; color: #6c757d; border: 1px solid #e2e8f0; }

    .btn-view-file {
        background: #fff;
        border: 1px solid #dee2e6;
        color: #495057;
        padding: 5px 12px;
        border-radius: 4px;
        font-size: 0.8rem;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.2s;
        text-decoration: none;
        font-weight: 600;
    }
    .btn-view-file:hover {
        background: #f8f9fa;
        border-color: #adb5bd;
        color: #212529;
    }

</style>

<div class="main-container">
    
    <!-- Header -->
    <div class="dashboard-header">
        <div>
            <h2>Planejamentos da Rede</h2>
            <p class="text-muted mb-0">Acompanhamento de entregas de Educação Física</p>
        </div>
        <div>
            <a href="<?= url('supervisor-edfis/dashboard') ?>" class="btn btn-secondary px-4" style="border-radius: 6px; font-weight: 500;">
                <i class="fas fa-arrow-left me-2"></i> Voltar ao Painel
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="list-section">
        <form method="GET" action="<?= url('supervisor-edfis/plannings') ?>" class="filter-container">
            
            <!-- Escola -->
            <div class="filter-group" style="flex: 2;">
                <label for="school_id" class="filter-label">Filtrar por Escola</label>
                <select name="school_id" id="school_id" class="filter-select cursor-pointer">
                    <option value="">Todas as Escolas</option>
                    <?php foreach ($schools as $school): ?>
                        <option value="<?= $school['id'] ?>" <?= (isset($_GET['school_id']) && $_GET['school_id'] == $school['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($school['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Bimestre -->
            <div class="filter-group">
                <label for="period_name" class="filter-label">Bimestre</label>
                <select name="period_name" id="period_name" class="filter-select cursor-pointer">
                    <option value="">Todos</option>
                    <?php foreach ($periods as $period): ?>
                        <option value="<?= htmlspecialchars($period['name']) ?>" <?= (isset($_GET['period_name']) && $_GET['period_name'] == $period['name']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($period['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

             <!-- Status -->
             <div class="filter-group">
                <label for="status" class="filter-label">Status</label>
                <select name="status" id="status" class="filter-select cursor-pointer">
                    <option value="">Todos os Status</option>
                    <option value="enviado" <?= (isset($_GET['status']) && $_GET['status'] == 'enviado') ? 'selected' : '' ?>>Enviado</option>
                    <option value="aprovado" <?= (isset($_GET['status']) && $_GET['status'] == 'aprovado') ? 'selected' : '' ?>>Aprovado</option>
                    <option value="ajustado" <?= (isset($_GET['status']) && $_GET['status'] == 'ajustado') ? 'selected' : '' ?>>Ajustes</option>
                    <option value="rejeitado" <?= (isset($_GET['status']) && $_GET['status'] == 'rejeitado') ? 'selected' : '' ?>>Devolvido</option>
                    <option value="atrasado" <?= (isset($_GET['status']) && $_GET['status'] == 'atrasado') ? 'selected' : '' ?>>Atrasado</option>
                </select>
            </div>

            <!-- Botão Filtrar -->
            <div style="padding-bottom: 2px;">
                <button type="submit" class="btn-filter">
                    <i class="fas fa-search"></i> Filtrar
                </button>
            </div>

            <!-- Botão Limpar -->
            <div style="padding-bottom: 2px;">
                 <?php if(!empty($_GET['school_id']) || !empty($_GET['period_name']) || !empty($_GET['status'])): ?>
                    <a href="<?= url('supervisor-edfis/plannings') ?>" class="btn-clear text-decoration-none">
                        <i class="fas fa-times"></i> Limpar
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Tabela de Planejamentos -->
    <div class="list-section p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="padding-left: 25px;">Data Env.</th>
                        <th>Escola</th>
                        <th>Professor</th>
                        <th>Planejamento</th>
                        <th class="text-center">Bim.</th>
                        <th class="text-center">Status</th>
                        <th class="text-right" style="padding-right: 25px; text-align: right;">Documento</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($plannings)): ?>
                        <tr>
                            <td colspan="7" class="text-center p-5 text-muted">
                                <i class="fas fa-file-excel fa-2x mb-3 d-block opacity-50"></i>
                                Nenhum planejamento encontrado com os filtros selecionados.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($plannings as $plan): ?>
                            <tr>
                                <td style="padding-left: 25px; font-family: monospace; color: #666;">
                                    <?= date('d/m/Y', strtotime($plan['submitted_at'])) ?>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($plan['school_name']) ?></strong>
                                </td>
                                <td>
                                    <?= htmlspecialchars($plan['professor_name']) ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($plan['title']) ?>
                                </td>
                                <td class="text-center">
                                    <span style="background:#edf2f7; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">
                                        <?= preg_replace('/[^0-9]/', '', $plan['period_name'] ?? '0') ?>º
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="status-badge status-<?= strtolower($plan['status']) ?>">
                                        <?= ucfirst($plan['status']) ?>
                                    </span>
                                </td>
                                <td style="padding-right: 25px; text-align: right;">
                                    <a href="<?= url('uploads/' . $plan['file_path']) ?>" target="_blank" class="btn-view-file">
                                        <i class="fas fa-download"></i> Ver Arquivo
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
