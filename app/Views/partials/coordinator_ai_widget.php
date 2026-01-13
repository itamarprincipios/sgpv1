<?php
/**
 * IANNE Coordinator Widget - Avatar Flutuante com Modal
 * Componente reutilizável para todas as páginas do coordenador
 */

// Verificar se usuário é coordenador ou supervisor ed. fis
$user = auth(); // Get authenticated user
$allowedRoles = ['coordinator', 'supervisor_edfis'];
if (!$user || !in_array($user['role'], $allowedRoles)) {
    return; // Não exibir se não for autorizado
}
?>

<!-- IANNE Floating Avatar Widget -->
<link rel="stylesheet" href="/css/ianne-coordinator.css">

<!-- Avatar Flutuante (FAB) -->
<div id="ianne-fab" onclick="openIanneModal()">
    <img id="ianne-fab-avatar" src="/img/ianne-avatar.png" alt="IANNE - Assistente IA" title="Clique para falar com a IANNE">
    <span id="ianne-badge">3</span> <!-- Badge oculto por padrão -->
</div>

<!-- Modal Overlay -->
<div id="ianne-modal-overlay" onclick="closeIanneModal()">
    <div id="ianne-modal" onclick="event.stopPropagation()">
        <!-- Header -->
        <div id="ianne-modal-header">
            <img src="/img/ianne-avatar.png" alt="IANNE">
            <div>
                <h3>🤖 IANNE - Assistente Pedagógica IA</h3>
                <p>Análise inteligente dos planejamentos da sua escola</p>
            </div>
            <button id="ianne-close-btn" onclick="closeIanneModal()" title="Fechar">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <!-- Body -->
        <div id="ianne-modal-body">
            <!-- Campo de Pergunta -->
            <div id="ianne-question-box">
                <label for="ianne-question">💬 Faça sua pergunta:</label>
                <textarea 
                    id="ianne-question" 
                    placeholder="Ex: Quantos professores enviaram planejamentos este bimestre? Quais metodologias estão sendo mais utilizadas?"
                ></textarea>
            </div>
            
            <!-- Botão Perguntar -->
            <button id="ianne-ask-btn" onclick="askIanne()">
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
