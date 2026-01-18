<?php require __DIR__ . '/../layouts/header.php'; ?>

<style>
    .navigation-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-top: 30px;
    }
    
    .nav-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        border: 2px solid transparent;
        transition: all 0.3s ease;
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        gap: 12px;
        position: relative;
        overflow: hidden;
    }
    
    .nav-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        border-color: var(--primary);
    }
    
    .nav-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary), #667eea);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }
    
    .nav-card:hover::before {
        transform: scaleX(1);
    }
    
    .nav-card-icon {
        font-size: 2.5rem;
        color: var(--primary);
        margin-bottom: 5px;
    }
    
    .nav-card h3 {
        margin: 0;
        font-size: 1.2rem;
        color: #1f2937;
        font-weight: 600;
    }
    
    .nav-card p {
        margin: 0;
        font-size: 0.9rem;
        color: #6b7280;
        line-height: 1.5;
    }
    
    .nav-card-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: #ef4444;
        color: white;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: bold;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    
    
    @media (max-width: 768px) {
        .navigation-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }
        
        .school-hero {
            padding: 20px 15px !important;
        }
        
        .school-hero > div {
            flex-direction: column !important;
            align-items: center !important;
            text-align: center;
            gap: 15px !important;
        }
        
        .school-hero img {
            width: 120px !important;
            height: 120px !important;
        }
        
        .school-hero button {
            width: 35px !important;
            height: 35px !important;
            bottom: 5px !important;
            right: 5px !important;
        }
        
        .school-hero button i {
            font-size: 16px !important;
        }
        
        .school-hero h1 {
            font-size: 1.3rem !important;
            text-align: center;
            line-height: 1.3;
        }
        
        .school-hero h1 i {
            display: block;
            margin-bottom: 8px;
        }
        
        .school-hero p {
            font-size: 0.9rem;
        }
        
        .stats-grid {
            grid-template-columns: 1fr !important;
            gap: 12px;
        }
        
        .stat-card {
            padding: 15px !important;
        }
        
        .nav-card {
            padding: 20px;
        }
        
        .nav-card-icon {
            font-size: 2rem;
        }
        
        .nav-card h3 {
            font-size: 1.1rem;
        }
        
        .nav-card p {
            font-size: 0.85rem;
        }
        
        /* Gamification cards mobile */
        .gamification-card {
            padding: 15px !important;
        }
        
        .rank-table {
            font-size: 0.85rem;
        }
        
        .rank-table th,
        .rank-table td {
            padding: 8px 5px !important;
        }
        
        .g-header h3 {
            font-size: 1rem !important;
        }
    }

</style>

<div class="school-hero">
    <div style="display: flex; align-items: center; gap: 20px;">
        <!-- Avatar Section -->
        <div style="position: relative; flex-shrink: 0;">
            <?php 
                $photoUrl = !empty($user['profile_photo']) ? url('uploads/avatars/' . $user['profile_photo']) : 'https://ui-avatars.com/api/?name=' . urlencode($user['name']) . '&background=random'; 
            ?>
            <img src="<?= $photoUrl ?>" alt="Perfil" style="width:200px; height:200px; border-radius:50%; object-fit:cover; border:3px solid #fff; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            
            <form action="<?= url('school/photo/upload') ?>" method="POST" enctype="multipart/form-data" id="photo-form" style="display:none;">
                <input type="file" name="photo" id="photo-input" accept="image/png, image/jpeg" onchange="document.getElementById('photo-form').submit()">
            </form>
            
            <button onclick="document.getElementById('photo-input').click()" title="Alterar Foto" style="position: absolute; bottom: 10px; right: 10px; width: 45px; height: 45px; border-radius: 50%; padding: 0; border: none; background: #fff; color: var(--primary); cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                <i class="fas fa-camera" style="font-size: 20px;"></i>
            </button>
        </div>
        
        <div>
            <h1>
                <i class="fas fa-school"></i> 
                <?= isset($school['name']) ? htmlspecialchars($school['name']) : 'Painel da Escola' ?>
            </h1>
            <p>Painel de Gestão <?= ($user['role'] == 'director') ? 'do Diretor Escolar' : 'do Coordenador Pedagógico' ?></p>
        </div>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon-wrapper bg-blue">
            <i class="fas fa-chalkboard-teacher"></i>
        </div>
        <div class="stat-content">
            <span class="stat-value"><?= count($professors) ?></span>
            <span class="stat-label">Professores</span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon-wrapper bg-purple">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-content">
            <span class="stat-value"><?= count($classes) ?></span>
            <span class="stat-label">Turmas</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-wrapper bg-orange">
            <i class="fas fa-file-alt"></i>
        </div>
        <div class="stat-content">
            <span class="stat-value"><?= count($plannings) ?></span>
            <span class="stat-label">Planejamentos</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-wrapper bg-red">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <div class="stat-content">
            <span class="stat-value"><?= count($pendingSubmissions) ?></span>
            <span class="stat-label">Pendências</span>
        </div>
    </div>
</div>

