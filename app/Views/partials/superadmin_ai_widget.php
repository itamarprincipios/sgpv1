<?php
// Buscar TODAS as escolas do sistema
$schoolModel = new School();
$allSchools = $schoolModel->all();
?>

<style>
.ianne-school-selector {
    margin-bottom: 15px;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 8px;
}

.ianne-school-selector label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: #374151;
    font-size: 0.9rem;
}

.ianne-school-selector select {
    width: 100%;
    padding: 10px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.95rem;
    background: white;
    cursor: pointer;
}

.ianne-school-selector select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}
</style>

<div class="ai-widget">
    <div class="ai-header">
        <div class="ai-avatar">
            <i class="fas fa-robot"></i>
        </div>
        <div>
            <h3>IANNE</h3>
            <p>Assistente Pedagógica IA - Visão Completa</p>
        </div>
    </div>
    
    <!-- SELETOR DE ESCOLA -->
    <div class="ianne-school-selector">
        <label>
            <i class="fas fa-school"></i> Filtrar por escola:
        </label>
        <select id="ianne-school-filter">
            <option value="">🌐 Toda a rede municipal (<?= count($allSchools) ?> escolas)</option>
            <?php foreach ($allSchools as $school): ?>
                <option value="<?= $school['id'] ?>">
                    <?= htmlspecialchars($school['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="ai-chat" id="ianne-chat">
        <div class="ai-message ai-message-assistant">
            <p>Olá, Superadmin! Tenho acesso a dados de toda a rede municipal (<?= count($allSchools) ?> escolas). 
            Você pode consultar uma escola específica ou analisar dados de toda a rede. Como posso ajudar?</p>
        </div>
    </div>
    
    <div class="ai-input-container">
        <textarea id="ianne-question" placeholder="Digite sua pergunta..." rows="2"></textarea>
        <button onclick="askIanneSuperadmin()" class="ai-send-btn">
            <i class="fas fa-paper-plane"></i>
        </button>
    </div>
</div>

<script>
function askIanneSuperadmin() {
    const question = document.getElementById('ianne-question').value.trim();
    if (!question) return;
    
    const schoolId = document.getElementById('ianne-school-filter').value;
    
    const filters = {};
    if (schoolId) {
        filters.school_id = parseInt(schoolId);
    }
    
    // Adicionar mensagem do usuário
    addIanneMessage(question, 'user');
    document.getElementById('ianne-question').value = '';
    
    // Mostrar loading
    const loadingId = addIanneMessage('Analisando dados...', 'assistant', true);
    
    fetch('<?= url('rag/query') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ question, filters })
    })
    .then(res => res.json())
    .then(data => {
        removeIanneMessage(loadingId);
        if (data.success) {
            addIanneMessage(data.response, 'assistant');
        } else {
            addIanneMessage('Erro: ' + data.error, 'assistant error');
        }
    })
    .catch(err => {
        removeIanneMessage(loadingId);
        addIanneMessage('Erro ao conectar com a IANNE.', 'assistant error');
    });
}

function addIanneMessage(text, type, isLoading = false) {
    const chat = document.getElementById('ianne-chat');
    const msgId = 'msg-' + Date.now();
    const div = document.createElement('div');
    div.id = msgId;
    div.className = 'ai-message ai-message-' + type;
    div.innerHTML = '<p>' + text + '</p>';
    chat.appendChild(div);
    chat.scrollTop = chat.scrollHeight;
    return msgId;
}

function removeIanneMessage(msgId) {
    const msg = document.getElementById(msgId);
    if (msg) msg.remove();
}

// Permitir envio com Enter (Shift+Enter para nova linha)
document.getElementById('ianne-question').addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        askIanneSuperadmin();
    }
});
</script>
