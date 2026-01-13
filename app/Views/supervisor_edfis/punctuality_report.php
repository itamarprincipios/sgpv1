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

    .rate-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .rate-excellent { background: #e6fffa; color: #28a745; border: 1px solid #b2f5ea; }
    .rate-good { background: #fffaf0; color: #ffc107; border: 1px solid #fbd38d; }
    .rate-poor { background: #fff5f5; color: #dc3545; border: 1px solid #feb2b2; }

    .btn-whatsapp {
        background: #25D366;
        color: white;
        padding: 6px 12px;
        border-radius: 6px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.85rem;
        font-weight: 600;
        border: none;
        transition: all 0.2s;
    }
    .btn-whatsapp:hover {
        background: #128C7E;
        color: white;
        transform: translateY(-1px);
    }

    @media print {
        .btn, .navbar, .filter-container, .dashboard-header .btn { 
            display: none !important; 
        }
        .main-container { padding: 0; margin: 0; }
        .list-section { border: none; box-shadow: none; }
    }
</style>

<div class="main-container">
    
    <!-- Header -->
    <div class="dashboard-header">
        <div>
            <h2>Relatório de Pontualidade</h2>
            <p class="text-muted mb-0">Acompanhamento de prazos de entrega dos professores de Educação Física</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()" class="btn btn-outline-secondary px-4" style="border-radius: 6px; font-weight: 500;">
                <i class="fas fa-print me-2"></i> Imprimir
            </button>
            <a href="<?= url('supervisor-edfis/dashboard') ?>" class="btn btn-secondary px-4" style="border-radius: 6px; font-weight: 500;">
                <i class="fas fa-arrow-left me-2"></i> Voltar ao Painel
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="list-section">
        <form method="GET" action="<?= url('supervisor-edfis/punctuality_report') ?>" class="filter-container">
            
            <!-- Escola -->
            <div class="filter-group" style="flex: 2;">
                <label for="school_id" class="filter-label">Filtrar por Escola</label>
                <select name="school_id" id="school_id" class="filter-select cursor-pointer">
                    <option value="">Todas as Escolas</option>
                    <?php foreach ($schools as $school): ?>
                        <option value="<?= $school['id'] ?>" <?= (isset($schoolIdFilter) && $schoolIdFilter == $school['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($school['name']) ?>
                        </option>
                    <?php endforeach; ?>
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
                 <?php if(!empty($schoolIdFilter)): ?>
                    <a href="<?= url('supervisor-edfis/punctuality_report') ?>" class="btn-clear text-decoration-none">
                        <i class="fas fa-times"></i> Limpar
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Tabela de Relatório -->
    <div class="list-section p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="padding-left: 25px;">Professor</th>
                        <th>Escola</th>
                        <th class="text-center">Total Planejamentos</th>
                        <th class="text-center">No Prazo</th>
                        <th class="text-center">Taxa de Pontualidade</th>
                        <th class="text-center" style="padding-right: 25px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($report)): ?>
                        <tr>
                            <td colspan="6" class="text-center p-5 text-muted">
                                <i class="fas fa-chart-line fa-2x mb-3 d-block opacity-50"></i>
                                Nenhum registro encontrado com os filtros selecionados.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($report as $row): ?>
                            <tr>
                                <td style="padding-left: 25px;">
                                    <strong><?= htmlspecialchars($row['professor_name']) ?></strong>
                                </td>
                                <td>
                                    <?= htmlspecialchars($row['school_name']) ?>
                                </td>
                                <td class="text-center">
                                    <span style="background:#edf2f7; padding: 4px 10px; border-radius: 4px; font-weight: 600;">
                                        <?= $row['total_plannings'] ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span style="background:#e6fffa; padding: 4px 10px; border-radius: 4px; font-weight: 600; color: #28a745;">
                                        <?= $row['on_time'] ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php 
                                        $rate = $row['punctuality_rate'];
                                        $badgeClass = 'rate-poor';
                                        if ($rate >= 90) $badgeClass = 'rate-excellent';
                                        elseif ($rate >= 70) $badgeClass = 'rate-good';
                                    ?>
                                    <span class="rate-badge <?= $badgeClass ?>">
                                        <?= $rate ?>%
                                    </span>
                                </td>
                                <td class="text-center" style="padding-right: 25px;">
                                    <?php if (!empty($row['whatsapp'])): ?>
                                        <?php
                                            $phone = preg_replace('/[^0-9]/', '', $row['whatsapp']);
                                            $msg = urlencode("Olá professor(a) {$row['professor_name']}, estou analisando seus prazos de entrega de planejamentos no SGP.");
                                        ?>
                                        <a href="https://wa.me/55<?= $phone ?>?text=<?= $msg ?>" target="_blank" class="btn-whatsapp">
                                            <i class="fab fa-whatsapp"></i> Cobrar
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted" style="font-size: 0.8rem;">Sem WhatsApp</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
