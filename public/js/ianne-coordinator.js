/**
 * IANNE Coordinator - JavaScript
 * Lógica de interação com o avatar flutuante e modal
 */

// Abrir modal
function openIanneModal() {
    const overlay = document.getElementById('ianne-modal-overlay');
    overlay.classList.add('show');

    // Focar no campo de pergunta
    setTimeout(() => {
        document.getElementById('ianne-question').focus();
    }, 300);

    // Carregar histórico
    loadIanneHistory();
}

// Fechar modal
function closeIanneModal() {
    const overlay = document.getElementById('ianne-modal-overlay');
    overlay.classList.remove('show');
}

// Fechar com tecla ESC
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeIanneModal();
    }
});

// Perguntar à IANNE
async function askIanne() {
    const questionField = document.getElementById('ianne-question');
    const question = questionField.value.trim();

    if (!question) {
        alert('Por favor, digite uma pergunta.');
        return;
    }

    // Mostrar loading
    document.getElementById('ianne-loading').classList.add('show');
    document.getElementById('ianne-response-container').classList.remove('show');
    document.getElementById('ianne-ask-btn').disabled = true;

    try {
        // Sanitizar a pergunta para evitar problemas com JSON
        const sanitizedQuestion = question.replace(/[\r\n]+/g, ' ').trim();

        const response = await fetch('/api/rag.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json; charset=utf-8'
            },
            body: JSON.stringify({
                question: sanitizedQuestion,
                filters: {} // Filtros serão aplicados automaticamente pelo backend
            })
        });

        const data = await response.json();

        if (data.success) {
            // Exibir resposta
            document.getElementById('ianne-response-text').textContent = data.response;
            document.getElementById('ianne-response-container').classList.add('show');

            // Recarregar histórico
            loadIanneHistory();

            // Limpar campo de pergunta
            questionField.value = '';
        } else {
            alert('Erro: ' + (data.error || 'Erro desconhecido'));
            console.error('Erro da API:', data);
        }
    } catch (error) {
        console.error('Erro ao consultar IANNE:', error);
        alert('Erro ao consultar IA. Verifique sua conexão e tente novamente.\n\nDetalhes: ' + error.message);
    } finally {
        // Ocultar loading
        document.getElementById('ianne-loading').classList.remove('show');
        document.getElementById('ianne-ask-btn').disabled = false;
    }
}

// Carregar histórico de consultas
async function loadIanneHistory() {
    try {
        const response = await fetch('/api/rag.php', {
            method: 'GET'
        });

        const data = await response.json();

        if (data.success && data.history.length > 0) {
            const historyList = document.getElementById('ianne-history-list');
            historyList.innerHTML = '';

            // Mostrar apenas últimas 5
            const recent = data.history.slice(0, 5);

            recent.forEach(item => {
                const div = document.createElement('div');
                div.className = 'ianne-history-item';
                div.onclick = () => reuseQuestion(item.question);

                const question = document.createElement('div');
                question.className = 'ianne-history-question';
                question.textContent = item.question;

                const answer = document.createElement('div');
                answer.className = 'ianne-history-answer';
                answer.textContent = item.response.substring(0, 100) + '...';

                const time = document.createElement('div');
                time.className = 'ianne-history-time';
                time.textContent = formatDateTime(item.created_at);

                div.appendChild(question);
                div.appendChild(answer);
                div.appendChild(time);

                historyList.appendChild(div);
            });

            document.getElementById('ianne-history-container').classList.add('show');
        }
    } catch (error) {
        console.error('Erro ao carregar histórico:', error);
    }
}

// Reutilizar pergunta do histórico
function reuseQuestion(question) {
    document.getElementById('ianne-question').value = question;
    document.getElementById('ianne-question').focus();
}

// Formatar data/hora
function formatDateTime(datetime) {
    const date = new Date(datetime);
    const now = new Date();
    const diff = now - date;
    const hours = Math.floor(diff / (1000 * 60 * 60));

    if (hours < 1) {
        const minutes = Math.floor(diff / (1000 * 60));
        return minutes + ' min atrás';
    }

    if (hours < 24) {
        return hours + ' hora(s) atrás';
    }

    return date.toLocaleDateString('pt-BR') + ' ' + date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
}

// Permitir Enter com Ctrl para enviar
document.addEventListener('DOMContentLoaded', function () {
    const questionField = document.getElementById('ianne-question');

    if (questionField) {
        questionField.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && e.ctrlKey) {
                e.preventDefault();
                askIanne();
            }
        });
    }
});