<!-- GAMIFICATION WIDGET (Director Only) - Keep existing code -->
<?php if ($user['role'] == 'director'): ?>
    <?php
        // CALCULATE DATA ON THE FLY (MVP)
        $globalPunctuality = $docModel->getSchoolPunctuality();
        usort($globalPunctuality, function($a, $b) { return $b['avg_score'] <=> $a['avg_score']; });
        
        $mySchoolRank = null;
        $mySchoolData = null;
        $targetSchoolId = isset($school['id']) ? $school['id'] : $schools[0]['id'];
        
        foreach ($globalPunctuality as $idx => $row) {
            if ($row['school_name'] === ($school['name'] ?? '')) {
                $mySchoolRank = $idx + 1;
                $mySchoolData = $row;
                break;
            }
        }
        
        if (!$mySchoolData) {
            $mySchoolRank = count($globalPunctuality) + 1;
            $mySchoolData = ['school_name' => $school['name'], 'avg_score' => 10.0, 'total_docs' => 0];
        }

        $profStats = [];
        foreach($professors as $p) {
            $stats = $docModel->getProfessorStats($p['id']);
            $total = $stats['stats']['total_sent'] ?? 0;
            $ontime = $stats['stats']['on_time'] ?? 0;
            $profStats[] = [
                'name' => $p['name'],
                'school_name' => $school['name'] ?? '',
                'points' => $ontime * 5,
                'whatsapp' => $p['whatsapp'],
                'id' => $p['id']
            ];
        }
        usort($profStats, function($a, $b) { return $b['points'] <=> $a['points']; });
        $topProfessors = array_slice($profStats, 0, 3);

        $coordStats = [];
        foreach($coordinators as $c) {
            $coordStats[] = [
                'name' => $c['name'],
                'school_name' => $c['school_name'] ?? $school['name'],
                'punctuality' => number_format($mySchoolData['avg_score'], 1),
                'whatsapp' => $c['whatsapp'],
                'id' => $c['id']
            ];
        }
        $coordStats = array_slice($coordStats, 0, 3);
    ?>

    <style>
        .gamification-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .gamification-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            padding: 20px;
            border: 1px solid #e5e7eb;
        }
        .g-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .g-header h3 {
            margin: 0;
            font-size: 1.1rem;
            color: #374151;
        }
        .g-icon {
            color: #f59e0b;
            font-size: 1.2rem;
        }
        .rank-table {
            width: 100%;
            border-collapse: collapse;
        }
        .rank-table th {
            text-align: left;
            font-size: 0.75rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 8px 10px;
            background: #f9fafb;
        }
        .rank-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 0.9rem;
        }
        .rank-pos {
            font-weight: bold;
            color: #6b7280;
            width: 50px;
            text-align: center;
        }
        .medal-icon { color: #f59e0b; margin-right: 5px; }
        .rank-score {
            font-weight: bold;
            color: #4f46e5;
            text-align: right;
        }
        
        @media (min-width: 992px) {
            .gamification-lower {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
            }
        }
    </style>

    <div class="gamification-grid">
        <!-- Ranking de Escolas -->
        <div class="gamification-card">
            <div class="g-header">
                <i class="fas fa-trophy g-icon"></i>
                <h3>Ranking de Escolas mais Pontuais</h3>
            </div>
            <table class="rank-table">
                <thead>
                    <tr>
                        <th style="text-align: center;">Posição</th>
                        <th>Escola</th>
                        <th style="text-align: right;">Pontualidade</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="rank-pos">
                            <?php if($mySchoolRank == 1): ?><i class="fas fa-medal medal-icon"></i><?php else: ?><?= $mySchoolRank ?>º<?php endif; ?>
                        </td>
                        <td><strong><?= htmlspecialchars($mySchoolData['school_name']) ?></strong></td>
                        <td class="rank-score"><?= number_format($mySchoolData['avg_score'], 1) ?>%</td>
                    </tr>
                    <?php 
                    $count = 0;
                    foreach($globalPunctuality as $idx => $row): 
                        if($row['school_name'] == $mySchoolData['school_name']) continue;
                        if($count >= 2) break;
                        $count++;
                    ?>
                    <tr style="opacity: 0.6;">
                        <td class="rank-pos"><?= $idx + 1 ?>º</td>
                        <td><?= htmlspecialchars($row['school_name']) ?></td>
                        <td class="rank-score"><?= number_format($row['avg_score'], 1) ?>%</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="gamification-lower">
            <!-- Professores Destaque -->
            <div class="gamification-card">
                <div class="g-header">
                    <i class="fas fa-chalkboard-teacher" style="color: #10b981;"></i>
                    <h3>Professores Destaque</h3>
                </div>
                <table class="rank-table">
                    <thead>
                        <tr>
                            <th style="text-align: center;">Pos.</th>
                            <th>Nome</th>
                            <th style="text-align: center;">Pontos</th>
                            <th style="text-align: center;">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($topProfessors as $i => $p): ?>
                        <tr>
                            <td class="rank-pos">
                                <?php if($i == 0): ?><i class="fas fa-medal medal-icon"></i><?php else: ?><?= $i + 1 ?>º<?php endif; ?>
                            </td>
                            <td>
                                <div style="font-weight: 600;"><?= htmlspecialchars($p['name']) ?></div>
                                <div style="font-size: 0.75rem; color: #9ca3af;"><?= htmlspecialchars($p['school_name']) ?></div>
                            </td>
                            <td style="text-align: center; font-weight: bold; color: #10b981;"><?= $p['points'] ?>.0</td>
                            <td style="text-align: center;">
                                <?php if(!empty($p['whatsapp'])): ?>
                                    <a href="https://wa.me/<?= preg_replace('/\\D/','', $p['whatsapp']) ?>" target="_blank" class="btn-icon" style="background: #e6fffa; color: #10b981; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px;"><i class="fab fa-whatsapp"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Coordenadores Destaque -->
            <div class="gamification-card">
                <div class="g-header">
                    <i class="fas fa-user-tie" style="color: #6366f1;"></i>
                    <h3>Coordenadores Destaque</h3>
                </div>
                <table class="rank-table">
                    <thead>
                        <tr>
                            <th style="text-align: center;">Pos.</th>
                            <th>Nome</th>
                            <th style="text-align: center;">Pontualidade</th>
                            <th style="text-align: center;">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($coordStats as $i => $c): ?>
                        <tr>
                            <td class="rank-pos">
                                 <?php if($i == 0): ?><i class="fas fa-medal medal-icon"></i><?php else: ?><?= $i + 1 ?>º<?php endif; ?>
                            </td>
                            <td>
                                <div style="font-weight: 600;"><?= htmlspecialchars($c['name']) ?></div>
                                <div style="font-size: 0.75rem; color: #9ca3af;"><?= htmlspecialchars($c['school_name']) ?></div>
                            </td>
                            <td style="text-align: center; font-weight: bold; color: #6366f1;"><?= $c['punctuality'] ?>%</td>
                            <td style="text-align: center;">
                                <?php if(!empty($c['whatsapp'])): ?>
                                    <a href="https://wa.me/<?= preg_replace('/\\D/','', $c['whatsapp']) ?>" target="_blank" class="btn-icon" style="background: #eef2ff; color: #6366f1; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px;"><i class="fab fa-whatsapp"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- NAVIGATION CARDS -->
<h2 style="margin: 30px 0 20px 0; color: #1f2937; font-size: 1.5rem;">
    <i class="fas fa-th-large"></i> Áreas de Gestão
</h2>

<div class="navigation-grid">
    <a href="<?= url('school/plannings') ?>" class="nav-card">
        <i class="fas fa-file-alt nav-card-icon"></i>
        <h3>Meus Planejamentos</h3>
        <p><?= count($plannings) ?> planejamentos cadastrados</p>
    </a>
    
    <a href="<?= url('school/bimesters') ?>" class="nav-card">
        <i class="fas fa-calendar-alt nav-card-icon" style="color: #10b981;"></i>
        <h3>Organização por Bimestres</h3>
        <p>Organize os planejamentos por período</p>
    </a>
    
    <a href="<?= url('school/pending') ?>" class="nav-card">
        <?php if(count($pendingSubmissions) > 0): ?>
            <span class="nav-card-badge"><?= count($pendingSubmissions) ?></span>
        <?php endif; ?>
        <i class="fas fa-exclamation-triangle nav-card-icon" style="color: #ef4444;"></i>
        <h3>Pendências de Entrega</h3>
        <p><?= count($pendingSubmissions) ?> pendências encontradas</p>
    </a>
    
    <a href="<?= url('school/uploads') ?>" class="nav-card">
        <?php if (!empty($newUploadsCount) && $newUploadsCount > 0): ?>
            <span class="nav-card-badge"><?= $newUploadsCount ?></span>
        <?php endif; ?>
        <i class="fas fa-upload nav-card-icon" style="color: #8b5cf6;"></i>
        <h3>Envios Recentes</h3>
        <p>Documentos recebidos dos professores</p>
    </a>
    
    <a href="<?= url('school/classes') ?>" class="nav-card">
        <i class="fas fa-users nav-card-icon" style="color: #f59e0b;"></i>
        <h3>Turmas</h3>
        <p><?= count($classes) ?> turmas cadastradas</p>
    </a>
    
    <a href="<?= url('school/professors') ?>" class="nav-card">
        <i class="fas fa-chalkboard-teacher nav-card-icon" style="color: #3b82f6;"></i>
        <h3>Professores</h3>
        <p><?= count($professors) ?> professores cadastrados</p>
    </a>
    
    <?php if($user['role'] === 'director'): ?>
    <a href="<?= url('school/coordinators') ?>" class="nav-card">
        <i class="fas fa-user-tie nav-card-icon" style="color: #6366f1;"></i>
        <h3>Coordenadores</h3>
        <p>Gerenciar coordenadores da escola</p>
    </a>
    <?php endif; ?>
</div>

<!-- IANNE AI Widget -->
<?php if ($user['role'] === 'coordinator'): ?>
    <?php require __DIR__ . '/../partials/coordinator_ai_widget.php'; ?>
<?php elseif ($user['role'] === 'director'): ?>
    <?php require __DIR__ . '/../partials/coordinator_ai_widget.php'; ?>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
