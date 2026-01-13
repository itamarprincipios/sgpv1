<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="main-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="<?= url('supervisor-edfis/dashboard') ?>" class="btn btn-primary mb-2">
                <i class="fas fa-arrow-left"></i> Voltar ao Painel
            </a>
            <h1><i class="fas fa-clock"></i> Relatório de Pontualidade</h1>
            <p class="text-muted">Acompanhamento de prazos de entrega dos professores de Educação Física</p>
        </div>
        <button onclick="window.print()" class="btn btn-secondary">
            <i class="fas fa-print"></i> Imprimir
        </button>
    </div>

    <!-- Filtro de Escola -->
    <div class="card p-3 mb-4">
        <form method="GET" action="<?= url('supervisor-edfis/punctuality_report') ?>" class="d-flex align-items-end gap-3">
            <div class="flex-grow-1">
                <label for="school_id" class="form-label fw-bold">Filtrar por Escola:</label>
                <select name="school_id" id="school_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Todas as Escolas</option>
                    <?php foreach ($schools as $school): ?>
                        <option value="<?= $school['id'] ?>" <?= (isset($schoolIdFilter) && $schoolIdFilter == $school['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($school['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if (!empty($schoolIdFilter)): ?>
                <a href="<?= url('supervisor-edfis/punctuality_report') ?>" class="btn btn-outline-danger">
                    <i class="fas fa-times"></i> Limpar Filtro
                </a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card p-4">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Professor</th>
                        <th>Escola</th>
                        <th class="text-center">Total Planejamentos</th>
                        <th class="text-center">Entregues no Prazo</th>
                        <th class="text-center">Taxa de Pontualidade</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($report)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">Nenhum registro encontrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($report as $row): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($row['professor_name']) ?></strong>
                                </td>
                                <td><?= htmlspecialchars($row['school_name']) ?></td>
                                <td class="text-center"><?= $row['total_plannings'] ?></td>
                                <td class="text-center"><?= $row['on_time'] ?></td>
                                <td class="text-center">
                                    <?php 
                                        $rate = $row['punctuality_rate'];
                                        $colorClass = 'bg-danger';
                                        if ($rate >= 90) $colorClass = 'bg-success';
                                        elseif ($rate >= 70) $colorClass = 'bg-warning';
                                    ?>
                                    <span class="badge <?= $colorClass ?>" style="font-size: 0.9rem; color: white; padding: 5px 10px;">
                                        <?= $rate ?>%
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if (!empty($row['whatsapp'])): ?>
                                        <?php
                                            // Formatar número para link do WhatsApp
                                            $phone = preg_replace('/[^0-9]/', '', $row['whatsapp']);
                                            $msg = urlencode("Olá professor(a) {$row['professor_name']}, estou analisando seus prazos de entrega de planejamentos no SGP.");
                                        ?>
                                        <a href="https://wa.me/55<?= $phone ?>?text=<?= $msg ?>" target="_blank" class="btn btn-sm btn-success" title="Enviar cobrança via WhatsApp">
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

<style>
    .badge {
        border-radius: 4px;
        font-weight: 600;
    }
    .bg-success { background-color: #28a745 !important; }
    .bg-warning { background-color: #ffc107 !important; color: #212529 !important; }
    .bg-danger { background-color: #dc3545 !important; }
    
    @media print {
        .btn, .navbar { display: none !important; }
        .main-container { padding: 0; margin: 0; }
        .card { border: none; box-shadow: none; }
    }
</style>
