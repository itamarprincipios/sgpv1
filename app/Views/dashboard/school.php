<?php require __DIR__ . '/../layouts/header.php'; ?>

<style>
    /* Tabs Styles */
    .tabs {
        display: flex;
        border-bottom: 2px solid #ddd;
        margin-bottom: 20px;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .tab-btn {
        padding: 10px 20px;
        cursor: pointer;
        background: none;
        border: none;
        font-size: 1rem;
        font-weight: 500;
        color: #666;
        border-bottom: 3px solid transparent;
        transition: all 0.3s;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .tab-btn.active {
        color: var(--primary);
        border-bottom-color: var(--primary);
    }
    .tab-content {
        display: none;
        animation: fadeIn 0.3s;
    }
    .tab-content.active {
        display: block;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .dropdown-content a:hover {
        background-color: #f8f9fa !important;
        color: var(--primary) !important;
    }
    
    /* Responsive Styles for Mobile/Tablet */
    @media (max-width: 768px) {
        .school-hero h1 {
            font-size: 1.5rem;
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }
        
        .school-hero {
            padding: 25px 15px;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .tabs {
            gap: 8px;
            padding-bottom: 5px;
        }
        
        .tab-btn {
            padding: 8px 12px;
            font-size: 0.85rem;
        }
        
        /* Formulários em coluna */
        form[style*="grid-template-columns"] {
            grid-template-columns: 1fr !important;
        }
        
        /* Filtros em coluna */
        .filter-container {
            padding: 1rem;
        }
        
        /* Dropdowns */
        .dropdown-content {
            right: auto !important;
            left: 0 !important;
            min-width: 100% !important;
        }
    }
</style>


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

    <!-- GAMIFICATION WIDGET (Director Only) -->
    <?php if ($user['role'] == 'director'): ?>
        <?php
            // CALCULATE DATA ON THE FLY (MVP)
            // 1. School Ranking
            // Fetch global Punctuality (simulated or real if Document model allows)
            $globalPunctuality = $docModel->getSchoolPunctuality(); // Returns [{school_name, avg_score, total_docs}]
            
            // Sort by score desc
            usort($globalPunctuality, function($a, $b) { return $b['avg_score'] <=> $a['avg_score']; });
            
            // Find my school
            $mySchoolRank = null;
            $mySchoolData = null;
            $targetSchoolId = isset($school['id']) ? $school['id'] : $schools[0]['id'];
            
            // Note: getSchoolPunctuality returns Name, Score, Total. It doesn't return ID currently.
            // Matching by name is risky but acceptable for MVP if unique names.
            // Better: Let's assume the first match with similar name or just display top schools and Highlight ours.
            // Or simpler: Show "Minha Escola" score and its hypothetical rank.
            
            foreach ($globalPunctuality as $idx => $row) {
                if ($row['school_name'] === ($school['name'] ?? '')) {
                    $mySchoolRank = $idx + 1;
                    $mySchoolData = $row;
                    break;
                }
            }
            // Fallback if not found (no docs yet)
            if (!$mySchoolData) {
                $mySchoolRank = count($globalPunctuality) + 1;
                $mySchoolData = ['school_name' => $school['name'], 'avg_score' => 10.0, 'total_docs' => 0];
            }

            // 2. Professors Destaque
            // We have $professors list. Need their stats.
            $profStats = [];
            foreach($professors as $p) {
                // Warning: N+1 query. OK for small schools. Optimization needed for large scale.
                $stats = $docModel->getProfessorStats($p['id']);
                // Score Calculation: (OnTime / Total) * 10 
                // Stats returns total_sent, on_time.
                $total = $stats['stats']['total_sent'] ?? 0;
                $ontime = $stats['stats']['on_time'] ?? 0;
                $score = ($total > 0) ? ($ontime / $total) * 100 : 0; // Usage % or Punctuality % ?
                // User asked for "Pontos 15.0" in image. Maybe raw count of OnTime?
                // Visual shows "Pontualidade 100.0%".
                // Professor Destaque shows "Pontos 15.0". Let's use OnTime Count as Points.
                $profStats[] = [
                    'name' => $p['name'],
                    'school_name' => $school['name'] ?? '',
                    'points' => $ontime * 5, // 5 points per activity? Or just count. Let's use count.
                    'whatsapp' => $p['whatsapp'],
                    'id' => $p['id']
                ];
            }
            usort($profStats, function($a, $b) { return $b['points'] <=> $a['points']; });
            $topProfessors = array_slice($profStats, 0, 5); // Top 5

            // 3. Coordinators Destaque (Same School score for now)
            $coordStats = [];
            foreach($coordinators as $c) {
                $coordStats[] = [
                    'name' => $c['name'],
                    'school_name' => $c['school_name'] ?? $school['name'],
                    'punctuality' => number_format($mySchoolData['avg_score'], 1), // School Avg
                    'whatsapp' => $c['whatsapp'],
                    'id' => $c['id']
                ];
            }
            // Only have one list, so just show them.
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
                color: #f59e0b; /* Gold */
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
                        <!-- Show Top 2 Global just for context if valid -->
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
                                        <a href="https://wa.me/<?= preg_replace('/\D/','', $p['whatsapp']) ?>" target="_blank" class="btn-icon" style="background: #e6fffa; color: #10b981; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px;"><i class="fab fa-whatsapp"></i></a>
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
                                        <a href="https://wa.me/<?= preg_replace('/\D/','', $c['whatsapp']) ?>" target="_blank" class="btn-icon" style="background: #eef2ff; color: #6366f1; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px;"><i class="fab fa-whatsapp"></i></a>
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

<div class="tabs">
    <button class="tab-btn active" onclick="openTab(event, 'tab-planning')">Meus Planejamentos</button>
    <button class="tab-btn" onclick="openTab(event, 'tab-bimesters')">Organização (Bimestres)</button>
    <button class="tab-btn" onclick="openTab(event, 'tab-pending')">Pendências de Entrega</button>
    <button class="tab-btn" onclick="openTab(event, 'tab-uploads'); markUploadsViewed(this)" style="position: relative;">
        Envios Recentes
        <?php if (!empty($newUploadsCount) && $newUploadsCount > 0): ?>
            <span id="badge-uploads" style="background: #e74c3c; color: white; border-radius: 50%; padding: 2px 6px; font-size: 0.7rem; position: absolute; top: 0; right: 0; transform: translate(50%, -10%); box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                <?= $newUploadsCount ?>
            </span>
        <?php endif; ?>
    </button>

    <button class="tab-btn" onclick="openTab(event, 'tab-classes')">Turmas</button>
    <button class="tab-btn" onclick="openTab(event, 'tab-professors')">Professores</button>
    <?php if($user['role'] === 'director'): ?>
        <button class="tab-btn" onclick="openTab(event, 'tab-coordinators')">Coordenadores</button>
    <?php endif; ?>
</div>

<?php $showSchool = isset($schools) && count($schools) > 1; ?>

<!-- TAB 1: PLANEJAMENTOS (LISTA) -->
<div id="tab-planning" class="tab-content active">
    <div style="margin-bottom: 15px;">
        <a href="<?= url('school/planning/create') ?>" class="btn btn-primary" style="width: auto;"><i class="fas fa-plus"></i> Novo Planejamento</a>
    </div>
    <div class="list-section">
        <h3>Meus Planejamentos Cadastrados</h3>
        <table class="data-table">
            <thead>
                <tr>
                   <?php if($showSchool): ?><th>Escola</th><?php endif; ?>
                   <th>Nome</th>
                   <th>Descrição/Período</th>
                   <th>Prazo Limite</th>
                   <th>Área de envios</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($plannings)): ?>
                    <tr><td colspan="<?= $showSchool ? 5 : 4 ?>">Nenhum planejamento criado.</td></tr>
                <?php else: ?>
                    <?php foreach($plannings as $p): ?>
                        <tr>
                            <?php if($showSchool): ?>
                                <td><small class="badge" style="background: #e2e8f0; color: #333;"><?= htmlspecialchars($p['school_name']) ?></small></td>
                            <?php endif; ?>
                            <td><?= htmlspecialchars($p['name']) ?></td>
                            <td><?= htmlspecialchars($p['description']) ?></td>
                            <td><?= date('d/m/Y', strtotime($p['deadline'])) ?></td>
                            <td style="display: flex; gap: 20px; align-items: center;">
                                <a href="<?= url('school/planning/view?id=' . $p['id']) ?>" class="btn btn-primary" style="width: auto; padding: 5px 15px; font-size: 0.85rem;">
                                    <i class="fas fa-list"></i> Controle de Envios
                                </a>
                                <div style="display: flex; gap: 10px; border-left: 1px solid #ddd; padding-left: 15px;">
                                    <a href="<?= url('school/planning/edit?id=' . $p['id']) ?>" class="btn-icon" title="Editar"><i class="fas fa-edit"></i></a>
                                    <a href="<?= url('school/planning/delete?id=' . $p['id']) ?>" class="btn-icon" style="color: red;" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este planejamento? Todos os envios relacionados também serão afetados.')"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- TAB 2: ORGANIZAÇÃO POR BIMESTRES -->
<div id="tab-bimesters" class="tab-content">
    <div class="list-section">
        <h3><i class="fas fa-calendar-alt"></i> Organização por Bimestres</h3>
        <p style="color: #666; font-size: 0.9rem; margin-bottom: 20px;">Associe os planejamentos cadastrados aos bimestres correspondentes para melhor organização.</p>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            <?php 
            $bimestres = [
                ['id' => 1, 'name' => '1º Bimestre', 'color' => '#3b82f6'],
                ['id' => 2, 'name' => '2º Bimestre', 'color' => '#10b981'],
                ['id' => 3, 'name' => '3º Bimestre', 'color' => '#f59e0b'],
                ['id' => 4, 'name' => '4º Bimestre', 'color' => '#8b5cf6']
            ];
            
            foreach($bimestres as $bim):
                // Filter plannings for this bimester
                $bimPlannings = array_filter($plannings, function($p) use ($bim) {
                    return isset($p['bimester']) && $p['bimester'] == $bim['id'];
                });
            ?>
                <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-top: 4px solid <?= $bim['color'] ?>;">
                    <h4 style="margin: 0 0 15px 0; color: <?= $bim['color'] ?>; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-calendar-check"></i>
                        <?= $bim['name'] ?>
                    </h4>
                    
                    <?php if(empty($bimPlannings)): ?>
                        <p style="color: #9ca3af; font-size: 0.9rem; font-style: italic;">Nenhum planejamento associado a este bimestre.</p>
                    <?php else: ?>
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <?php foreach($bimPlannings as $p): ?>
                                <li style="padding: 8px 0; border-bottom: 1px solid #f3f4f6; display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <strong><?= htmlspecialchars($p['name']) ?></strong>
                                        <?php if($showSchool): ?>
                                            <br><small style="color: #6b7280;"><?= htmlspecialchars($p['school_name']) ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <a href="<?= url('school/planning/view?id=' . $p['id']) ?>" class="btn-icon" title="Ver detalhes">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    
                    <!-- Dropdown to associate unassigned plannings -->
                    <?php 
                    $unassignedPlannings = array_filter($plannings, function($p) {
                        return empty($p['bimester']) || $p['bimester'] == 0;
                    });
                    if(!empty($unassignedPlannings)): 
                    ?>
                        <form action="<?= url('school/planning/associate-bimester') ?>" method="POST" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #f3f4f6;">
                            <input type="hidden" name="bimester" value="<?= $bim['id'] ?>">
                            <select name="planning_id" required style="width: 100%; padding: 8px; border: 1px solid #e5e7eb; border-radius: 6px; margin-bottom: 10px;">
                                <option value="">Associar planejamento...</option>
                                <?php foreach($unassignedPlannings as $p): ?>
                                    <option value="<?= $p['id'] ?>">
                                        <?= htmlspecialchars($p['name']) ?>
                                        <?php if($showSchool): ?> - <?= htmlspecialchars($p['school_name']) ?><?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 8px; font-size: 0.9rem;">
                                <i class="fas fa-plus"></i> Adicionar
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- TAB 3: PENDÊNCIAS -->
<div id="tab-pending" class="tab-content">
    <div class="list-section">
        <h3 style="color: #e74c3c;"><i class="fas fa-exclamation-triangle"></i> Pendências de Entrega (Tempo Real)</h3>
        <p style="color: #666; font-size: 0.9rem; margin-bottom: 20px;">Acompanhe abaixo os professores que ainda não enviaram os planejamentos vigentes ou atrasados.</p>
        
        <table class="data-table">
            <thead>
                <tr>
                    <?php if($showSchool): ?><th>Escola</th><?php endif; ?>
                    <th>Professor</th>
                    <th>Turma</th>
                    <th>Planejamento Pendente</th>
                    <th>Prazo</th>
                    <th>Status</th>
                    <th>Cobrar</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($pendingSubmissions)): ?>
                    <tr><td colspan="<?= $showSchool ? 7 : 6 ?>" style="text-align: center; color: #2ecc71; font-weight: bold; padding: 20px;"><i class="fas fa-check-circle"></i> Parabéns! Nenhuma pendência encontrada.</td></tr>
                <?php else: ?>
                    <?php foreach($pendingSubmissions as $p): 
                        $isLate = strtotime($p['deadline']) < time();
                    ?>
                        <tr style="<?= $isLate ? 'background-color: #fff5f5;' : '' ?>">
                            <?php if($showSchool): ?>
                                <td><small class="badge" style="background: #e2e8f0; color: #333;"><?= htmlspecialchars($p['school_name']) ?></small></td>
                            <?php endif; ?>
                            <td><?= htmlspecialchars($p['professor_name']) ?></td>
                            <td><?= htmlspecialchars($p['class_name']) ?></td>
                            <td><?= htmlspecialchars($p['planning_name']) ?></td>
                            <td style="font-weight: bold; <?= $isLate ? 'color: #c0392b;' : '' ?>">
                                <?= date('d/m/Y', strtotime($p['deadline'])) ?>
                            </td>
                            <td>
                                <?php if($isLate): ?>
                                    <span class="status-badge status-rejeitado">Atrasado</span>
                                <?php else: ?>
                                    <span class="status-badge status-ajustado">Pendente</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($p['whatsapp'])): 
                                    $phone = preg_replace('/\D/', '', $p['whatsapp']);
                                    if (strlen($phone) >= 10 && substr($phone, 0, 2) != '55') {
                                        $phone = '55' . $phone;
                                    }
                                    $msg = $isLate 
                                        ? "Olá, " . urlencode($p['professor_name']) . "! Consta em nosso sistema que a entrega do planejamento *" . urlencode($p['planning_name']) . "* está atrasada. O prazo era " . date('d/m/Y', strtotime($p['deadline'])) . ". Poderia verificar?"
                                        : "Olá, " . urlencode($p['professor_name']) . "! Lembrete amigável: o prazo para entrega do planejamento *" . urlencode($p['planning_name']) . "* encerra em " . date('d/m/Y', strtotime($p['deadline'])) . ".";
                                ?>
                                    <a href="https://wa.me/<?= $phone ?>?text=<?= $msg ?>" target="_blank" class="whatsapp-btn" style="background: #25D366;">
                                        <i class="fab fa-whatsapp"></i> Cobrar
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

<!-- TAB 2: ENVIOS RECENTES -->
<div id="tab-uploads" class="tab-content">
    <div class="list-section">
        <h3>Últimos Documentos Recebidos</h3>
        
        <form method="GET" action="<?= url('school/dashboard') ?>" class="filter-container">
            <input type="hidden" name="tab" value="uploads"> 
            
            <div class="filter-group">
                <label class="filter-label">Planejamento/Bimestre</label>
                <select name="period_id" class="filter-select" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    <?php foreach($plannings as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= ($filters['period_id'] == $p['id']) ? 'selected' : '' ?>>
                             <?php if($showSchool) echo '['.htmlspecialchars($p['school_name']).'] '; ?>
                            <?= htmlspecialchars($p['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Professor</label>
                <select name="professor_id" class="filter-select" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    <?php foreach($professors as $prof): ?>
                        <option value="<?= $prof['id'] ?>" <?= ($filters['professor_id'] == $prof['id']) ? 'selected' : '' ?>>
                            <?php if($showSchool) echo '['.htmlspecialchars($prof['school_name']).'] '; ?>
                            <?= htmlspecialchars($prof['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group" style="flex: 0 0 150px; min-width: 150px;">
                <label class="filter-label">Status</label>
                <select name="status" class="filter-select" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    <option value="enviado" <?= ($filters['status'] == 'enviado') ? 'selected' : '' ?>>Enviado</option>
                    <option value="aprovado" <?= ($filters['status'] == 'aprovado') ? 'selected' : '' ?>>Aprovado</option>
                    <option value="ajustado" <?= ($filters['status'] == 'ajustado') ? 'selected' : '' ?>>Ajustado</option>
                    <option value="rejeitado" <?= ($filters['status'] == 'rejeitado') ? 'selected' : '' ?>>Rejeitado</option>
                    <option value="atrasado" <?= ($filters['status'] == 'atrasado') ? 'selected' : '' ?>>Atrasado</option>
                </select>
            </div>
            
            <div class="filter-actions">
                 <a href="<?= url('school/dashboard') ?>" class="btn-filter-clear">
                    <i class="fas fa-times"></i> Limpar
                 </a>
            </div>
        </form>

        <table class="data-table">
           <thead>
               <tr>
                   <?php if($showSchool): ?><th>Escola</th><?php endif; ?>
                   <th>Professor</th>
                   <th>Turma</th>
                   <th>Documento</th>
                   <th>Status</th>
                   <th>Ação</th>
               </tr>
           </thead>
           <tbody>
               <?php if(empty($documents)): ?>
                   <tr><td colspan="<?= $showSchool ? 6 : 5 ?>">Nenhum documento.</td></tr>
               <?php else: ?>
                   <?php foreach($documents as $doc): ?>
                       <tr>
                           <?php if($showSchool): ?>
                                <td><small class="badge" style="background: #e2e8f0; color: #333;"><?= htmlspecialchars($doc['school_name']) ?></small></td>
                           <?php endif; ?>
                           <td><?= htmlspecialchars($doc['professor_name']) ?></td>
                           <td>-</td>
                           <td><?= htmlspecialchars($doc['title']) ?></td>
                           <td><span class="status-badge status-<?= $doc['status'] ?>"><?= ucfirst($doc['status']) ?></span></td>
                           <td><a href="<?= url('uploads/' . $doc['file_path']) ?>" target="_blank" class="btn-icon"><i class="fas fa-eye"></i></a></td>
                       </tr>
                   <?php endforeach; ?>
               <?php endif; ?>
           </tbody>
        </table>
    </div>
</div>

<!-- TAB 3: TURMAS -->
<div id="tab-classes" class="tab-content">
    <div class="content-row">
        <div class="upload-section">
            <h3>Cadastrar Nova Turma</h3>
            <form action="<?= url('school/class/store') ?>" method="POST">
                <?php if(count($schools) > 1): ?>
                    <div class="form-group">
                        <label>Escola</label>
                        <select name="school_id" required class="form-control" style="width: 100%; margin-bottom: 10px;">
                            <?php foreach($schools as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php else: ?>
                    <input type="hidden" name="school_id" value="<?= $schools[0]['id'] ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label>Nome da Turma</label>
                    <input type="text" name="name" required placeholder="Ex: 5º Ano A">
                </div>
                <button type="submit" class="btn btn-primary">Salvar Turma</button>
            </form>
        </div>
        <div class="list-section">
            <h3>Turmas Cadastradas</h3>
            <ul style="list-style: none;">
                <?php foreach($classes as $c): ?>
                    <li style="padding: 10px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between;">
                        <div>
                            <span style="font-weight: bold;">
                                <?php if($showSchool): ?>
                                    <span class="badge" style="background: #e2e8f0; color: #333; font-size: 0.8em; margin-right: 5px;"><?= htmlspecialchars($c['school_name'] ?? '') ?></span>
                                <?php endif; ?>
                                <?= htmlspecialchars($c['name']) ?>
                            </span>
                            <div style="font-size: 0.85rem; color: #666; margin-top: 4px;">
                                <?php if($c['professor_name']): ?>
                                    <i class="fas fa-chalkboard-teacher"></i> Titular: <?= htmlspecialchars($c['professor_name']) ?>
                                <?php else: ?>
                                    <span style="color: #e74c3c;"><i class="fas fa-exclamation-circle"></i> Sem professor titular</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <a href="<?= url('school/class/edit?id='.$c['id']) ?>" class="btn-icon" title="Editar"><i class="fas fa-edit"></i></a>
                            <a href="<?= url('school/class/delete?id='.$c['id']) ?>" class="btn-icon" style="color: red;" title="Excluir" onclick="return confirm('ATENÇÃO: Tem certeza que deseja excluir esta turma? Os professores vinculados ficarão sem turma.')"><i class="fas fa-trash"></i></a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<!-- TAB 4: PROFESSORES -->
<div id="tab-professors" class="tab-content">
    <div class="content-row">
        <div class="upload-section">
            <h3>Cadastrar Professor</h3>
            <form action="<?= url('school/professor/store') ?>" method="POST">
                <?php if(count($schools) > 1): ?>
                    <div class="form-group">
                        <label>Escola</label>
                        <select name="school_id" required class="form-control" style="width: 100%; margin-bottom: 10px;">
                            <?php foreach($schools as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php else: ?>
                    <input type="hidden" name="school_id" value="<?= $schools[0]['id'] ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label>Nome Completo</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>E-mail (Login)</label>
                    <input type="email" name="email" required>
                </div>
                <!-- Senha padrão oculta: 123456 -->
                <div class="form-group">
                    <label>WhatsApp</label>
                    <input type="text" name="whatsapp" placeholder="Ex: 5511999999999">
                </div>
                <div class="form-group">
                    <label>Vincular a Turma</label>
                    <select name="class_id">
                        <option value="">Selecione uma turma...</option>
                        <?php foreach($classes as $c): ?>
                            <!-- Grouping might be nice but simple name append is faster -->
                            <option value="<?= $c['id'] ?>">
                                <?php if($showSchool) echo '[' . htmlspecialchars($c['school_name'] ?? '') . '] '; ?>
                                <?= htmlspecialchars($c['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="display: flex; align-items: center; gap: 10px; margin-top: 10px;">
                    <input type="checkbox" name="is_physical_education" id="prof_is_pe" value="1" style="width: 18px; height: 18px;">
                    <label for="prof_is_pe" style="margin: 0; cursor: pointer;">Professor de Educação Física?</label>
                </div>
                <button type="submit" class="btn btn-primary">Cadastrar Professor</button>
            </form>
        </div>
        <div class="list-section">
            <h3>Professores da Escola</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <?php if($showSchool): ?><th>Escola</th><?php endif; ?>
                        <th>Nome</th>
                        <th>Turma</th>
                        <th>WhatsApp</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($professors as $prof): ?>
                        <tr>
                            <?php if($showSchool): ?>
                                <td><small class="badge" style="background: #e2e8f0; color: #333;"><?= htmlspecialchars($prof['school_name'] ?? '') ?></small></td>
                            <?php endif; ?>
                            <td><?= htmlspecialchars($prof['name']) ?></td>
                            <td>
                                <?php 
                                if ($prof['is_physical_education'] == 1) {
                                    echo '<span style="color: #10b981; font-weight: 600;">Educação Física</span>';
                                } elseif ($prof['class_name']) {
                                    echo htmlspecialchars($prof['class_name']);
                                } else {
                                    echo '<span style="color:red">Sem Turma</span>';
                                }
                                ?>
                            </td>
                            <td><?= htmlspecialchars($prof['whatsapp']) ?></td>
                            <td>
                                <a href="<?= url('school/professor/edit?id='.$prof['id']) ?>" class="btn-icon" title="Editar"><i class="fas fa-edit"></i></a>
                                <?php if (!empty($prof['whatsapp'])): 
                                    $phone = preg_replace('/\D/', '', $prof['whatsapp']);
                                    if (strlen($phone) >= 10 && substr($phone, 0, 2) != '55') {
                                        $phone = '55' . $phone;
                                    }
                                ?>
                                    <a href="https://wa.me/<?= $phone ?>?text=Olá, professor(a) <?= urlencode($prof['name']) ?>!" target="_blank" class="btn-icon" style="color: #25D366;" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                                <?php endif; ?>
                                <a href="<?= url('school/professor/reset-password?id='.$prof['id']) ?>" class="btn-icon" style="color: #f59e0b;" title="Resetar Senha" onclick="return confirm('Resetar a senha do professor <?= htmlspecialchars($prof['name']) ?> para \'professor123\'?')"><i class="fas fa-key"></i></a>
                                <a href="<?= url('school/professor/delete?id='.$prof['id']) ?>" class="btn-icon" style="color: red;" onclick="return confirm('Tem certeza que vai excluir o professor? (Esta ação não pode ser desfeita)')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>

                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- TAB 6: COORDENADORES (DIRETOR ONLY) -->
<?php if($user['role'] === 'director'): ?>
<div id="tab-coordinators" class="tab-content">
    <div class="upload-section">
        <h3><i class="fas fa-user-plus"></i> Novo Coordenador</h3>
        <form action="<?= url('school/coordinator/store') ?>" method="POST" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; align-items: end;">
            <div class="form-group" style="margin: 0;">
                <label>Nome Completo</label>
                <input type="text" name="name" required placeholder="Nome do Coordenador">
            </div>
            <div class="form-group" style="margin: 0;">
                <label>E-mail (Login)</label>
                <input type="email" name="email" required placeholder="email@sgp.com">
            </div>
            <div class="form-group" style="margin: 0;">
                <label>Escola</label>
                <select name="school_id" required>
                    <?php foreach($schools as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="margin: 0;">
                <label>WhatsApp</label>
                <input type="text" name="whatsapp" placeholder="Ex: 5511999999999">
            </div>
            <button type="submit" class="btn btn-primary" style="height: 42px;">Salvar</button>
        </form>
        <p style="font-size: 0.8rem; color: #9ca3af; margin-top: 10px;">* Senha padrão: <strong>coordinator123</strong></p>
    </div>

    <div class="list-section">
        <h3>Coordenadores da Escola</h3>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Escola</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($coordinators)): ?>
                        <tr><td colspan="4" style="text-align: center; color: #777;">Nenhum coordenador encontrado.</td></tr>
                    <?php else: ?>
                        <?php foreach($coordinators as $coord): ?>
                            <tr>
                                <td><?= htmlspecialchars($coord['name']) ?></td>
                                <td><?= htmlspecialchars($coord['email']) ?></td>
                                <td><?= htmlspecialchars($coord['school_name'] ?? 'N/A') ?></td>
                                <td>
                                    <a href="<?= url('school/coordinator/edit?id='.$coord['id']) ?>" class="btn-icon" title="Editar"><i class="fas fa-edit"></i></a>
                                    
                                    <?php if (!empty($coord['whatsapp'])): 
                                        $phone = preg_replace('/\D/', '', $coord['whatsapp']);
                                        if (strlen($phone) >= 10 && substr($phone, 0, 2) != '55') $phone = '55' . $phone;
                                    ?>
                                        <a href="https://wa.me/<?= $phone ?>" target="_blank" class="btn-icon" style="color: #25D366;" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                                    <?php endif; ?>
                                    
                                    <a href="<?= url('school/coordinator/delete?id='.$coord['id']) ?>" class="btn-icon" style="color: red;" onclick="return confirm('Excluir coordenador?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
    function openTab(evt, tabName) {
        // ... (existing)
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tab-content");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].classList.remove("active");
        }
        tablinks = document.getElementsByClassName("tab-btn");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].classList.remove("active");
        }
        
        var target = document.getElementById(tabName);
        if (target) {
            target.classList.add("active");
            if (evt) evt.currentTarget.classList.add("active");
            else {
                // Find button for this tab
                // Simple hack: assume order matches? No.
                // Loop buttons
                for (i = 0; i < tablinks.length; i++) {
                     if (tablinks[i].getAttribute('onclick').includes(tabName)) {
                         tablinks[i].classList.add("active");
                     }
                }
            }
        }
    }
    
    // ... (rest)
    
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        
        if (tab === 'classes') openTab(null, 'tab-classes');
        else if (tab === 'professors') openTab(null, 'tab-professors');
        else if (tab === 'coordinators') openTab(null, 'tab-coordinators'); // NEW
        else if (tab === 'bimesters') openTab(null, 'tab-bimesters');
        else if (tab === 'uploads' || urlParams.has('period_id')) openTab(null, 'tab-uploads');
    });
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
