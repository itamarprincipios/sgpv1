<?php
/**
 * RAGController - Controlador principal do sistema RAG
 * 
 * Orquestra o fluxo completo:
 * 1. Identifica contexto necessário (escola, professor, rede)
 * 2. Recupera dados via ContextBuilder
 * 3. Formata prompt via PromptBuilder
 * 4. Consulta IA via AIService
 * 5. Retorna resposta formatada
 */

require_once __DIR__ . '/../Core/ContextBuilder.php';
require_once __DIR__ . '/../Core/PromptBuilder.php';
require_once __DIR__ . '/../Core/AIService.php';

class RAGController {
    private $contextBuilder;
    private $promptBuilder;
    private $aiService;
    
    public function __construct() {
        $this->contextBuilder = new ContextBuilder();
        $this->promptBuilder = new PromptBuilder();
        
        // Carregar .env para AIService
        $this->loadEnv();
        $this->aiService = new AIService();
    }
    
    private function loadEnv() {
        $envFile = __DIR__ . '/../../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    putenv(trim($key) . '=' . trim($value));
                }
            }
        }
    }
    
    /**
     * Endpoint principal para consultas RAG
     * Recebe JSON: { "question": "...", "filters": {...} }
     */
    public function query() {
        header('Content-Type: application/json');
        
        try {
            // Verificar autenticação (SEMED, Admin, Superadmin, Coordenador ou Supervisor Ed. Fis)
            $allowedRoles = ['semed', 'admin', 'superadmin', 'coordinator', 'supervisor_edfis'];
            if (!isset($_SESSION['user']['id']) || !in_array($_SESSION['user']['role'], $allowedRoles)) {
                throw new Exception('Acesso negado. Apenas usuários autorizados podem usar esta funcionalidade.');
            }
            
            // Receber dados da requisição
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                throw new Exception('Dados inválidos. Envie JSON com "question" e opcionalmente "filters".');
            }
            
            $question = $input['question'] ?? '';
            $filters = $input['filters'] ?? [];
            
            if (empty($question)) {
                throw new Exception('Pergunta não fornecida.');
            }
            
            // Se usuário é COORDENADOR, forçar filtro de escola
            if ($_SESSION['user']['role'] === 'coordinator') {
                // Buscar school_id do coordenador
                $schoolId = $this->getCoordinatorSchoolId($_SESSION['user']['id']);
                
                if (!$schoolId) {
                    throw new Exception('Coordenador não está vinculado a nenhuma escola.');
                }
                
                // Forçar filtro de escola (sobrescreve qualquer filtro enviado)
                $filters['school_id'] = $schoolId;
            } elseif ($_SESSION['user']['role'] === 'supervisor_edfis') {
                 // Supervisor Ed. Física recebe tratamento especial no buildContext
                 $filters['context_type'] = 'physical_education';
            }
            
            // Executar fluxo RAG
            $result = $this->executeRAG($question, $filters);
            
            // Salvar consulta no histórico
            $this->saveQuery($_SESSION['user']['id'], $question, $filters, $result['response']);
            
            echo json_encode([
                'success' => true,
                'question' => $question,
                'response' => $result['response'],
                'context_type' => $result['context_type'],
                'response_time_ms' => $result['response_time_ms']
            ], JSON_UNESCAPED_UNICODE);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * Executa o fluxo RAG completo
     * @param string $question Pergunta do usuário
     * @param array $filters Filtros de contexto
     * @return array Resultado com resposta e metadados
     */
    private function executeRAG($question, $filters) {
        $startTime = microtime(true);
        
        // 1. Identificar e recuperar contexto
        $context = $this->buildContext($filters);
        
        // 2. Construir prompt
        $prompt = $this->promptBuilder->buildAnalysisPrompt($context, $question);
        
        // 3. Consultar IA
        $response = $this->aiService->query($prompt);
        
        $endTime = microtime(true);
        $responseTime = round(($endTime - $startTime) * 1000, 2);
        
        return [
            'response' => $response,
            'context_type' => $context['tipo'] ?? 'desconhecido',
            'response_time_ms' => $responseTime
        ];
    }
    
    /**
     * Constrói contexto baseado nos filtros
     * @param array $filters Filtros (school_id, professor_id, etc)
     * @return array Contexto estruturado
     */
    private function buildContext($filters) {
        // Prioridade: Professor > Escola > Rede
        
        if (isset($filters['professor_id'])) {
            return $this->contextBuilder->getProfessorContext(
                $filters['professor_id'],
                $filters['period_id'] ?? null
            );
        }
        
        if (isset($filters['school_id'])) {
            return $this->contextBuilder->getSchoolContext($filters['school_id']);
        }
        
        if (isset($filters['context_type']) && $filters['context_type'] === 'physical_education') {
            return $this->contextBuilder->getPhysicalEducationContext();
        }
        
        // Se não especificou nada, retorna contexto da rede
        return $this->contextBuilder->getNetworkContext();
    }
    
    /**
     * Salva consulta no histórico
     * @param int $userId ID do usuário
     * @param string $question Pergunta
     * @param array $filters Filtros usados
     * @param string $response Resposta da IA
     */
    private function saveQuery($userId, $question, $filters, $response) {
        try {
            $db = Database::getInstance();
            $sql = "INSERT INTO ai_queries (user_id, question, context_filters, response, created_at) 
                    VALUES (:user_id, :question, :context_filters, :response, NOW())";
            
            $db->query($sql, [
                'user_id' => $userId,
                'question' => $question,
                'context_filters' => json_encode($filters),
                'response' => $response
            ]);
        } catch (Exception $e) {
            // Não falhar se não conseguir salvar histórico
            error_log("Erro ao salvar consulta: " . $e->getMessage());
        }
    }
    
    /**
     * Retorna histórico de consultas do usuário
     */
    public function getHistory() {
        header('Content-Type: application/json');
        
        try {
            if (!isset($_SESSION['user']['id'])) {
                throw new Exception('Não autenticado');
            }
            
            $db = Database::getInstance();
            $sql = "SELECT id, question, response, created_at 
                    FROM ai_queries 
                    WHERE user_id = :user_id 
                    ORDER BY created_at DESC 
                    LIMIT 20";
            
            $stmt = $db->query($sql, ['user_id' => $_SESSION['user']['id']]);
            $history = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true,
                'history' => $history
            ], JSON_UNESCAPED_UNICODE);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * Busca o school_id do coordenador
     * Verifica tanto na tabela users (school_id direto) quanto em user_schools (múltiplas escolas)
     * @param int $userId ID do coordenador
     * @return int|null ID da escola ou null se não encontrado
     */
    private function getCoordinatorSchoolId($userId) {
        try {
            $db = Database::getInstance();
            
            // OPÇÃO 1: Buscar school_id diretamente na tabela users
            $sql = "SELECT school_id FROM users WHERE id = :user_id AND school_id IS NOT NULL LIMIT 1";
            $stmt = $db->query($sql, ['user_id' => $userId]);
            $result = $stmt->fetch();
            
            if ($result && $result['school_id']) {
                return (int)$result['school_id'];
            }
            
            // OPÇÃO 2 (Fallback): Buscar na tabela user_schools (para usuários com múltiplas escolas)
            $sql = "SELECT school_id FROM user_schools WHERE user_id = :user_id LIMIT 1";
            $stmt = $db->query($sql, ['user_id' => $userId]);
            $result = $stmt->fetch();
            
            return $result ? (int)$result['school_id'] : null;
        } catch (Exception $e) {
            error_log("Erro ao buscar escola do coordenador: " . $e->getMessage());
            return null;
        }
    }
}
