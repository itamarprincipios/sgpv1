<?php 
$pageTitle = 'SGP - Supervisão de Monitores';
require __DIR__ . '/../layouts/header.php'; 
?>

<style>
.supervisor-header {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); /* Green/Teal gradient for Monitors */
    color: white;
    padding: 2rem;
    border-radius: 10px;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.supervisor-info {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.supervisor-photo {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    border: 3px solid white;
    object-fit: cover;
    cursor: pointer;
}

.supervisor-photo:hover {
    opacity: 0.9;
}

.supervisor-details h1 {
    margin: 0;
    font-size: 1.5rem;
}

.supervisor-details p {
    margin: 0.5rem 0 0 0;
    opacity: 0.9;
}

.nav-buttons {
    background: white;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 2rem;
    display: flex;
    gap: 1rem;
}

.nav-btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 6px;
    background: #11998e;
    color: white;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-block;
}

.nav-btn:hover {
    background: #0e847a;
    transform: translateY(-2px);
    color: white;
}

.nav-btn.active {
    background: #38ef7d;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-align: center;
}

.stat-value {
    font-size: 2.5rem;
    font-weight: bold;
    color: #11998e;
}

.stat-label {
    color: #666;
    font-size: 0.9rem;
    margin-top: 0.5rem;
}

.professor-card {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.school-group {
    margin-bottom: 2rem;
}

.school-header {
    background: #f5f5f5;
    padding: 1rem;
    border-radius: 6px;
    margin-bottom: 1rem;
    font-weight: bold;
    color: #333;
}

.status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.85rem;
    font-weight: 500;
}

