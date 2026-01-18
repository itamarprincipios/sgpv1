<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="dashboard-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="margin-bottom: 5px;"><?= htmlspecialchars($planning['name']) ?></h2>
            <p style="color: #666;"><?= htmlspecialchars($planning['description']) ?> | Prazo: <?= date('d/m/Y', strtotime($planning['deadline'])) ?></p>
        </div>
        <a href="<?= url('supervisor-monitor/plannings') ?>" class="btn btn-secondary" style="width: auto; background-color: #6c757d;">Voltar</a>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb; display: flex; align-items: center; gap: 10px; font-weight: 500;">
        <i class="fas fa-check-circle"></i> <?= $_SESSION['success'] ?>
        <?php unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<?php
    // Logic to group classes by "Grade" (e.g. "1º Ano")
    // Expected format: "1º Ano A", "2º Ano B" -> Prefix: "1º Ano", "2º Ano"
    $tabsData = [];
    foreach ($groupedData as $className => $records) {
        $className = $className ?: 'Sem Turma';
        $prefix = $className;
        if (preg_match('/^(.*)\s+[A-Z]$/', $className, $matches)) {
            $prefix = trim($matches[1]);
        }
        $prefix = mb_convert_case($prefix, MB_CASE_TITLE, "UTF-8");
        $tabsData[$prefix][$className] = $records;
    }
    ksort($tabsData);
    if (empty($tabsData)) $tabsData['Geral'] = $groupedData;
?>

<style>
    .sub-tabs {
        display: flex;
        gap: 5px;
        border-bottom: 3px solid #eee; /* Light gray line for bottom */
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    .sub-tab-btn {
        padding: 8px 16px;
        background: #f1f1f1;
        border: none;
        cursor: pointer;
        border-radius: 5px 5px 0 0;
        font-weight: 500;
        color: #555;
        transition: 0.2s;
    }
    .sub-tab-btn:hover {
        background: #e2e2e2;
    }
    .sub-tab-btn.active {
        background: #11998e; /* Green primary for Monitor */
        color: white;
    }
    .sub-tab-content {
        display: none;
        animation: fadeIn 0.3s;
    }
    .sub-tab-content.active {
        display: block;
    }
</style>

<div class="list-section">
    <?php if(empty($groupedData)): ?>
        <p>Nenhuma turma encontrada.</p>
    <?php else: ?>
        
        <!-- Tabs Buttons -->
        <div class="sub-tabs">
            <?php $first = true; foreach(array_keys($tabsData) as $tabName): ?>
                <button class="sub-tab-btn <?= $first ? 'active' : '' ?>" onclick="openSubTab(event, 'subtab-<?= md5($tabName) ?>')">
                    <?= htmlspecialchars($tabName) ?>
                </button>
            <?php $first = false; endforeach; ?>
        </div>

        <!-- Tabs Content -->
        <?php $first = true; foreach($tabsData as $tabName => $classes): ?>
            <div id="subtab-<?= md5($tabName) ?>" class="sub-tab-content <?= $first ? 'active' : '' ?>">
                <?php foreach($classes as $className => $records): ?>
                    <div class="class-block" style="margin-bottom: 20px; background: #fff; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
                        <div style="background: #f8f9fa; padding: 10px 20px; font-weight: bold; font-size: 1.0rem; border-bottom: 1px solid #eee; color: #333;">
                            <i class="fas fa-users"></i> Turma: <?= htmlspecialchars($className) ?>
                        </div>
                        
                        <table class="data-table" style="margin: 0; border: none;">
                            <thead>
                                <tr>
                                    <th style="width: 30%;">Professor</th>
                                    <th style="width: 20%;">Entregue em</th>
                                    <th style="width: 20%;">Status</th>
                                    <th style="width: 30%;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($records as $rec): ?>
                                    <?php if(!$rec['professor_name']): ?>
                                        <tr><td colspan="4" style="text-align:center; color: #999;">Nenhum professor nesta turma</td></tr>
                                    <?php else: ?>
                                        <tr>
                                            <td><?= htmlspecialchars($rec['professor_name']) ?></td>
                                            <td>
                                                <?= $rec['submitted_at'] ? date('d/m/Y H:i', strtotime($rec['submitted_at'])) : '-' ?>
                                            </td>
                                            <td>
                                                <?php if($rec['status']): ?>
                                                    <span class="status-badge status-<?= $rec['status'] ?>"><?= ucfirst($rec['status']) ?></span>
                                                <?php else: ?>
                                                    <span class="status-badge" style="background: #eee; color: #666;">Pendente</span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="vertical-align: middle; padding: 15px;">
                                                <div style="display: flex; flex-direction: column; gap: 10px;">
                                                    <?php if(isset($rec['file_path']) && $rec['file_path']): ?>
                                                        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                                            <a href="<?= @url('uploads/' . $rec['file_path']) ?>" target="_blank" class="btn" title="Ver Arquivo" style="background: #e3f2fd; color: #1976d2; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 0.8rem; display: flex; align-items: center; gap: 5px; border: 1px solid #bbdefb; width: auto; margin: 0;">
                                                                <i class="fas fa-eye"></i> Visualizar
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php 
                                                        $phone = preg_replace('/[^0-9]/', '', $rec['whatsapp'] ?? '');
                                                        if(!empty($phone)): 
                                                            $msg = "Olá! 👋\nConsta em nosso sistema que o \"{$planning['name']}\" ainda está pendente ou precisa de atenção.\nPor favor, verifique no seu portal.";
                                                            $encodedMsg = urlencode($msg);
                                                    ?>
                                                        <div style="margin-top: 5px;">
                                                            <button type="button" onclick="openWhatsappModal('<?= $phone ?>', '<?= htmlspecialchars($rec['professor_name']) ?>')" style="background: none; border: none; color: #128C7E; cursor: pointer; text-decoration: none; font-size: 0.85rem; font-weight: bold; display: flex; align-items: center; gap: 5px; padding: 0;" title="Falar com o professor">
                                                                <i class="fab fa-whatsapp" style="font-size: 1.1rem;"></i> Falar com o professor
                                                            </button>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
<?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php $first = false; endforeach; ?>
    <?php endif; ?>
</div>

<script>
    function openSubTab(evt, tabId) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("sub-tab-content");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].classList.remove("active");
        }
        tablinks = document.getElementsByClassName("sub-tab-btn");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].classList.remove("active");
        }
        document.getElementById(tabId).classList.add("active");
        if (evt) evt.currentTarget.classList.add("active");
    }

    function openWhatsappModal(phone, professorName) {
        document.getElementById('wsPhone').value = phone;
        document.getElementById('wsProfName').innerText = professorName;
        document.getElementById('wsProfNameDisplay').innerText = professorName;
        document.getElementById('wsCustomMsg').value = ""; 
        document.getElementById('whatsappModal').style.display = "block";
    }

    function closeWhatsappModal() {
        document.getElementById('whatsappModal').style.display = "none";
    }

    function sendWhatsapp() {
        const phone = document.getElementById('wsPhone').value;
        const profName = document.getElementById('wsProfName').innerText;
        const customMsg = document.getElementById('wsCustomMsg').value;
        const signature = "\n\nMensagem enviada automaticamente pelo Sistema SGP-Monitoria";
        
        const fullMsg = "Ola prof. " + profName + "\n" + customMsg + signature;
        const encodedMsg = encodeURIComponent(fullMsg);
        
        const url = "https://web.whatsapp.com/send?phone=+55" + phone + "&text=" + encodedMsg;
        window.open(url, '_blank');
        closeWhatsappModal();
    }

    window.onclick = function(event) {
        var modal = document.getElementById('whatsappModal');
        if (event.target == modal) {
            closeWhatsappModal();
        }
    }
