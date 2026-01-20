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
        border-color: #11998e;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(17, 153, 142, 0.25);
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
        background-color: #11998e;
        color: #fff;
    }
    .btn-filter:hover {
        background-color: #0e847a;
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
    
    /* Modern Action Buttons */
    .btn-print-modern {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important;
        color: white !important;
        border: none !important;
        padding: 12px 24px !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        font-size: 0.95rem !important;
        transition: all 0.3s ease !important;
        box-shadow: 0 4px 12px rgba(17, 153, 142, 0.3) !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
    }
    
    .btn-print-modern:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(17, 153, 142, 0.4) !important;
        background: linear-gradient(135deg, #0e847a 0%, #2fcc6a 100%) !important;
    }
    
    .btn-back-modern {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
        color: #495057 !important;
        border: 2px solid #dee2e6 !important;
        padding: 12px 24px !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        font-size: 0.95rem !important;
        transition: all 0.3s ease !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        text-decoration: none !important;
    }
    
    .btn-back-modern:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
        background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%) !important;
        border-color: #adb5bd !important;
        color: #212529 !important;
    }

    @media print {
        /* Reset and hide UI elements */
        .btn, .navbar, .filter-container, .dashboard-header .btn, .dashboard-header div:last-child { 
            display: none !important; 
        }
        
        body {
            background: #fff !important;
            color: #000 !important;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        
        .main-container {
            padding: 0 !important;
            max-width: 100% !important;
            margin: 0 !important;
        }
        
        .list-section {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        
        /* Professional Header */
        .dashboard-header {
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 15px;
            margin-bottom: 25px;
            page-break-after: avoid;
        }
        
        .dashboard-header::before {
            content: "SISTEMA DE GESTÃO PEDAGÓGICA - MONITORIA";
            display: block;
            font-size: 9pt;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        
        .dashboard-header h2 {
            color: #2c3e50 !important;
            font-size: 18pt;
            font-weight: 600;
            margin: 0 !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .dashboard-header p {
            display: none;
        }
        
        /* Report Metadata */
        .dashboard-header::after {
            content: "Data de Emissão: " attr(data-date);
            display: block;
            font-size: 9pt;
            color: #7f8c8d;
            margin-top: 8px;
        }
        
        /* Professional Table Styling */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 10pt;
            page-break-inside: auto;
            display: table !important;
            table-layout: fixed !important;
        }
        
        .data-table thead {
            background: #34495e !important;
            color: white !important;
            display: table-header-group !important;
            width: 100% !important;
        }
        
        .data-table thead tr {
            width: 100% !important;
            display: table-row !important;
        }
        
        .data-table thead th {
            background: #34495e !important;
            color: white !important;
            padding: 10px 8px !important;
            text-align: left;
            font-weight: 600;
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border: none !important;
            display: table-cell !important;
        }
        
        .data-table thead th.text-center {
            text-align: center !important;
        }
        
        .data-table tbody {
            display: table-row-group !important;
        }
        
        .data-table tbody tr {
            page-break-inside: avoid;
            border-bottom: 1px solid #ecf0f1;
            display: table-row !important;
        }
        
        .data-table tbody tr:nth-child(even) {
            background: #f8f9fa !important;
        }
        
        .data-table tbody tr:hover {
            background: inherit !important;
            transform: none !important;
        }
        
        .data-table tbody td {
            padding: 8px !important;
            border: none !important;
            color: #2c3e50 !important;
            display: table-cell !important;
            vertical-align: middle;
        }
        
        .data-table tbody td.text-center {
            text-align: center !important;
        }
        
        /* Column widths for punctuality report */
        .data-table th:nth-child(1),
        .data-table td:nth-child(1) {
            width: 25% !important;
        }
        
        .data-table th:nth-child(2),
        .data-table td:nth-child(2) {
            width: 25% !important;
        }
        
        .data-table th:nth-child(3),
        .data-table td:nth-child(3),
        .data-table th:nth-child(4),
        .data-table td:nth-child(4),
        .data-table th:nth-child(5),
        .data-table td:nth-child(5) {
            width: auto !important;
            min-width: 80px;
        }
        
        .data-table th:nth-child(6),
        .data-table td:nth-child(6) {
            display: none !important;
        }
        
        /* Rate badges */
        .rate-badge {
            padding: 3px 8px !important;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: 600;
        }
        
        .rate-excellent { background: #d4edda !important; color: #155724 !important; border: 1px solid #c3e6cb !important; }
        .rate-good { background: #fff3cd !important; color: #856404 !important; border: 1px solid #ffeaa7 !important; }
        .rate-poor { background: #f8d7da !important; color: #721c24 !important; border: 1px solid #f5c6cb !important; }
        
        /* Page footer */
        @page {
            margin: 1.5cm 1.5cm 2cm 1.5cm;
            
            @bottom-center {
                content: "Página " counter(page) " de " counter(pages);
                font-size: 8pt;
                color: #7f8c8d;
            }
            
            @bottom-right {
                content: "SGP - Monitoria";
                font-size: 8pt;
                color: #7f8c8d;
            }
        }
        
        /* Empty state message */
        td[colspan] {
            text-align: center !important;
            padding: 30px !important;
            color: #95a5a6 !important;
            font-style: italic;
        }
        
        /* Ensure proper spacing */
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }

</style>

<div class="main-container">
    
    <!-- Header -->
    <div class="dashboard-header">
        <div>
            <h2>Relatório de Pontualidade</h2>
            <p class="text-muted mb-0">Acompanhamento de prazos de entrega dos professores Monitores</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()" class="btn-print-modern">
                <i class="fas fa-print"></i> Imprimir
            </button>
            <a href="<?= url('supervisor-monitor/dashboard') ?>" class="btn-back-modern">
                <i class="fas fa-arrow-left"></i> Voltar ao Painel
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="list-section">
        <form method="GET" action="<?= url('supervisor-monitor/punctuality_report') ?>" class="filter-container">
            
            <!-- Escola -->
            <div class="filter-group" style="flex: 2;">
                <label for="school_id" class="filter-label">Filtrar por Escola</label>
                <select name="school_id" id="school_id" class="filter-select cursor-pointer" onchange="this.form.submit()">
                    <option value="">Todas as Escolas</option>
                    <?php foreach ($schools as $school): ?>
                        <option value="<?= $school['id'] ?>" <?= (isset($schoolId) && $schoolId == $school['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($school['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Professor Monitor (só aparece se escola selecionada) -->
            <?php if ($schoolId): ?>
            <div class="filter-group" style="flex: 2;">
                <label for="professor_id" class="filter-label">Professor Monitor</label>
                <select name="professor_id" id="professor_id" class="filter-select cursor-pointer" onchange="this.form.submit()">
                    <option value="">Todos os Professores</option>
                    <?php foreach ($professors as $prof): ?>
                        <option value="<?= $prof['id'] ?>" <?= (isset($professorId) && $professorId == $prof['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($prof['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <!-- Período (só aparece se professor selecionado) -->
            <?php if (isset($professorId) && $professorId): ?>
            <div class="filter-group" style="flex: 0 0 150px; min-width: 150px;">
                <label for="period" class="filter-label">Período</label>
                <select name="period" id="period" class="filter-select cursor-pointer" onchange="this.form.submit()">
                    <option value="annual" <?= ($period == 'annual') ? 'selected' : '' ?>>Anual</option>
                    <option value="monthly" <?= ($period == 'monthly') ? 'selected' : '' ?>>Mensal (Atual)</option>
                    <option value="bimonthly" <?= ($period == 'bimonthly') ? 'selected' : '' ?>>Bimestral</option>
                </select>
            </div>
            <?php endif; ?>

            <!-- Botão Filtrar -->
            <div style="padding-bottom: 2px;">
                <button type="submit" class="btn-filter">
                    <i class="fas fa-search"></i> Filtrar
                </button>
            </div>

            <!-- Botão Limpar -->
            <div style="padding-bottom: 2px;">
                 <?php if(!empty($schoolId) || !empty($professorId)): ?>
                    <a href="<?= url('supervisor-monitor/punctuality_report') ?>" class="btn-clear text-decoration-none">
                        <i class="fas fa-times"></i> Limpar
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Dashboard Individual do Professor Monitor -->
    <?php if (isset($professorId) && $professorId && isset($data['stats'])): ?>
        <?php 
            $stats = $data['stats']; 
            $submissions = $data['submissions'];
            
            $profName = $selectedProf['name'] ?? 'Professor';
            $profPhoto = $selectedProf['profile_photo'] ?? null;
            
            if ($profPhoto && file_exists(__DIR__ . '/../../public/uploads/avatars/' . $profPhoto)) {
                $avatarUrl = url('uploads/avatars/' . $profPhoto);
            } else {
                $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($profName) . "&background=random&color=fff&size=128";
            }
        ?>
        
        <div class="list-section">
            <div style="display: flex; align-items: center; gap: 25px; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                <img src="<?= $avatarUrl ?>" alt="Foto do Professor" style="width: 200px; height: 200px; border-radius: 50%; object-fit: cover; border: 4px solid #ddd; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div>
                    <h3 style="margin: 0; color: #2c3e50; font-size: 2rem;">
                        <?= htmlspecialchars($profName) ?>
                        <span style="background-color: #17a2b8; color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 0.9rem; margin-left: 10px;">M.A.E</span>
                    </h3>
                    <span style="color: #666; font-size: 1.1rem; display: block; margin-top: 5px;">
                        Monitor de Aluno Especial - Dashboard de Desempenho
                    </span>
                    <?php if (!empty($selectedProf['whatsapp'])): ?>
                        <div style="margin-top: 10px; font-size: 0.9rem; color: #555;">
                            <div><i class="fab fa-whatsapp"></i> <?= htmlspecialchars($selectedProf['whatsapp']) ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
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

        <div class="list-section">
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
        </div>

    <?php else: ?>
    
    <!-- Tabela de Relatório Geral -->
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
    <?php endif; ?>

</div>

<script>
    // Add current date to header for print
    document.addEventListener('DOMContentLoaded', function() {
        const header = document.querySelector('.dashboard-header');
        if (header) {
            const today = new Date().toLocaleDateString('pt-BR', { 
                day: '2-digit', 
                month: '2-digit', 
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            header.setAttribute('data-date', today);
        }
    });
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