.status-enviado { background: #d4edda; color: #155724; }
.status-aprovado { background: #cce5ff; color: #004085; }
.status-atrasado { background: #f8d7da; color: #721c24; }
.status-pendente { background: #fff3cd; color: #856404; }

.btn-whats {
    background: #25D366;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
}

.btn-whats:hover {
    background: #128C7E;
    color: white;
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
    text-decoration: none;
    display: inline-block;
}

.whatsapp-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4);
    color: white;
}
</style>


<!-- Header com Foto e Informações -->
<div class="school-hero" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
    <div style="display: flex; align-items: center; gap: 20px;">
        <!-- Avatar Section -->
        <div style="position: relative; flex-shrink: 0;">
            <?php 
                $photoPath = $user['profile_photo'] ?? 'default-avatar.png';
                // Se for arquivo local (não URL), adiciona o caminho
                if (strpos($photoPath, 'http') === false) {
                     $photoUrl = url('uploads/avatars/' . $photoPath);
                } else {
                     $photoUrl = $photoPath;
                }
            ?>
            <img src="<?= $photoUrl ?>" alt="Perfil" style="width:200px; height:200px; border-radius:50%; object-fit:cover; border:3px solid #fff; box-shadow: 0 4px 6px rgba(0,0,0,0.1);"
                 onclick="document.getElementById('photoUpload').click()"
                 onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=<?= urlencode($user['name']) ?>&background=random&size=200';">
            
            <button onclick="document.getElementById('photoUpload').click()" title="Alterar Foto" style="position: absolute; bottom: 10px; right: 10px; width: 45px; height: 45px; border-radius: 50%; padding: 0; border: none; background: #fff; color: #11998e; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                <i class="fas fa-camera" style="font-size: 20px;"></i>
            </button>
        </div>
        
        <div>
            <h1>
                <i class="fas fa-chalkboard-teacher"></i> 
                SGP - Supervisão de Monitores
            </h1>
            <p>Painel de Gestão da Supervisão de Monitores</p>
        </div>
    </div>
</div>

<!-- Upload de Foto (hidden) -->
<form id="photoForm" method="POST" action="<?= url('supervisor-monitor/photo/upload') ?>" enctype="multipart/form-data" style="display: none;">
    <input type="file" id="photoUpload" name="photo" accept="image/*" onchange="document.getElementById('photoForm').submit()">
</form>

<!-- Botões de Navegação -->
<div class="nav-buttons">
    <a href="<?= url('supervisor-monitor/professors') ?>" class="nav-btn">
        <i class="fas fa-users"></i> Professores
    </a>
    <a href="<?= url('supervisor-monitor/plannings') ?>" class="nav-btn">
        <i class="fas fa-file-alt"></i> Planejamentos
    </a>
    <a href="<?= url('supervisor-monitor/punctuality_report') ?>" class="nav-btn">
        <i class="fas fa-chart-line"></i> Relatórios
    </a>
</div>

<!-- Filtro e Título da Seção -->
<div style="display: flex; justify-content: space-between; align-items: center; width: 100%; margin-bottom: 30px;">
    <h2>Visão Geral - Monitores</h2>
    <form action="" method="GET" class="filter-container" style="margin: 0; width: auto; min-width: 200px;">
        <div class="filter-group" style="margin: 0; flex: 1;">
            <select name="filter" id="filter" onchange="this.form.submit()" class="filter-select">
                <option value="annual" <?= ($filter ?? 'annual') == 'annual' ? 'selected' : '' ?>>Anual</option>
                <option value="bimestral" <?= ($filter ?? 'annual') == 'bimestral' ? 'selected' : '' ?>>Bimestral</option>
                <option value="monthly" <?= ($filter ?? 'annual') == 'monthly' ? 'selected' : '' ?>>Mensal</option>
            </select>
        </div>
    </form>
</div>

<!-- Estatísticas -->
<div class="stats-grid">
    <!-- Card Professores -->
    <div class="stat-card">
        <div class="stat-icon-wrapper bg-blue">
            <i class="fas fa-chalkboard-teacher"></i>
        </div>
        <div class="stat-content">
            <span class="stat-value"><?= $stats['total_professors'] ?></span>
            <span class="stat-label">Professores</span>
        </div>
    </div>
    
    <!-- Card Planejamentos -->
    <div class="stat-card">
        <div class="stat-icon-wrapper bg-purple">
            <i class="fas fa-file-alt"></i>
        </div>
        <div class="stat-content">
            <span class="stat-value"><?= $stats['total_plannings'] ?></span>
            <span class="stat-label">Planejamentos</span>
        </div>
    </div>

    <!-- Card Atrasados -->
    <div class="stat-card">
        <div class="stat-icon-wrapper bg-red">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <div class="stat-content">
            <span class="stat-value"><?= $stats['late'] ?></span>
            <span class="stat-label">Pendências</span>
        </div>
    </div>

    <!-- Card Pontualidade -->
    <div class="stat-card">
        <div class="stat-icon-wrapper bg-orange">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
            <span class="stat-value"><?= $stats['punctuality_rate'] ?>%</span>
            <span class="stat-label">Pontualidade</span>
        </div>
    </div>
</div>


<!-- Seção de Rankings -->
<div class="content-row" style="display: flex; flex-direction: column; gap: 30px; margin-top: 20px;">
    <!-- Ranking de Escolas mais Pontuais -->
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
                                    <span style="font-size: 1.2rem; font-weight: 700; color: #11998e;">
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

    <!-- Grid de Professores e Coordenadores -->
    <div class="rankings-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
        <!-- Monitores Destaque -->
        <div class="ranking-section">
            <div class="ranking-title">
                <i class="fas fa-user-graduate" style="color: #10b981;"></i>
                <span>👩‍🏫 Monitores Destaque</span>
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
                    <?php if (empty($rankProfessors)): ?>
                        <tr><td colspan="4" style="text-align: center; padding: 30px; color: #9ca3af;">Nenhum dado disponível</td></tr>
                    <?php else: ?>
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
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Coordenadores Destaque -->
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
                    <?php if (empty($rankCoordinators)): ?>
                        <tr><td colspan="4" style="text-align: center; padding: 30px; color: #9ca3af;">Nenhum dado disponível</td></tr>
                    <?php else: ?>
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
                    <?php endif; ?>
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


<!-- Abas de Navegação (Conteúdo Oculto) -->
<div id="professores-tab" class="tab-content-supervisor" style="display: none;">
        <?php if (empty($professorsBySchool)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Nenhum professor Monitor cadastrado ainda.
            </div>
        <?php else: ?>
            <?php foreach ($professorsBySchool as $schoolId => $schoolData): ?>
                <div class="school-group">
                    <div class="school-header">
                        <i class="fas fa-school"></i> <?= htmlspecialchars($schoolData['school_name']) ?>
                        <span style="float: right; font-weight: normal; font-size: 0.9rem;">
                            <?= count($schoolData['professors']) ?> professor(es)
                        </span>
                    </div>
                    
                    <?php foreach ($schoolData['professors'] as $prof): ?>
                        <div class="professor-card">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <strong><?= htmlspecialchars($prof['name']) ?></strong><br>
                                    <small class="text-muted">
                                        Turma: <?= htmlspecialchars($prof['class_name'] ?? 'N/A') ?>
                                    </small>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Email:</small><br>
                                    <?= htmlspecialchars($prof['email']) ?>
                                </div>
                                <div class="col-md-2">
                                    <small class="text-muted">WhatsApp:</small><br>
                                    <?= htmlspecialchars($prof['whatsapp'] ?? 'Não informado') ?>
                                </div>
                                <div class="col-md-2">
                                    <?php
                                    // Verificar último envio
                                    $lastPlanning = null;
                                    foreach ($plannings as $plan) {
                                        if ($plan['user_id'] == $prof['id']) {
                                            $lastPlanning = $plan;
                                            break;
                                        }
                                    }
                                    
                                    if ($lastPlanning):
                                        $statusClass = 'status-' . $lastPlanning['status'];
                                        $statusText = ucfirst($lastPlanning['status']);
                                    ?>
                                        <span class="status-badge <?= $statusClass ?>">
                                            <?= $statusText ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="status-badge status-pendente">Pendente</span>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-2 text-right">
                                    <?php if ($prof['whatsapp']): ?>
                                        <a href="https://wa.me/55<?= preg_replace('/[^0-9]/', '', $prof['whatsapp']) ?>?text=Olá <?= urlencode($prof['name']) ?>, sou a supervisora de Monitores da SEMED." 
                                           class="btn-whats" target="_blank">
                                            <i class="fab fa-whatsapp"></i> Contatar
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Tab: Planejamentos -->
    <div id="planejamentos-tab" class="tab-content-supervisor" style="display: none;">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Professor</th>
                        <th>Escola</th>
                        <th>Período</th>
                        <th>Data Envio</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($plannings)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                Nenhum planejamento enviado ainda.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($plannings as $plan): ?>
                            <tr>
                                <td><?= htmlspecialchars($plan['professor_name']) ?></td>
                                <td><?= htmlspecialchars($plan['school_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($plan['period_name']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($plan['submitted_at'])) ?></td>
                                <td>
                                    <span class="status-badge status-<?= $plan['status'] ?>">
                                        <?= ucfirst($plan['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= url('supervisor-monitor/planning/view?id=' . $plan['id']) ?>" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> Visualizar
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

<script>
function showTab(tabName) {
    // Esconder todas as tabs
    document.getElementById('professores-tab').style.display = 'none';
    document.getElementById('planejamentos-tab').style.display = 'none';
    
    // Remover classe active de todos os botões
    document.querySelectorAll('.nav-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Mostrar tab selecionada
    if (tabName === 'professores') {
        document.getElementById('professores-tab').style.display = 'block';
        document.querySelectorAll('.nav-btn')[0].classList.add('active');
    } else if (tabName === 'planejamentos') {
        document.getElementById('planejamentos-tab').style.display = 'block';
        document.querySelectorAll('.nav-btn')[1].classList.add('active');
    }
}
</script>

<!-- Widget IANNE -->
<?php
if ($user && ($user['role'] === 'supervisor_edfis' || $user['role'] === 'supervisor_monitor')) {
    include __DIR__ . '/../partials/coordinator_ai_widget.php';
}
?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