</script>

<!-- WhatsApp Modal -->
<div id="whatsappModal" class="modal" style="display:none; position:fixed; z-index:1001; left:0; top:0; width:100%; height:100%; overflow:auto; background-color: rgba(0,0,0,0.4);">
    <div class="modal-content" style="background-color: #fefefe; margin: 10% auto; padding: 25px; border: 1px solid #888; width: 90%; max-width: 500px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.3);">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px; color: #128C7E;">
            <i class="fab fa-whatsapp" style="font-size: 2rem;"></i>
            <h3 style="margin: 0;">Falar com Prof. <span id="wsProfName"></span></h3>
        </div>
        
        <input type="hidden" id="wsPhone">
        
        <div class="form-group">
            <label style="font-weight: bold; display: block; margin-bottom: 8px; color: #555;">Sua Mensagem:</label>
            <div style="background: #f9f9f9; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-style: italic; color: #777; margin-bottom: 5px; font-size: 0.9rem;">
                Ola prof. <span id="wsProfNameDisplay"></span>
            </div>
            <textarea id="wsCustomMsg" class="form-control" placeholder="Digite sua mensagem aqui..." style="width: 100%; height: 120px; padding: 12px; border: 1px solid #ccc; border-radius: 6px; font-family: inherit; resize: vertical;"></textarea>
            <div style="background: #f9f9f9; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-style: italic; color: #777; margin-top: 5px; font-size: 0.8rem;">
                Mensagem enviada automaticamente pelo Sistema SGP-Monitoria
            </div>
        </div>
        
        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 25px;">
            <button type="button" class="btn" onclick="closeWhatsappModal()" style="background: #f1f1f1; color: #333; padding: 10px 20px; border-radius: 6px; border: 1px solid #ddd;">Cancelar</button>
            <button type="button" class="btn" onclick="sendWhatsapp()" style="background: #25D366; color: white; padding: 10px 20px; border-radius: 6px; border: none; font-weight: bold; display: flex; align-items: center; gap: 8px;">
                <i class="fab fa-whatsapp"></i> Abrir WhatsApp
            </button>
        </div>
    </div>
</div>

<?php 
$user = auth();
if ($user && ($user['role'] === 'supervisor_edfis' || $user['role'] === 'supervisor_monitor')) {
    include __DIR__ . '/../partials/coordinator_ai_widget.php';
}
?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
