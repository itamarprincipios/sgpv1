<!-- Widget IANNE - Supervisora Ed. Física -->
<div id="ianne-widget-edfis" class="ianne-widget">
    <div class="ianne-avatar" onclick="toggleIanneChat()">
        <img src="<?= url('public/img/ianne-avatar.png') ?>" alt="IANNE">
        <div class="status-indicator"></div>
    </div>
</div>

<div id="ianne-chat-modal" class="ianne-modal" style="display: none;">
    <div class="ianne-modal-content">
        <div class="ianne-header">
            <div class="ianne-title">
                <img src="<?= url('public/img/ianne-avatar.png') ?>" alt="IANNE" class="avatar-small">
                <div>
                    <strong>IANNE - Assistente Pedagógica IA</strong>
                    <small>Supervisão de Educação Física</small>
                </div>
            </div>
            <button onclick="toggleIanneChat()" class="close-btn">&times;</button>
        </div>
        
        <div class="ianne-messages" id="ianne-messages">
            <div class="message ianne-message">
                Olá! Sou a IANNE, sua assistente para supervisão de Educação Física. 
                Posso te ajudar com informações sobre professores, planejamentos e pontualidade. 
                Em que posso ajudar?
            </div>
        </div>
        
        <div class="ianne-input-area">
            <textarea id="ianne-input" placeholder="Faça sua pergunta..." rows="3"></textarea>
            <button onclick="sendIanneMessage()" class="send-btn">
                <i class="fas fa-paper-plane"></i> Perguntar à IANNE
            </button>
        </div>
    </div>
</div>

<link rel="stylesheet" href="<?= url('public/css/ianne-coordinator.css') ?>?v=<?= time() ?>">
<script src="<?= url('public/js/ianne-coordinator.js') ?>"></script>

<script>
// Sobrescrever contexto para filtrar apenas Ed. Fis
window.IANNE_CONTEXT = 'supervisor_edfis';
</script>
