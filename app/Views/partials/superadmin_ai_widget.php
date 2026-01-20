<?php
/**
 * IANNE Superadmin Widget - Avatar Flutuante com Modal + Seletor de Escola
 */

// Verificar se usuário é superadmin
$user = auth();
if (!$user || $user['role'] !== 'superadmin') {
    return;
}

// Buscar TODAS as escolas do sistema
$schoolModel = new School();
$allSchools = $schoolModel->all();
?>

<!-- IANNE Floating Avatar Widget -->
<link rel="stylesheet" href="/css/ianne-coordinator.css">

<!-- Avatar Flutuante (FAB) -->
<div id="ianne-fab" onclick="openIanneModal()">
    <img id="ianne-fab-avatar" src="/img/ianne-avatar.png" alt="IANNE - Assistente IA" title="Clique para falar com a IANNE">
    <span id="ianne-badge">3</span>
</div>

<!-- Modal Overlay -->
<div id="ianne-modal-overlay" onclick="closeIanneModal()">
    <div id="ianne-modal" onclick="event.stopPropagation()">
        <!-- Header -->
        <div id="ianne-modal-header">
            <img src="/img/ianne-avatar.png" alt="IANNE">
            <div>
                <h3>🤖 IANNE - Assistente Pedagógica IA</h3>
                <p>Visão completa da rede municipal</p>
            </div>
            <button id="ianne-close-btn" onclick="closeIanneModal()" title="Fechar">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <!-- Body -->
        <div id="ianne-modal-body">
            <!-- Seletor de Escola -->
            <div style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #1e293b;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #374151; font-size: 0.9rem;">
                    <i class="fas fa-school"></i> Filtrar por escola:
                </label>
                <select id="ianne-school-filter" style="width: 100%; padding: 10px; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 0.95rem; background: white; cursor: pointer;">
                    <option value="">🌐 Toda a rede municipal (<?= count($allSchools) ?> escolas)</option>
                    <?php foreach ($allSchools as $school): ?>
                        <option value="<?= $school['id'] ?>">
                            <?= htmlspecialchars($school['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Campo de Pergunta -->
            <div id="ianne-question-box">
                <label for="ianne-question">💬 Faça sua pergunta:</label>
                <textarea 
                    id="ianne-question" 
                    placeholder="Ex: Qual o ranking de escolas mais pontuais? Quantos professores temos na rede?"
                ></textarea>
            </div>
            
            <!-- Botão Perguntar -->
            <button id="ianne-ask-btn" onclick="askIanneSuperadmin()">
                🚀 Perguntar à IANNE
            </button>
            
            <!-- Loading -->
            <div id="ianne-loading">
                <div class="ianne-spinner"></div>
                <p>Analisando dados e consultando IA...</p>
            </div>
            
            <!-- Resposta -->
            <div id="ianne-response-container">
                <div id="ianne-response-box">
                    <h4>
                        <i class="fas fa-brain"></i>
                        Resposta da IANNE
                    </h4>
                    <div id="ianne-response-text"></div>
                </div>
            </div>
            
            <!-- Histórico -->
            <div id="ianne-history-container">
                <h4>
                    <i class="fas fa-history"></i>
                    Consultas recentes
                </h4>
                <div id="ianne-history-list"></div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script src="/js/ianne-coordinator.js"></script>
<script>
// Override askIanne para incluir school_id
function askIanneSuperadmin() {
    const question = document.getElementById('ianne-question').value.trim();
    if (!question) {
        alert('Por favor, digite uma pergunta.');
        return;
    }
    
    const schoolId = document.getElementById('ianne-school-filter').value;
    
    // Mostrar loading
    document.getElementById('ianne-loading').style.display = 'block';
    document.getElementById('ianne-response-container').style.display = 'none';
    document.getElementById('ianne-ask-btn').disabled = true;
    
    const filters = {};
    if (schoolId) {
        filters.school_id = parseInt(schoolId);
    }
    
    fetch('<?= url('rag/query') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ question, filters })
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('ianne-loading').style.display = 'none';
        document.getElementById('ianne-ask-btn').disabled = false;
        
        if (data.success) {
            document.getElementById('ianne-response-text').innerHTML = data.response.replace(/\n/g, '<br>');
            document.getElementById('ianne-response-container').style.display = 'block';
            
            // Limpar campo
            document.getElementById('ianne-question').value = '';
            
            // Atualizar histórico se a função existir
            if (typeof loadHistory === 'function') {
                loadHistory();
            }
        } else {
            document.getElementById('ianne-response-text').innerHTML = '<p style="color: #e74c3c;">❌ Erro: ' + data.error + '</p>';
            document.getElementById('ianne-response-container').style.display = 'block';
        }
    })
    .catch(err => {
        document.getElementById('ianne-loading').style.display = 'none';
        document.getElementById('ianne-ask-btn').disabled = false;
        document.getElementById('ianne-response-text').innerHTML = '<p style="color: #e74c3c;">❌ Erro ao conectar com a IANNE.</p>';
        document.getElementById('ianne-response-container').style.display = 'block';
    });
}

// Permitir Enter para enviar
document.getElementById('ianne-question').addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        askIanneSuperadmin();
    }
});
</script>
