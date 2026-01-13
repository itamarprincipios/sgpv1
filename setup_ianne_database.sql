-- ============================================
-- SCRIPT DE CONFIGURAÇÃO DO BANCO DE DADOS
-- Sistema IANNE (Assistente IA)
-- ============================================

-- 1. Criar tabela para histórico de consultas à IA
CREATE TABLE IF NOT EXISTS ai_queries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    question TEXT NOT NULL,
    context_filters JSON,
    response TEXT,
    response_time_ms INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_created (user_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Adicionar colunas na tabela documents (se não existirem)
-- Verificar se as colunas já existem antes de executar

-- Para verificar manualmente, execute:
-- SHOW COLUMNS FROM documents LIKE 'content_text';
-- SHOW COLUMNS FROM documents LIKE 'content_extracted_at';

-- Se as colunas NÃO existirem, execute:
ALTER TABLE documents 
ADD COLUMN IF NOT EXISTS content_text LONGTEXT NULL COMMENT 'Conteúdo extraído do documento para análise da IA' AFTER file_path,
ADD COLUMN IF NOT EXISTS content_extracted_at TIMESTAMP NULL COMMENT 'Data/hora da extração do conteúdo' AFTER content_text;

-- ============================================
-- VERIFICAÇÃO
-- ============================================

-- Verificar se a tabela ai_queries foi criada
SELECT 'Tabela ai_queries criada!' AS status
FROM information_schema.tables 
WHERE table_schema = DATABASE() 
AND table_name = 'ai_queries';

-- Verificar se as colunas foram adicionadas
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    COLUMN_COMMENT
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'documents'
AND COLUMN_NAME IN ('content_text', 'content_extracted_at');
