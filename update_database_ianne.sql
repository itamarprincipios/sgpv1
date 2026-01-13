-- ============================================
-- ATUALIZAÇÃO IANNE - VERSÃO SIMPLES
-- Banco: u199671261_dbsgp
-- ============================================

-- 1. CRIAR TABELA ai_queries
CREATE TABLE IF NOT EXISTS `ai_queries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `question` text NOT NULL,
  `context_filters` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`context_filters`)),
  `response` text DEFAULT NULL,
  `response_time_ms` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_created` (`user_id`,`created_at`),
  CONSTRAINT `ai_queries_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- 2. ADICIONAR COLUNAS EM documents
-- Importante: Se a coluna JÁ EXISTIR, vai dar erro - isso é NORMAL!
-- Basta ignorar o erro e continuar

-- Adicionar content_text
ALTER TABLE `documents` 
ADD COLUMN `content_text` longtext DEFAULT NULL COMMENT 'Conteúdo extraído para análise IA' AFTER `file_path`;

-- Adicionar content_extracted_at
ALTER TABLE `documents` 
ADD COLUMN `content_extracted_at` timestamp NULL DEFAULT NULL COMMENT 'Data da extração' AFTER `content_text`;
