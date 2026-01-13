<?php
/**
 * DocumentExtractor - Extrai texto de documentos Word (.docx)
 * 
 * Esta classe usa PHP nativo (ZipArchive) para ler arquivos .docx
 * sem necessidade de bibliotecas externas.
 * 
 * Arquivos .docx são na verdade arquivos ZIP contendo XML.
 */

require_once __DIR__ . '/Database.php';

class DocumentExtractor {
    
    /**
     * Extrai texto de um arquivo .docx
     * @param string $filePath Caminho completo do arquivo
     * @return string Texto extraído
     * @throws Exception Se não conseguir ler o arquivo
     */
    public function extractText($filePath) {
        if (!file_exists($filePath)) {
            throw new Exception("Arquivo não encontrado: $filePath");
        }
        
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        if ($extension === 'docx') {
            return $this->extractFromDocx($filePath);
        } elseif ($extension === 'doc') {
            // Arquivo .doc antigo - não suportado diretamente
            // Retornar mensagem indicando que precisa ser .docx
            return "[Arquivo .doc antigo - Por favor, converta para .docx para extração de conteúdo]";
        } else {
            throw new Exception("Formato não suportado: $extension");
        }
    }
    
    /**
     * Extrai texto de arquivo .docx usando ZipArchive
     * @param string $filePath Caminho do arquivo .docx
     * @return string Texto extraído
     */
    private function extractFromDocx($filePath) {
        $zip = new ZipArchive();
        
        if ($zip->open($filePath) !== true) {
            throw new Exception("Não foi possível abrir o arquivo como ZIP");
        }
        
        // O conteúdo principal está em word/document.xml
        $content = $zip->getFromName('word/document.xml');
        $zip->close();
        
        if ($content === false) {
            throw new Exception("Não foi possível encontrar document.xml no arquivo");
        }
        
        // Extrair texto do XML
        $text = $this->extractTextFromXml($content);
        
        return $text;
    }
    
    /**
     * Extrai texto puro do XML do Word
     * @param string $xml Conteúdo XML
     * @return string Texto extraído
     */
    private function extractTextFromXml($xml) {
        // Remover namespaces para facilitar parsing
        $xml = str_replace(['w:', 'w:'], '', $xml);
        
        // Carregar XML
        $dom = new DOMDocument();
        @$dom->loadXML($xml);
        
        // Pegar todos os elementos <t> (text)
        $textNodes = $dom->getElementsByTagName('t');
        
        $text = '';
        foreach ($textNodes as $node) {
            $text .= $node->nodeValue . ' ';
        }
        
        // Limpar espaços extras
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);
        
        return $text;
    }
    
    /**
     * Salva conteúdo extraído no banco de dados
     * @param int $documentId ID do documento
     * @param string $text Texto extraído
     * @return bool Sucesso
     */
    public function saveExtractedContent($documentId, $text) {
        try {
            $db = Database::getInstance();
            
            $sql = "UPDATE documents 
                    SET content_text = :content_text,
                        content_extracted_at = NOW()
                    WHERE id = :document_id";
            
            $db->query($sql, [
                'content_text' => $text,
                'document_id' => $documentId
            ]);
            
            return true;
        } catch (Exception $e) {
            error_log("Erro ao salvar conteúdo extraído: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Extrai e salva conteúdo de um documento
     * @param int $documentId ID do documento no banco
     * @return array Resultado com sucesso e mensagem
     */
    public function extractAndSave($documentId) {
        try {
            $db = Database::getInstance();
            
            // Buscar informações do documento
            $sql = "SELECT id, file_path FROM documents WHERE id = :id";
            $stmt = $db->query($sql, ['id' => $documentId]);
            $document = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$document) {
                error_log("DocumentExtractor: Documento ID $documentId não encontrado");
                return [
                    'success' => false,
                    'message' => 'Documento não encontrado'
                ];
            }
            
            // Construir caminho completo
            // Verificar se o caminho já inclui 'uploads/'
            $filePath = $document['file_path'];
            if (strpos($filePath, 'uploads/') !== 0) {
                $filePath = 'uploads/' . $filePath;
            }
            $fullPath = __DIR__ . '/../../public/' . $filePath;
            
            // Verificar se arquivo existe
            if (!file_exists($fullPath)) {
                error_log("DocumentExtractor: Arquivo não encontrado: $fullPath");
                return [
                    'success' => false,
                    'message' => "Arquivo não encontrado: $filePath"
                ];
            }
            
            // Extrair texto
            $text = $this->extractText($fullPath);
            
            // Salvar no banco
            $saved = $this->saveExtractedContent($documentId, $text);
            
            if ($saved) {
                return [
                    'success' => true,
                    'message' => 'Conteúdo extraído com sucesso',
                    'text_length' => strlen($text),
                    'preview' => substr($text, 0, 200) . '...'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erro ao salvar conteúdo no banco'
                ];
            }
            
        } catch (Exception $e) {
            error_log("DocumentExtractor::extractAndSave error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return [
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Processa todos os documentos sem conteúdo extraído
     * @param int $limit Limite de documentos a processar
     * @return array Estatísticas do processamento
     */
    public function processAllDocuments($limit = 50) {
        try {
            $db = Database::getInstance();
            
            // Garantir que limit é inteiro
            $limit = (int)$limit;
            
            // Buscar documentos sem conteúdo extraído
            // LIMIT não pode ser usado com prepared statements em MariaDB
            $sql = "SELECT id, file_path, title 
                    FROM documents 
                    WHERE content_extracted_at IS NULL 
                    LIMIT $limit";
            
            $stmt = $db->query($sql);
            $documents = $stmt->fetchAll();
            
            $stats = [
                'total' => count($documents),
                'success' => 0,
                'failed' => 0,
                'errors' => []
            ];
            
            foreach ($documents as $doc) {
                $result = $this->extractAndSave($doc['id']);
                
                if ($result['success']) {
                    $stats['success']++;
                } else {
                    $stats['failed']++;
                    $stats['errors'][] = [
                        'id' => $doc['id'],
                        'title' => $doc['title'],
                        'error' => $result['message']
                    ];
                }
            }
            
            return $stats;
            
        } catch (Exception $e) {
            return [
                'total' => 0,
                'success' => 0,
                'failed' => 0,
                'errors' => [['error' => $e->getMessage()]]
            ];
        }
    }
}
