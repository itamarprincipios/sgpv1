<?php
// Verificar autenticação já foi feita no controller
$user = auth();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Análise Pedagógica com IA - SEMED</title>
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .ai-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .ai-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .ai-header h1 {
            margin: 0 0 10px 0;
            font-size: 28px;
        }
        
        .ai-header p {
            margin: 0;
            opacity: 0.9;
        }
        
        .ai-main {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 20px;
        }
        
        .ai-sidebar {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            height: fit-content;
        }
        
        .ai-sidebar h3 {
            margin-top: 0;
            color: #667eea;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        
        .filter-group {
            margin-bottom: 20px;
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        
        .filter-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .ai-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .question-box {
            margin-bottom: 20px;
        }
        
        .question-box label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
            color: #333;
            font-size: 16px;
        }
        
        .question-box textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            font-family: inherit;
            resize: vertical;
            min-height: 100px;
            box-sizing: border-box;
        }
        
        .question-box textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn-ask {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .btn-ask:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
        }
        
        .btn-ask:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .response-container {
            margin-top: 30px;
            display: none;
        }
        
        .response-box {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 25px;
            border-radius: 10px;
            border-left: 5px solid #667eea;
        }
        
        .response-box h3 {
            margin-top: 0;
            color: #667eea;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .response-text {
            line-height: 1.8;
            color: #333;
            white-space: pre-wrap;
        }
        
        .loading {
            text-align: center;
            padding: 40px;
        }
        
        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .history-container {
            margin-top: 40px;
        }
        
        .history-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 3px solid #667eea;
        }
        
        .history-item .question {
            font-weight: bold;
            color: #667eea;
            margin-bottom: 8px;
        }
        
        .history-item .answer {
            color: #666;
            font-size: 14px;
        }
        
        .history-item .timestamp {
            color: #999;
            font-size: 12px;
            margin-top: 8px;
        }
        
        .examples {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .examples h4 {
            margin-top: 0;
            color: #856404;
        }
        
        .examples ul {
            margin: 10px 0 0 0;
            padding-left: 20px;
        }
        
        .examples li {
            margin-bottom: 8px;
            color: #856404;
            cursor: pointer;
        }
        
        .examples li:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .ai-main {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php require __DIR__ . '/../layouts/header.php'; ?>
    
    <div class="ai-container">
        <!-- Header com IANNE -->
        <div class="ai-header">
            <div style="display: flex; align-items: center; gap: 30px;">
                <div class="ianne-avatar">
                    <img src="/img/ianne.jpg" alt="IANNE - Assistente Pedagógica" style="width: 120px; height: 120px; border-radius: 50%; border: 4px solid white; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                </div>
                <div style="flex: 1;">
                    <h1 style="margin: 0 0 10px 0; font-size: 32px;">👋 Olá! Eu sou a IANNE</h1>
                    <p style="margin: 0 0 5px 0; font-size: 18px; opacity: 0.95;">
                        <strong>I</strong>nteligência <strong>A</strong>rtificial para A<strong>N</strong>álise <strong>E</strong>ducacional
                    </p>
                    <p style="margin: 0; font-size: 15px; opacity: 0.85;">
                        Estou aqui para ajudar você a analisar planejamentos, identificar metodologias e tomar decisões pedagógicas baseadas em dados! 💡
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Dicas rápidas -->
        <div style="background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%); padding: 20px; border-radius: 10px; margin-bottom: 30px; border-left: 4px solid #3b82f6;">
            <h3 style="margin: 0 0 10px 0; color: #1e40af; display: flex; align-items: center; gap: 10px;">
                <span>💬</span>
                <span>Como posso te ajudar hoje?</span>
            </h3>
            <p style="margin: 0; color: #1e3a8a; line-height: 1.6;">
                Você pode me perguntar sobre <strong>estatísticas de envio</strong>, <strong>metodologias utilizadas</strong>, 
                <strong>validação BNCC</strong>, <strong>desempenho de escolas</strong> e muito mais! 
                Use os filtros ao lado para análises específicas ou deixe em branco para uma visão geral da rede.
            </p>
        </div>
        
        <div class="ai-main">
            <!-- Sidebar com filtros -->
            <div class="ai-sidebar">
                <h3>🔍 Filtros de Contexto</h3>
                
                <div class="filter-group">
                    <label for="filter-school">Escola:</label>
                    <select id="filter-school">
                        <option value="">Toda a rede</option>
                        <?php foreach ($schools as $school): ?>
                            <option value="<?= $school['id'] ?>"><?= htmlspecialchars($school['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="filter-professor">Professor:</label>
                    <select id="filter-professor">
                        <option value="">Todos os professores</option>
                        <?php foreach ($professors as $prof): ?>
                            <option value="<?= $prof['id'] ?>"><?= htmlspecialchars($prof['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <p style="font-size: 12px; color: #666; margin-top: 20px;">
                    💡 <strong>Dica:</strong> Selecione filtros para análises mais específicas ou deixe em branco para análise geral da rede.
                </p>
            </div>
            
            <!-- Conteúdo principal -->
            <div class="ai-content">
                <!-- Formulário de pergunta -->
                <div class="question-box">
                    <label for="question">Faça sua pergunta sobre o sistema:</label>
                    <textarea id="question" placeholder="Digite qualquer pergunta sobre professores, planejamentos, escolas, estatísticas, desempenho..."></textarea>
                </div>
                
                <button class="btn-ask" onclick="askAI()">
                    🚀 Perguntar à IA
                </button>
                
                <!-- Container de resposta -->
                <div class="response-container" id="response-container">
                    <div class="response-box">
                        <h3>
                            <img src="/img/ianne.jpg" alt="IANNE" style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid #667eea;">
                            <span>Resposta da IANNE</span>
                        </h3>
                        <div class="response-text" id="response-text"></div>
                    </div>
                </div>
                
                <!-- Loading -->
                <div class="loading" id="loading" style="display: none;">
                    <div class="loading-spinner"></div>
                    <p>Analisando dados e consultando IA...</p>
                </div>
                
                <!-- Histórico -->
                <div class="history-container" id="history-container" style="display: none;">
                    <h3>📜 Histórico de Consultas</h3>
                    <div id="history-list"></div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Dados dos professores por escola
        const professorsBySchool = <?= json_encode(array_reduce($professors, function($carry, $prof) {
            $schoolId = $prof['school_id'] ?? 0;
            if (!isset($carry[$schoolId])) {
                $carry[$schoolId] = [];
            }
            $carry[$schoolId][] = $prof;
            return $carry;
        }, [])) ?>;
        
        // Todos os professores
        const allProfessors = <?= json_encode($professors) ?>;
        
        // Filtrar professores quando escola é selecionada
        document.getElementById('filter-school').addEventListener('change', function() {
            const schoolId = this.value;
            const professorSelect = document.getElementById('filter-professor');
            
            // Limpar opções atuais (exceto "Todos")
            professorSelect.innerHTML = '<option value="">Todos os professores</option>';
            
            if (schoolId === '') {
                // Se nenhuma escola selecionada, mostrar todos
                allProfessors.forEach(prof => {
                    const option = document.createElement('option');
                    option.value = prof.id;
                    option.textContent = prof.name;
                    professorSelect.appendChild(option);
                });
            } else {
                // Mostrar apenas professores da escola selecionada
                const professors = professorsBySchool[schoolId] || [];
                professors.forEach(prof => {
                    const option = document.createElement('option');
                    option.value = prof.id;
                    option.textContent = prof.name;
                    professorSelect.appendChild(option);
                });
                
                if (professors.length === 0) {
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'Nenhum professor nesta escola';
                    option.disabled = true;
                    professorSelect.appendChild(option);
                }
            }
        });
        
        // Carregar histórico ao abrir a página
        loadHistory();
        
        async function askAI() {
            const question = document.getElementById('question').value.trim();
            
            if (!question) {
                alert('Por favor, digite uma pergunta.');
                return;
            }
            
            // Preparar filtros
            const filters = {};
            const schoolId = document.getElementById('filter-school').value;
            const professorId = document.getElementById('filter-professor').value;
            
            if (schoolId) filters.school_id = parseInt(schoolId);
            if (professorId) filters.professor_id = parseInt(professorId);
            
            // Mostrar loading
            document.getElementById('loading').style.display = 'block';
            document.getElementById('response-container').style.display = 'none';
            document.querySelector('.btn-ask').disabled = true;
            
            try {
                const response = await fetch('/api/rag.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        question: question,
                        filters: filters
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Mostrar resposta
                    document.getElementById('response-text').textContent = data.response;
                    document.getElementById('response-container').style.display = 'block';
                    
                    // Recarregar histórico
                    loadHistory();
                } else {
                    alert('Erro: ' + data.error);
                }
            } catch (error) {
                alert('Erro ao consultar IA: ' + error.message);
            } finally {
                document.getElementById('loading').style.display = 'none';
                document.querySelector('.btn-ask').disabled = false;
            }
        }
        
        async function loadHistory() {
            try {
                const response = await fetch('/api/rag.php', {
                    method: 'GET'
                });
                
                const data = await response.json();
                
                if (data.success && data.history.length > 0) {
                    const historyList = document.getElementById('history-list');
                    historyList.innerHTML = '';
                    
                    data.history.forEach(item => {
                        const div = document.createElement('div');
                        div.className = 'history-item';
                        div.innerHTML = `
                            <div class="question">${escapeHtml(item.question)}</div>
                            <div class="answer">${escapeHtml(item.response.substring(0, 200))}...</div>
                            <div class="timestamp">${new Date(item.created_at).toLocaleString('pt-BR')}</div>
                        `;
                        historyList.appendChild(div);
                    });
                    
                    document.getElementById('history-container').style.display = 'block';
                }
            } catch (error) {
                console.error('Erro ao carregar histórico:', error);
            }
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>
</html>
