<?php

class AIService {
    private $apiKey;
    private $model;
    private $maxTokens;
    private $temperature;
    
    public function __construct() {
        $this->loadEnv();
        $this->apiKey = getenv('OPENAI_API_KEY');
        $this->model = getenv('OPENAI_MODEL') ?: 'gpt-4o-mini';
        $this->maxTokens = (int)(getenv('OPENAI_MAX_TOKENS') ?: 1000);
        $this->temperature = (float)(getenv('OPENAI_TEMPERATURE') ?: 0.3);
        
        if (empty($this->apiKey) || $this->apiKey === 'sua-chave-aqui') {
            throw new Exception('OPENAI_API_KEY não configurada no arquivo .env');
        }
    }
    
    private function loadEnv() {
        $envFile = __DIR__ . '/../../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                // Ignorar comentários
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }
                
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);
                    putenv("$key=$value");
                }
            }
        }
    }
    
    /**
     * Envia uma pergunta para a OpenAI e retorna a resposta
     * @param string $prompt O prompt a ser enviado
     * @return string A resposta da IA
     * @throws Exception Em caso de erro na API
     */
    public function query($prompt) {
        // Sanitizar o prompt para evitar problemas com JSON
        $prompt = mb_convert_encoding($prompt, 'UTF-8', 'UTF-8');
        $prompt = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $prompt);
        
        $data = [
            'model' => $this->model,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => $this->temperature,
            'max_tokens' => $this->maxTokens
        ];
        
        // Tentar encode JSON e verificar erros
        $jsonData = json_encode($data, JSON_UNESCAPED_UNICODE);
        if ($jsonData === false) {
            error_log("Erro ao codificar JSON: " . json_last_error_msg());
            throw new Exception("Erro ao preparar dados para IA: " . json_last_error_msg());
        }
        
        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Bearer ' . $this->apiKey
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Timeout de 30 segundos
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // Timeout de conexão 10s
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Desabilitar verificação SSL (apenas desenvolvimento)
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Desabilitar verificação de host SSL
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($curlError) {
            throw new Exception("Erro de conexão: " . $curlError);
        }
        
        if ($httpCode !== 200) {
            $errorData = json_decode($response, true);
            $errorMsg = $errorData['error']['message'] ?? $response;
            
            // Log detalhado do erro
            error_log("OpenAI API Error (HTTP $httpCode): " . $errorMsg);
            error_log("Request data: " . substr($jsonData, 0, 500)); // Log primeiros 500 chars
            
            throw new Exception("OpenAI API Error (HTTP $httpCode): " . $errorMsg);
        }
        
        $result = json_decode($response, true);
        
        if (!isset($result['choices'][0]['message']['content'])) {
            throw new Exception("Resposta inválida da OpenAI: " . $response);
        }
        
        return $result['choices'][0]['message']['content'];
    }
    
    /**
     * Retorna informações sobre o uso de tokens da última requisição
     * @param string $response Resposta completa da API
     * @return array Informações de uso
     */
    public function getUsageInfo($response) {
        $data = json_decode($response, true);
        return $data['usage'] ?? [];
    }
}
