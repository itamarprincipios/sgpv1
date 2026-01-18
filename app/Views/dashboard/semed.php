<?php require __DIR__ . '/../layouts/header.php'; ?>

<style>
    .semed-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 40px 30px;
        border-radius: 20px;
        margin-bottom: 30px;
        box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
        color: white;
    }
    
    .semed-hero h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0 0 10px 0;
        text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }
    
    .semed-hero p {
        font-size: 1.1rem;
        opacity: 0.95;
        margin: 0;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }
    
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05);
        position: relative;
        overflow: hidden;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--card-color), var(--card-color-light));
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
    }
    
    .stat-card.blue { --card-color: #3b82f6; --card-color-light: #60a5fa; }
    .stat-card.green { --card-color: #10b981; --card-color-light: #34d399; }
    .stat-card.yellow { --card-color: #f59e0b; --card-color-light: #fbbf24; }
    .stat-card.purple { --card-color: #8b5cf6; --card-color-light: #a78bfa; }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 15px;
        background: linear-gradient(135deg, var(--card-color), var(--card-color-light));
        color: white;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .stat-label {
        font-size: 0.85rem;
        color: #6b7280;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }
    
    .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1f2937;
        line-height: 1;
    }
    
    .chart-container {
        background: white;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        margin-bottom: 30px;
    }
    
    .chart-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .chart-title i {
        color: #667eea;
    }
    
    .ranking-section {
        background: white;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    
    .ranking-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .data-table {
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .data-table thead th {
        background: linear-gradient(135deg, #f9fafb, #f3f4f6);
        color: #374151;
        font-weight: 600;
        padding: 15px;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .data-table tbody tr {
        transition: all 0.2s ease;
    }
    
    .data-table tbody tr:hover {
        background: #f9fafb;
        transform: scale(1.01);
    }
    
    .data-table tbody td {
        padding: 15px;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .whatsapp-btn {
        background: linear-gradient(135deg, #25D366, #128C7E);
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 0.75rem;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(37, 211, 102, 0.3);
    }
    
    .whatsapp-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4);
    }
    
    /* Responsive Styles for Mobile/Tablet */
    @media (max-width: 768px) {
        .semed-hero {
            padding: 25px 15px;
        }
        
        .semed-hero h1 {
            font-size: 1.6rem;
        }
        
        .semed-hero p {
            font-size: 0.95rem;
        }
        
        .chart-container {
            padding: 20px 15px;
        }
        
        .chart-container canvas {
            max-height: 250px !important;
        }
        
        .chart-title {
            font-size: 1.1rem;
        }
        
        .ranking-title {
            font-size: 1rem;
        }
        
        .stat-value {
            font-size: 2rem;
        }
        
        .stat-label {
            font-size: 0.75rem;
        }
    }
    
    /* Period Filter Select - Matching semed-nav-btn style */
    .period-filter-select {
        display: inline-flex;
        align-items: center;
        padding: 12px 24px;
        background: linear-gradient(135deg, #ffffff, #f8f9fa);
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        color: #495057;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        cursor: pointer;
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23495057'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 1.2rem;
        padding-right: 45px;
        min-width: 180px;
    }
    
    .period-filter-select:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
        border-color: #667eea;
        background: linear-gradient(135deg, #f8f9fa, #ffffff);
    }
    
    .period-filter-select:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="semed-hero" style="position: relative;">
    <div style="display: flex; align-items: center; gap: 20px;">
        <!-- Avatar Section -->
        <div style="position: relative;">
            <?php 
                $photoUrl = !empty($user['profile_photo']) ? url('uploads/avatars/' . $user['profile_photo']) : 'https://ui-avatars.com/api/?name=' . urlencode($user['name']) . '&background=random'; 
            ?>
            <img src="<?= $photoUrl ?>" alt="Perfil" style="width:200px; height:200px; border-radius:50%; object-fit:cover; border:4px solid rgba(255,255,255,0.8); box-shadow: 0 4px 10px rgba(0,0,0,0.15);">
            
            <form action="<?= url('semed/photo/upload') ?>" method="POST" enctype="multipart/form-data" id="photo-form" style="display:none;">
                <input type="file" name="photo" id="photo-input" accept="image/png, image/jpeg" onchange="document.getElementById('photo-form').submit()">
            </form>
            
            <button onclick="document.getElementById('photo-input').click()" title="Alterar Foto" style="position: absolute; bottom: 10px; right: 10px; width: 45px; height: 45px; border-radius: 50%; padding: 0; border: none; background: white; color: #333; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                <i class="fas fa-camera" style="font-size: 20px; color: #667eea;"></i>
            </button>
        </div>

        <div>
            <h1>🎯 Painel SEMED</h1>
            <p>Visão completa da rede municipal de educação em tempo real</p>
        </div>
    </div>
</div>



<div style="display: flex; justify-content: space-between; align-items: center; width: 100%; margin-bottom: 30px;">
    <h2>Visão Geral da Rede (SEMED)</h2>
    <div style="display:flex; gap: 12px; align-items:center;">
        <a href="<?= url('semed/history') ?>" class="semed-nav-btn">
            <i class="fas fa-history"></i>
            <span>Banco de Planejamentos</span>
        </a>
        <form action="" method="GET" style="margin: 0;">
            <select name="filter" id="filter" onchange="this.form.submit()" class="period-filter-select">
                <option value="annual" <?= ($filter == 'annual') ? 'selected' : '' ?>>📅 Anual</option>
                <option value="bimestral" <?= ($filter == 'bimestral') ? 'selected' : '' ?>>📊 Bimestral</option>
                <option value="monthly" <?= ($filter == 'monthly') ? 'selected' : '' ?>>📆 Mensal</option>
            </select>
        </form>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card blue">
        <div class="stat-icon"><i class="fas fa-school"></i></div>
        <div class="stat-label">Escolas Ativas</div>
        <div class="stat-value"><?= $stats['total_schools'] ?></div>
    </div>
    
    <div class="stat-card green">
        <div class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></div>
        <div class="stat-label">Professores</div>
        <div class="stat-value"><?= $stats['total_professors'] ?></div>
    </div>
    
    <div class="stat-card yellow">
        <div class="stat-icon"><i class="fas fa-clipboard-list"></i></div>
        <div class="stat-label">Planejamentos</div>
        <div class="stat-value"><?= $stats['total_plannings'] ?></div>
    </div>
    
    <div class="stat-card purple">
        <div class="stat-icon"><i class="fas fa-check-double"></i></div>
        <div class="stat-label">Envios Totais</div>
        <div class="stat-value"><?= $stats['total_docs'] ?></div>
    </div>
</div>

<div class="charts-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 40px;">
    <div class="chart-container">
        <div class="chart-title">
            <i class="fas fa-chart-bar"></i>
            <span>Envios por Escola</span>
        </div>
        <canvas id="enviosChart" style="max-height: 300px;"></canvas>
    </div>
    
    <div class="chart-container">
        <div class="chart-title">
            <i class="fas fa-chart-line"></i>
            <span>Evolução Mensal</span>
        </div>
        <canvas id="timelineChart" style="max-height: 300px;"></canvas>
    </div>
</div>

<style>
    @media (max-width: 768px) {
        .charts-grid {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<script>
    // --- Chart: Bar (By School) ---
    const ctxBar = document.getElementById('enviosChart').getContext('2d');
    const chartData = <?= json_encode($chartData) ?>;
    
    new Chart(ctxBar, {
        type: 'pie',
        data: {
            labels: chartData.map(item => item.name),
            datasets: [{
                label: 'Documentos',
                data: chartData.map(item => item.total_docs),
                backgroundColor: [
                    '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#6366f1', 
                    '#14b8a6', '#f97316', '#06b6d4', '#84cc16'
                ],
                borderWidth: 1
            }]
        },
        options: { 
            responsive: true, 
            plugins: { 
                legend: { 
                    display: true,
                    position: 'right',
                    labels: { boxWidth: 12 }
                } 
            } 
        }
    });

    // --- Chart: Line (Monthly Timeline) ---
    const ctxLine = document.getElementById('timelineChart').getContext('2d');
    const monthlyData = <?= json_encode($monthlyData) ?>;
    
    new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: monthlyData.map(item => item.month),
            datasets: [{
                label: 'Volume de Envios',
                data: monthlyData.map(item => item.count),
                borderColor: '#2ecc71',
                backgroundColor: 'rgba(46, 204, 113, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointBackgroundColor: '#2ecc71'
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });
</script>

<div class="content-row" style="display: flex; flex-direction: column; gap: 30px; margin-top: 20px;">
    <div style="margin-bottom: 40px;">
        <div class="ranking-section">
            <div class="ranking-title">
                <i class="fas fa-trophy" style="color: #f59e0b;"></i>
                <span>🏆 Ranking de Escolas mais Pontuais</span>
            </div>
            <table class="data-table" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 10%;">Posição</th>
                        <th>Escola</th>
                        <th style="text-align: center;">Pontualidade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rankSchools)): ?>
                        <tr><td colspan="3" style="text-align: center; padding: 30px; color: #9ca3af;">Nenhum dado disponível para o período selecionado</td></tr>
                    <?php else: ?>
                        <?php foreach ($rankSchools as $index => $school): ?>
                            <tr>
                                <td style="text-align: center;">
                                    <span style="font-size: 1.5rem; font-weight: 700;">
                                        <?php if($index == 0): ?>🥇<?php elseif($index == 1): ?>🥈<?php elseif($index == 2): ?>🥉<?php else: ?><?= $index + 1 ?>º<?php endif; ?>
                                    </span>
                                </td>
                                <td><strong><?= htmlspecialchars($school['school_name']) ?></strong></td>
                                <td style="text-align: center;">
                                    <span style="font-size: 1.2rem; font-weight: 700; color: #667eea;">
                                        <?= number_format($school['punctuality_percentage'], 1) ?>%
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="rankings-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
        <!-- Professors Ranking -->
        <div class="ranking-section">
            <div class="ranking-title">
                <i class="fas fa-user-graduate" style="color: #10b981;"></i>
                <span>👩‍🏫 Professores Destaque</span>
            </div>
            <table class="data-table" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 15%;">Pos.</th>
                        <th>Nome</th>
                        <th style="text-align: center;">Pontos</th>
                        <th style="width: 15%;">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rankProfessors as $index => $prof): ?>
                        <tr>
                            <td style="text-align: center;">
                                <span style="font-size: 1.3rem; font-weight: 700;">
                                    <?php if($index == 0): ?>🥇<?php elseif($index == 1): ?>🥈<?php elseif($index == 2): ?>🥉<?php else: ?><?= $index + 1 ?>º<?php endif; ?>
                                </span>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($prof['professor_name']) ?></strong>
                                <br>
                                <small style="color:#9ca3af;"><?= htmlspecialchars($prof['school_name']) ?></small>
                            </td>
                            <td style="text-align: center;">
                                <span style="font-size: 1.1rem; font-weight: 700; color: #10b981;">
                                    <?= number_format($prof['total_points'], 1) ?>
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <?php if (!empty($prof['whatsapp'])): 
                                    $phone = preg_replace('/\D/', '', $prof['whatsapp']);
                                    if (strlen($phone) >= 10 && substr($phone, 0, 2) != '55') {
                                        $phone = '55' . $phone;
                                    }
                                ?>
                                    <a href="https://wa.me/<?= $phone ?>?text=Olá, <?= urlencode($prof['professor_name']) ?>! Parabéns pelo seu excelente desempenho no ranking de pontualidade!" target="_blank" class="whatsapp-btn">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Coordinators Ranking -->
        <div class="ranking-section">
            <div class="ranking-title">
                <i class="fas fa-user-tie" style="color: #8b5cf6;"></i>
                <span>🧭 Coordenadores Destaque</span>
            </div>
            <table class="data-table" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 15%;">Pos.</th>
                        <th>Nome</th>
                        <th style="text-align: center;">Pontualidade</th>
                        <th style="width: 15%;">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rankCoordinators as $index => $coord): ?>
                        <tr>
                            <td style="text-align: center;">
                                <span style="font-size: 1.3rem; font-weight: 700;">
                                    <?php if($index == 0): ?>🥇<?php elseif($index == 1): ?>🥈<?php elseif($index == 2): ?>🥉<?php else: ?><?= $index + 1 ?>º<?php endif; ?>
                                </span>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($coord['coordinator_name']) ?></strong>
                                <br>
                                <small style="color:#9ca3af;"><?= htmlspecialchars($coord['school_name']) ?></small>
                            </td>
                            <td style="text-align: center;">
                                    <span style="font-size: 1.1rem; font-weight: 700; color: #8b5cf6;">
                                        <?= number_format($coord['punctuality_percentage'], 1) ?>%
                                    </span>
                            </td>
                            <td style="text-align: center;">
                                <?php if (!empty($coord['whatsapp'])): 
                                    $phone = preg_replace('/\D/', '', $coord['whatsapp']);
                                    if (strlen($phone) >= 10 && substr($phone, 0, 2) != '55') {
                                        $phone = '55' . $phone;
                                    }
                                ?>
                                    <a href="https://wa.me/<?= $phone ?>?text=Olá, <?= urlencode($coord['coordinator_name']) ?>! Parabéns pelo excelente trabalho de gestão em sua escola!" target="_blank" class="whatsapp-btn">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <style>
        @media (max-width: 768px) {
            .rankings-grid {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
