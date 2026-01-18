<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="dashboard-header">
    <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
        <h2>Relatórios da Rede</h2>
        <div style="display: flex; gap: 10px;">
        <div style="display: flex; gap: 10px;">
            <a href="<?= url('school/reports?type=submissions') ?>" class="btn <?= ($type === 'submissions') ? 'btn-primary' : 'btn-secondary' ?>" style="width: auto;">Envios</a>
            <a href="<?= url('school/reports?type=pendencies') ?>" class="btn <?= ($type === 'pendencies') ? 'btn-primary' : 'btn-secondary' ?>" style="width: auto;">Pendências</a>
            <a href="<?= url('school/reports?type=punctuality') ?>" class="btn <?= ($type === 'punctuality') ? 'btn-primary' : 'btn-secondary' ?>" style="width: auto;">Pontualidade</a>
        </div>
        </div>
    </div>
</div>

<div class="list-section" style="margin-bottom: 20px;">
    <form action="" method="GET" class="filter-container">
        <input type="hidden" name="type" value="<?= $type ?>">
        
        <!-- School Filter Removed for Director Context -->
        <input type="hidden" name="school_id" value="<?= $schoolId ?>">

        <?php if ($schoolId): ?>
        <div class="filter-group">
            <label class="filter-label">Professor</label>
            <select name="professor_id" class="filter-select" onchange="this.form.submit()">
                <option value="">Todos os Professores</option>
                <?php foreach($professors as $prof): ?>
                    <option value="<?= $prof['id'] ?>" <?= ($professorId == $prof['id']) ? 'selected' : '' ?>><?= htmlspecialchars($prof['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>

        <?php if ($professorId): ?>
        <div class="filter-group" style="flex: 0 0 150px; min-width: 150px;">
            <label class="filter-label">Período</label>
            <select name="period" class="filter-select" onchange="this.form.submit()">
                <option value="annual" <?= ($period == 'annual') ? 'selected' : '' ?>>Anual</option>
                <option value="monthly" <?= ($period == 'monthly') ? 'selected' : '' ?>>Mensal (Atual)</option>
                <option value="bimonthly" <?= ($period == 'bimonthly') ? 'selected' : '' ?>>Bimestral</option>
            </select>
        </div>
        <?php endif; ?>

        <div class="filter-actions">
            <button type="button" onclick="window.print()" class="btn btn-secondary" style="width: auto; padding: 0.75rem 1.5rem;">
                <i class="fas fa-print"></i> Imprimir
            </button>
        </div>
    </form>
</div>


<div class="list-section">
    <?php if ($professorId && isset($data['stats'])): ?>
        <?php 
            $stats = $data['stats']; 
            $submissions = $data['submissions'];
        ?>
        <div style="margin-bottom: 30px;">
            <h3>Dashboard de Desempenho do Professor</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 15px;">
                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center; border: 1px solid #ddd;">
                    <div style="font-size: 0.9rem; color: #666;">Total Enviado</div>
                    <div style="font-size: 1.5rem; font-weight: bold; color: #333;"><?= $stats['total_sent'] ?></div>
                </div>
                <div style="background: #e6fffa; padding: 15px; border-radius: 8px; text-align: center; border: 1px solid #b2f5ea;">
                    <div style="font-size: 0.9rem; color: #234e52;">No Prazo</div>
                    <div style="font-size: 1.5rem; font-weight: bold; color: #285e61;"><?= $stats['on_time'] ?></div>
                </div>
                <div style="background: #fff5f5; padding: 15px; border-radius: 8px; text-align: center; border: 1px solid #feb2b2;">
                    <div style="font-size: 0.9rem; color: #742a2a;">Com Atraso</div>
                    <div style="font-size: 1.5rem; font-weight: bold; color: #9b2c2c;"><?= $stats['late_docs'] ?></div>
                </div>
                <div style="background: #f0fff4; padding: 15px; border-radius: 8px; text-align: center; border: 1px solid #9ae6b4;">
                    <div style="font-size: 0.9rem; color: #22543d;">Aprovados</div>
                    <div style="font-size: 1.5rem; font-weight: bold; color: #276749;"><?= $stats['approved'] ?></div>
                </div>
                <div style="background: #fffaf0; padding: 15px; border-radius: 8px; text-align: center; border: 1px solid #fbd38d;">
                    <div style="font-size: 0.9rem; color: #744210;">Com Ajustes</div>
                    <div style="font-size: 1.5rem; font-weight: bold; color: #975a16;"><?= $stats['adjusted'] ?></div>
                </div>
                 <div style="background: #fff5f7; padding: 15px; border-radius: 8px; text-align: center; border: 1px solid #feb2b2;">
                    <div style="font-size: 0.9rem; color: #742a2a;">Reprovados</div>
                    <div style="font-size: 1.5rem; font-weight: bold; color: #c53030;"><?= $stats['rejected'] ?></div>
                </div>
            </div>
        </div>

        <h3>Histórico de Envios Detalhado</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Planejamento</th>
                    <th>Envio</th>
                    <th>Prazo</th>
                    <th>Status</th>
                    <th style="text-align: center;">Pontuação</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($submissions)): ?>
                     <tr><td colspan="5" style="text-align: center; padding: 20px; color: #666;">Nenhum envio encontrado neste período.</td></tr>
                <?php else: ?>
                    <?php foreach($submissions as $sub): ?>
                        <tr>
                            <td><?= htmlspecialchars($sub['period_name']) ?></td>
                            <td>
                                <?= date('d/m/Y H:i', strtotime($sub['submitted_at'])) ?>
                                <?php if(strtotime($sub['submitted_at']) > strtotime($sub['deadline'])): ?>
                                    <span style="font-size: 0.8rem; background: #fff5f5; color: #c53030; padding: 2px 6px; border-radius: 4px; margin-left: 5px;">Atrasado</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d/m/Y', strtotime($sub['deadline'])) ?></td>
                            <td>
                                <span class="status-badge status-<?= $sub['status'] ?>">
                                    <?= ucfirst($sub['status']) ?>
                                </span>
                            </td>
                            <td style="text-align: center; font-weight: bold;">
                                <?= $sub['score_final'] ?? '-' ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

    <?php elseif ($type === 'submissions'): ?>
        <h3>Resumo de Entregas por Professor</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Escola</th>
                    <th>Professor</th>
                    <th style="text-align: center;">Total Enviado</th>
                    <th style="text-align: center;">Aprovados</th>
                    <th style="text-align: center;">Com Atraso</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['school_name']) ?></td>
                        <td><?= htmlspecialchars($row['professor_name']) ?></td>
                        <td style="text-align: center;"><?= $row['total_sent'] ?></td>
                        <td style="text-align: center; color: green;"><?= $row['approved'] ?></td>
                        <td style="text-align: center; color: red;"><?= $row['late_docs'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php elseif ($type === 'pendencies'): ?>
        <h3>Relatório de Planejamentos Pendentes (Atrasados)</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Escola</th>
                    <th>Professor</th>
                    <th>Planejamento</th>
                    <th>Prazo</th>
                    <th style="text-align: center;">Atraso (Dias)</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($data)): ?>
                    <tr><td colspan="5" style="text-align: center; padding: 20px; color: #666;">🎉 Nenhuma pendência encontrada na rede!</td></tr>
                <?php else: ?>
                    <?php foreach($data as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['school_name']) ?></td>
                            <td><?= htmlspecialchars($row['professor_name']) ?></td>
                            <td><?= htmlspecialchars($row['period_name']) ?></td>
                            <td><?= date('d/m/Y', strtotime($row['deadline'])) ?></td>
                            <td style="text-align: center; color: red; font-weight: bold;"><?= $row['days_late'] ?> dias</td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

    <?php elseif ($type === 'punctuality'): ?>
        <h3>Índice de Pontualidade por Professor</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 10%;">Posição</th>
                    <th>Professor</th>
                    <th style="text-align: center;">Média de Pontuação</th>
                    <th style="text-align: center;">Volume de Envios</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($data)): ?>
                     <tr><td colspan="4" style="text-align: center; padding: 20px; color: #666;">Nenhum dado de pontualidade encontrado.</td></tr>
                <?php else: ?>
                    <?php foreach($data as $index => $row): ?>
                        <tr>
                            <td style="text-align: center; font-weight: bold; width: 5%;"><?= $index + 1 ?>º</td>
                            <td style="width: 55%;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <?php 
                                        if (!empty($row['profile_photo']) && file_exists(__DIR__ . '/../../../public/uploads/avatars/' . $row['profile_photo'])) {
                                            $avatarUrl = url('uploads/avatars/' . $row['profile_photo']);
                                        } else {
                                            $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($row['professor_name']) . "&background=random&color=fff&size=40";
                                        }
                                    ?>
                                    <img src="<?= $avatarUrl ?>" alt="Foto" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                                    <strong><?= htmlspecialchars($row['professor_name']) ?></strong>
                                </div>
                            </td>
                            <td style="text-align: center; width: 20%; white-space: nowrap;">
                                <span style="font-size: 1rem; font-weight: bold; color: #000;">
                                    <?= number_format($row['avg_score'] ?? 0, 1) ?> pts
                                </span>
                            </td>
                            <td style="text-align: center; width: 20%;"><?= $row['total_docs'] ?> docs</td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<style>
    /* Professional Print Styles - Corporate Layout */
    @media print {
        /* CRITICAL: Force thead to display - must override mobile CSS */
        table.data-table thead,
        .data-table thead {
            display: table-header-group !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        table.data-table thead tr,
        .data-table thead tr {
            display: table-row !important;
            visibility: visible !important;
        }
        
        table.data-table thead th,
        .data-table thead th {
            display: table-cell !important;
            visibility: visible !important;
        }
        
        @page { 
            margin: 1.5cm; 
            size: A4 portrait; 
        }
        
        body { 
            background: #fff !important; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
            color: #000 !important;
            -webkit-print-color-adjust: exact !important; 
            print-color-adjust: exact !important;
        }
        
        /* Hide non-printable elements */
        .navbar, 
        .btn, 
        .filter-container,
        .dashboard-header div:last-child,
        .btn-icon,
        img[alt="Foto"] {
            display: none !important;
        }
        
        .main-container { 
            padding: 0 !important; 
            width: 100% !important; 
            max-width: 100% !important; 
            margin: 0 !important; 
        }
        
        .list-section { 
            border: none !important; 
            box-shadow: none !important; 
            padding: 0 !important; 
            margin: 0 !important; 
            page-break-inside: avoid;
        }
        
        /* Professional Header */
        .dashboard-header::before {
            content: "SISTEMA DE GESTÃO PEDAGÓGICA - RELATÓRIOS DA ESCOLA";
            display: block;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 2px solid #333;
        }
        
        .dashboard-header {
            position: relative;
        }
        
        .dashboard-header::after {
            content: "Escola: " attr(data-school-name) "\A Diretor(a): " attr(data-director-name) "\A Emitido em: " attr(data-print-date);
            display: block;
            text-align: left;
            font-size: 11px;
            color: #444;
            margin-top: 8px;
            margin-bottom: 15px;
            white-space: pre-line;
            line-height: 1.6;
            font-weight: 500;
        }
        
        .dashboard-header h2 {
            font-size: 14px !important;
            text-align: center;
            margin: 10px 0 15px 0 !important;
            color: #444 !important;
            font-weight: 600;
        }
        
        h3 {
            font-size: 13px !important;
            margin: 15px 0 10px 0 !important;
            color: #333 !important;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            font-weight: 600;
        }
        
        /* Professional Table Styling */
        .data-table {
            width: 100% !important;
            border-collapse: collapse !important;
            margin-top: 10px;
            font-size: 10px !important;
            display: table !important;
        }
        
        .data-table thead {
            background-color: #2c3e50 !important;
            color: white !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            display: table-header-group !important;
            visibility: visible !important;
        }
        
        .data-table thead tr {
            display: table-row !important;
            visibility: visible !important;
        }
        
        .data-table thead th {
            display: table-cell !important;
            visibility: visible !important;
        }
        
        .data-table tbody {
            display: table-row-group !important;
        }
        
        .data-table tr {
            display: table-row !important;
            page-break-inside: avoid;
        }
        
        .data-table th {
            padding: 8px 6px !important;
            border: 1px solid #34495e !important;
            font-weight: 600 !important;
            font-size: 10px !important;
            text-align: left !important;
            color: #fff !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            display: table-cell !important;
        }
        
        .data-table td {
            padding: 6px !important;
            border: 1px solid #ddd !important;
            font-size: 9px !important;
            color: #000 !important;
            vertical-align: middle !important;
            display: table-cell !important;
        }
        
        .data-table tbody tr {
            page-break-inside: avoid;
        }
        
        .data-table tbody tr:nth-child(even) {
            background-color: #f8f9fa !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        /* Hide School column ONLY in tbody (keep header visible) */
        .data-table tbody tr td:nth-child(1) {
            display: none !important;
        }
        
        /* Hide School header */
        .data-table thead tr th:nth-child(1) {
            display: none !important;
        }
        
        /* Column Width Adjustments for Different Reports */
        .data-table th:nth-child(2),
        .data-table td:nth-child(2) {
            width: 40%;
        }
        
        .data-table th:nth-child(3),
        .data-table td:nth-child(3) {
            width: 20%;
            text-align: center !important;
        }
        
        .data-table th:nth-child(4),
        .data-table td:nth-child(4) {
            width: 20%;
            text-align: center !important;
        }
        
        .data-table th:nth-child(5),
        .data-table td:nth-child(5) {
            width: 20%;
            text-align: center !important;
        }
        
        /* Stats Grid for Professor Dashboard */
        .list-section > div > div[style*="grid-template-columns"] {
            display: grid !important;
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 8px !important;
            margin-bottom: 15px !important;
            page-break-inside: avoid;
        }
        
        .list-section > div > div[style*="grid-template-columns"] > div {
            padding: 8px !important;
            border: 1px solid #ccc !important;
            border-radius: 4px !important;
            text-align: center !important;
        }
        
        .list-section > div > div[style*="grid-template-columns"] > div > div:first-child {
            font-size: 9px !important;
            margin-bottom: 4px !important;
        }
        
        .list-section > div > div[style*="grid-template-columns"] > div > div:last-child {
            font-size: 14px !important;
            font-weight: bold !important;
        }
        
        /* Status Badges */
        .status-badge {
            background: none !important;
            border: none !important;
            padding: 0 !important;
            color: #000 !important;
            font-weight: normal !important;
        }
        
        /* Ensure proper page breaks */
        h3 {
            page-break-after: avoid;
        }
        
        table {
            page-break-inside: auto;
        }
        
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        
        /* Footer with page numbers */
        @page {
            @bottom-right {
                content: "Página " counter(page) " de " counter(pages);
                font-size: 9px;
                color: #666;
            }
        }
    }
</style>

<script>
    // Auto-insert current date/time, school name, and director name for print header
    document.addEventListener('DOMContentLoaded', function() {
        const now = new Date();
        const dateStr = now.toLocaleString('pt-BR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        const header = document.querySelector('.dashboard-header');
        if (header) {
            header.setAttribute('data-print-date', dateStr);
            
            // Get school name from PHP
            <?php if (isset($schools) && !empty($schools)): ?>
                header.setAttribute('data-school-name', '<?= htmlspecialchars($schools[0]['name'] ?? 'N/A') ?>');
            <?php endif; ?>
            
            // Get director name from user session
            <?php if (isset($user)): ?>
                header.setAttribute('data-director-name', '<?= htmlspecialchars($user['name'] ?? 'N/A') ?>');
            <?php endif; ?>
        }
    });
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
