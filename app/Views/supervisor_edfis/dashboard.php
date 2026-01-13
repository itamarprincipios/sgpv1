<?php 
$pageTitle = 'SGP - Supervisão Ed. Física';
require __DIR__ . '/../layouts/header.php'; 
?>

<style>
.supervisor-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
    background: #667eea;
    color: white;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-block;
}

.nav-btn:hover {
    background: #5568d3;
    transform: translateY(-2px);
    color: white;
}

.nav-btn.active {
    background: #764ba2;
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
    color: #667eea;
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
</style>


<!-- Header com Foto e Informações -->
<div class="supervisor-header">
    <div class="supervisor-info">
        <?php
        $photoPath = $user['profile_photo'] ?? 'default-avatar.png';
        $photoUrl = url('public/uploads/profiles/' . $photoPath);
        ?>
        <img src="<?= $photoUrl ?>" alt="Foto" class="supervisor-photo" 
             onclick="document.getElementById('photoUpload').click()">
        
        <div class="supervisor-details">
            <h1><i class="fas fa-running"></i> SGP - SUPERVISÃO GERAL DE ED. FÍSICA</h1>
            <p>Painel de acompanhamento de todos os professores de Educação Física da rede municipal</p>
        </div>
    </div>
    
    <div style="text-align: right;">
        <div style="font-size: 0.9rem; opacity: 0.9;">Olá, <?= htmlspecialchars($user['name']) ?></div>
        <a href="<?= url('logout') ?>" style="color: white; text-decoration: underline; font-size: 0.9rem;">
            <i class="fas fa-sign-out-alt"></i> Sair
        </a>
    </div>
</div>

<!-- Upload de Foto (hidden) -->
<form id="photoForm" method="POST" action="<?= url('supervisor-edfis/photo/upload') ?>" enctype="multipart/form-data" style="display: none;">
    <input type="file" id="photoUpload" name="photo" accept="image/*" onchange="document.getElementById('photoForm').submit()">
</form>

<!-- Botões de Navegação -->
<div class="nav-buttons">
    <a href="#professores" class="nav-btn active" onclick="showTab('professores'); return false;">
        <i class="fas fa-users"></i> Professores
    </a>
    <a href="#planejamentos" class="nav-btn" onclick="showTab('planejamentos'); return false;">
        <i class="fas fa-file-alt"></i> Planejamentos
    </a>
    <a href="<?= url('supervisor-edfis/punctuality') ?>" class="nav-btn">
        <i class="fas fa-chart-line"></i> Relatórios
    </a>
</div>

<!-- Estatísticas -->
<div class="row" style="margin-bottom: 2rem;">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-value"><?= $stats['total_professors'] ?></div>
            <div class="stat-label">Total de Professores Ed. Fis</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-value"><?= $stats['total_plannings'] ?></div>
            <div class="stat-label">Planejamentos Enviados</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-value" style="color: #dc3545;"><?= $stats['late'] ?></div>
            <div class="stat-label">Atrasados</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-value" style="color: #28a745;"><?= $stats['punctuality_rate'] ?>%</div>
            <div class="stat-label">Taxa de Pontualidade</div>
        </div>
    </div>
</div>


<!-- Abas de Navegação - Agora são divs simples -->
<div id="professores-tab" class="tab-content-supervisor active">
        <?php if (empty($professorsBySchool)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Nenhum professor de Educação Física cadastrado ainda.
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
                                        <a href="https://wa.me/55<?= preg_replace('/[^0-9]/', '', $prof['whatsapp']) ?>?text=Olá <?= urlencode($prof['name']) ?>, sou a supervisora de Educação Física da SEMED." 
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
                                    <a href="<?= url('supervisor-edfis/planning/view?id=' . $plan['id']) ?>" 
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
if ($user && $user['role'] === 'supervisor_edfis') {
    include __DIR__ . '/../partials/supervisor_edfis_ai_widget.php';
}
?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
