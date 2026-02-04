-- Migration: Add First Grade Filter
-- Description: Adiciona colunas is_first_grade para filtrar planejamentos específicos do 1º ano
-- Date: 2026-02-04

-- Adicionar coluna is_first_grade na tabela periods
ALTER TABLE periods ADD COLUMN is_first_grade TINYINT(1) DEFAULT 0 AFTER is_monitor;

-- Adicionar coluna is_first_grade na tabela users
ALTER TABLE users ADD COLUMN is_first_grade TINYINT(1) DEFAULT 0 AFTER is_monitor;

-- Criar índices para melhorar performance das queries
CREATE INDEX idx_periods_first_grade ON periods(is_first_grade);
CREATE INDEX idx_users_first_grade ON users(is_first_grade);

-- Verificação
SELECT 'Migration completed successfully!' AS status;
