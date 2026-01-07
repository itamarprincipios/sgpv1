<?php require __DIR__ . '/../layouts/header.php'; ?>

<style>
    /* Tabs Styles - Reusing logical styles */
    .tabs {
        display: flex;
        border-bottom: 2px solid var(--border-color);
        margin-bottom: 20px;
        overflow-x: auto;
    }
    .tab-btn {
        padding: 10px 20px;
        cursor: pointer;
        background: none;
        border: none;
        font-size: 1rem;
        font-weight: 500;
        color: var(--secondary);
        border-bottom: 3px solid transparent;
        transition: all 0.3s;
        white-space: nowrap;
    }
    .tab-btn:hover {
        color: var(--primary);
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
    .status-badge.status-aprovado { background: #FFD700; color: #000; font-weight: bold; }
    .status-badge.status-ajustado { background: #28a745; color: #fff; font-weight: bold; }
    .status-badge.status-rejeitado { background: #dc3545; color: #fff; font-weight: bold; }
    .status-badge.status-atrasado { background: #6c757d; color: #fff; }
    .status-badge.status-enviado { background: #e3f2fd; color: #1976d2; }
</style>

<div class="dashboard-header">
    <div style="display:flex; justify-content:space-between; width:100%; align-items:flex-start;">
        <div style="display:flex; align-items:center;">
             <!-- Avatar Section -->
            <div style="position: relative; margin-right: 15px;">
                <?php 
                    $photoUrl = !empty($user['profile_photo']) ? url('uploads/avatars/' . $user['profile_photo']) : 'https://ui-avatars.com/api/?name=' . urlencode($user['name']) . '&background=random'; 
                ?>
                <img src="<?= $photoUrl ?>" alt="Perfil" style="width:200px; height:200px; border-radius:50%; object-fit:cover; border:5px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.15);">
                
                <form action="<?= url('professor/photo/upload') ?>" method="POST" enctype="multipart/form-data" id="photo-form" style="display:none;">
                    <input type="file" name="photo" id="photo-input" accept="image/png, image/jpeg" onchange="document.getElementById('photo-form').submit()">
                </form>
                
                <button onclick="document.getElementById('photo-input').click()" title="Alterar Foto" style="position: absolute; bottom: 10px; right: 10px; width: 40px; height: 40px; border-radius: 50%; padding: 0; border: none; background: #007bff; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                    <i class="fas fa-camera" style="font-size: 18px;"></i>
                </button>
            </div>

            <div>
                <h2 style="margin:0;">Meus Envios e Solicitações</h2>
                <div style="font-size: 0.9rem; color: #555; margin-top: 5px;">
                    <i class="fas fa-school"></i> <strong>Escola:</strong> <?= htmlspecialchars($schoolData['name'] ?? 'Não informada') ?> 
                    <span style="font-weight: normal; margin-left: 20px;">
                        <i class="fas fa-chalkboard"></i> <strong>Turma:</strong> <?= htmlspecialchars($className ?? 'Não vinculada') ?>
                    </span>
                </div>
            </div>
        </div>
        
        <button onclick="document.getElementById('password-form').style.display = document.getElementById('password-form').style.display == 'none' ? 'block' : 'none'" class="btn btn-secondary" style="width:auto; font-size:0.8rem;">Alterar Senha</button>
    </div>
    
    <div id="password-form" style="display:none; background:var(--card-bg); padding:15px; border:1px solid var(--border-color); margin-top:10px; border-radius:5px;">
        <form action="<?= url('professor/password/change') ?>" method="POST" style="display:flex; gap:10px; align-items:flex-end;">
            <div class="form-group" style="margin:0;">
                <label>Nova Senha</label>
                <div style="position: relative; width: 100%;">
                    <input type="password" name="password" id="new-password" required placeholder="Digite a nova senha" style="padding:5px; padding-right: 35px; width: 100%;">
                    <span onclick="togglePassword('new-password', 'eye-icon-prof')" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #666;">
                        <i id="eye-icon-prof" class="fas fa-eye"></i>
                    </span>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="padding:5px 15px;">Salvar</button>
        </form>
    </div>

    <div class="stats-cards">
        <div class="card card-blue" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white;">
            <h3>Minha Pontuação</h3>
            <p style="font-size: 2rem; font-weight: bold;"><?= number_format($totalPoints, 1) ?> pts</p>
        </div>
        <div class="card card-green">
            <h3>Envios Aprovados</h3>
            <p><?= count(array_filter($documents, fn($d) => $d['status'] == 'aprovado')) ?></p>
        </div>
        <div class="card card-purple" style="background: #f8f9fa; border: 1px solid #ddd; color: #333;">
            <h3 style="color: #666;">Medalhas Atuais</h3>
            <div style="font-size: 1.5rem; display: flex; gap: 5px; flex-wrap: wrap;">
                <?php if(empty($medals)): ?>
                    <span style="font-size: 0.8rem; color: #999;">Em busca de conquistas...</span>
                <?php else: ?>
                    <?php foreach ($medals as $m): ?>
                        <span title="<?= htmlspecialchars($m['medal_type']) ?>"><?= mb_substr($m['medal_type'], 0, 2) ?></span>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

    <div class="upload-section" style="width: 100%; margin-bottom: 2rem;">
        <h3>Envio de Planejamento</h3>
        
        <?php if(empty($periods)): ?>
            <p>Nenhuma solicitação de planejamento em aberto.</p>
        <?php else: ?>
            <?php 
                // Extract IDs of periods that already have a document submitted
                $submittedPeriodIds = array_column($documents, 'period_id');
            ?>
            <!-- Tabs Navigation -->
            <div class="tabs">
                <?php foreach ($periods as $index => $period): ?>
                    <?php 
                        $currDoc = null;
                        foreach ($documents as $doc) {
                            if ($doc['period_id'] == $period['id']) {
                                $currDoc = $doc;
                                break;
                            }
                        }
                        
                        $isSubmitted = ($currDoc !== null);
                        $tabColor = 'inherit';
                        $checkIcon = '';

                        if ($isSubmitted) {
                            $s = strtolower(trim($currDoc['status']));
                            $tabColor = '#17a2b8'; // Blue for submitted
                            $checkIcon = ' <i class="fas fa-paper-plane" style="font-size:0.7em;"></i>';
                            
                            if ($s == 'aprovado') {
                                $tabColor = '#ccac00'; // Gold
                                $checkIcon = ' <i class="fas fa-trophy" style="font-size:0.7em;"></i>';
                            } elseif ($s == 'ajustado') {
                                $tabColor = '#28a745'; // Green
                                $checkIcon = ' <i class="fas fa-check-circle" style="font-size:0.7em;"></i>';
                            } elseif ($s == 'rejeitado') {
                                $tabColor = '#dc3545'; // Red
                                $checkIcon = ' <i class="fas fa-undo" style="font-size:0.7em;"></i>';
                            }
                        }
                    ?>
                    <button class="tab-btn <?= $index === 0 ? 'active' : '' ?>" onclick="openTab(event, 'planning-<?= $period['id'] ?>')" style="color: <?= $tabColor ?>; font-weight: <?= $isSubmitted ? 'bold' : 'normal' ?>;">
                        <?= htmlspecialchars($period['name']) . $checkIcon ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Tabs Content -->
            <?php foreach ($periods as $index => $period): ?>
            <?php
                // Find specific document for this period
                $currentDoc = null;
                foreach ($documents as $doc) {
                    if ($doc['period_id'] == $period['id']) {
                        $currentDoc = $doc;
                        break;
                    }
                }

                // Lógica de Cores e Status Base (Data)
                $deadline = new DateTime($period['deadline']);
                $today = new DateTime();
                $today->setTime(0,0,0); 
                $deadline->setTime(0,0,0);
                
                $statusText = 'No Prazo';
                $statusColor = 'green';

                if ($today > $deadline) {
                    $statusText = 'Atrasado';
                    $statusColor = 'red';
                } elseif ($today == $deadline) {
                    $statusText = 'Entrega Hoje!';
                    $statusColor = '#d39e00';
                }

                // Sobrepor com Status da Revisão se houver documento
                if ($currentDoc) {
                    $s = strtolower(trim($currentDoc['status']));
                    if ($s == 'aprovado') { 
                        $statusText = 'Aprovado'; $statusColor = '#ccac00'; 
                    } elseif ($s == 'ajustado') { 
                        $statusText = 'Aprovação c/ Ajustes'; $statusColor = '#28a745'; 
                    } elseif ($s == 'rejeitado') { 
                        $statusText = 'Devolvido p/ Correção'; $statusColor = '#dc3545'; 
                    } elseif ($s == 'atrasado' || $s == 'enviado') { 
                        $statusText = 'Aguardando Análise'; $statusColor = '#17a2b8'; 
                    }
                }
            ?>

            <div id="planning-<?= $period['id'] ?>" class="tab-content <?= $index === 0 ? 'active' : '' ?>">
                <div class="card" style="border-left: 5px solid <?= $statusColor ?>; position: relative; box-shadow: none; border: 1px solid var(--border-color);">
                    <h4 style="font-size: 1.1rem; margin-bottom: 0.5rem;"><?= htmlspecialchars($period['name']) ?></h4>
                    <p style="font-size: 0.9rem; color: var(--text-color); font-weight: normal; margin-bottom: 0.5rem;">
                        <?= htmlspecialchars($period['description']) ?>
                    </p>
                    <p style="font-size: 0.9rem; font-weight: bold; color: <?= $statusColor ?>;">
                        Prazo: <?= date('d/m/Y', strtotime($period['deadline'])) ?> 
                        <span style="font-size: 0.8rem; background: <?= $statusColor == 'green' ? '#d4edda' : ($statusColor == 'red' ? '#f8d7da' : '#fff3cd') ?>; padding: 2px 6px; border-radius: 4px; color: black; margin-left: 5px;">
                            <?= $statusText ?>
                        </span>
                    </p>


                    <?php if ($currentDoc): ?>
                        <div style="margin-top: 1rem; border-top: 1px solid var(--border-color); padding-top: 1rem; display: flex; align-items: center; justify-content: space-between; background: #fff; padding: 15px; border-radius: 5px; border: 1px solid #eee;">
                            <div>
                                <?php 
                                    $s = strtolower(trim($currentDoc['status']));
                                    $icon = 'fa-paper-plane';
                                    if ($s == 'aprovado') $icon = 'fa-trophy';
                                    elseif ($s == 'ajustado') $icon = 'fa-check-circle';
                                    elseif ($s == 'rejeitado') $icon = 'fa-undo';
                                    elseif (in_array($s, ['atrasado', 'enviado'])) $icon = 'fa-clock';
                                ?>
                                <h5 style="margin-bottom: 5px; color: <?= $statusColor ?>; font-weight: bold; font-size: 1rem;">
                                    <i class="fas <?= $icon ?>"></i> <?= $statusText ?>
                                </h5>
                                <a href="<?= url('uploads/' . $currentDoc['file_path']) ?>" target="_blank" style="text-decoration: none; color: var(--primary); font-weight: 500; font-size: 0.9rem;">
                                    <i class="fas fa-file-alt"></i> <?= htmlspecialchars($currentDoc['title'] ?: 'Arquivo de Planejamento') ?>
                                </a>
                                <br>
                                <small style="color: #666; font-size: 0.8rem;">Enviado em: <?= date('d/m/Y H:i', strtotime($currentDoc['submitted_at'])) ?></small>
                            </div>
                            <a href="<?= url('professor/upload/delete?id=' . $currentDoc['id']) ?>" onclick="return confirm('Excluir este arquivo?')" class="btn btn-sm" style="background-color: var(--danger); color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none;">
                                <i class="fas fa-trash"></i> Excluir
                            </a>
                        </div>
                    <?php else: ?>
                        <!-- Form de Upload Específico para este Planejamento -->
                        <form action="<?= url('professor/upload') ?>" method="POST" enctype="multipart/form-data" style="margin-top: 1rem; border-top: 1px solid var(--border-color); padding-top: 1rem;">
                            <input type="hidden" name="title" value="<?= htmlspecialchars($period['name']) ?>">
                            <input type="hidden" name="type" value="planejamento">
                            <input type="hidden" name="period_id" value="<?= $period['id'] ?>">
                            
                            <div class="form-group">
                                <label style="font-size: 0.9rem;">Enviar Arquivo</label>
                                <input type="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx" required style="font-size: 0.9rem; padding: 5px;">
                            </div>
                            <button type="submit" class="btn btn-primary" style="font-size: 0.9rem; padding: 0.5rem;">Enviar</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Histórico Section -->
<div class="list-section">
    <h3>Histórico de Envios</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>Data</th>
                <th>Título</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($documents)): ?>
                <tr><td colspan="4">Nenhum documento enviado.</td></tr>
            <?php else: ?>
                <?php foreach ($documents as $doc): ?>
                    <tr>
                        <td><?= date('d/m/Y H:i', strtotime($doc['submitted_at'])) ?></td>
                        <td><?= htmlspecialchars($doc['title']) ?></td>
                        <td>
                            <span class="status-badge status-<?= $doc['status'] ?>">
                                <?php 
                                    $statusLabels = [
                                        'enviado' => 'Enviado',
                                        'atrasado' => 'Enviado c/ Atraso',
                                        'aprovado' => 'Aprovado',
                                        'ajustado' => 'Aprovação c/ Ajustes',
                                        'rejeitado' => 'Devolvido p/ Correção'
                                    ];
                                    echo $statusLabels[$doc['status']] ?? ucfirst($doc['status']);
                                ?>
                            </span>
                            <?php if($doc['score_final'] > 0): ?>
                                <small style="display:block; color: #28a745; font-weight: bold; margin-top: 4px;">+<?= $doc['score_final'] ?> pts</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= url('uploads/' . $doc['file_path']) ?>" target="_blank" class="btn-icon"><i class="fas fa-eye"></i></a>
                            <?php if($doc['status'] != 'aprovado'): ?>
                                <a href="<?= url('professor/upload/delete?id=' . $doc['id']) ?>" onclick="return confirm('Excluir este arquivo?')" class="btn-icon" style="color: var(--danger);"><i class="fas fa-trash"></i></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

<?php if (!empty($coordinatorPhone)): ?>
    <a href="https://wa.me/55<?= preg_replace('/\D/', '', $coordinatorPhone) ?>" target="_blank" style="position: fixed; bottom: 20px; right: 20px; background-color: #25D366; color: white; padding: 15px 20px; border-radius: 50px; font-weight: bold; text-decoration: none; box-shadow: 0 4px 10px rgba(0,0,0,0.3); display: flex; align-items: center; gap: 10px; z-index: 1000; font-family: sans-serif;">
        <i class="fab fa-whatsapp" style="font-size: 1.5rem;"></i> Falar com Coordenação
    </a>
<?php endif; ?>

<script>
    // Password Toggle
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = "password";
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Tabs Logic
    function openTab(evt, tabId) {
        var i, tabcontent, tablinks;
        // Hide all tab content
        tabcontent = document.getElementsByClassName("tab-content");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].classList.remove("active");
        }
        
        // Deactivate all tab links
        tablinks = document.getElementsByClassName("tab-btn");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].classList.remove("active");
        }
        
        // Show the specific tab and activate the button
        document.getElementById(tabId).classList.add("active");
        evt.currentTarget.classList.add("active");
    }
</script>
