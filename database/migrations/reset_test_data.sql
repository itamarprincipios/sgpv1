-- ============================================
-- SCRIPT DE LIMPEZA DE DADOS DE TESTE
-- ============================================
-- Este script remove professores, planejamentos, envios e turmas
-- MANTÉM: Coordenadores, Diretores, SEMED, Supervisores e Escolas
-- ============================================

-- ATENÇÃO: Execute este script com CUIDADO!
-- Recomenda-se fazer BACKUP antes de executar

-- 1. DELETAR DOCUMENTOS (envios de professores)
-- Remove todos os arquivos enviados por professores
DELETE FROM documents 
WHERE user_id IN (
    SELECT id FROM users WHERE role = 'professor'
);

-- 2. DELETAR MEDALHAS DE PROFESSORES
-- Remove todas as medalhas conquistadas por professores
DELETE FROM user_medals 
WHERE user_id IN (
    SELECT id FROM users WHERE role = 'professor'
);

-- 3. DELETAR PROFESSORES
-- Remove todos os usuários com role = 'professor'
DELETE FROM users 
WHERE role = 'professor';

-- 4. DELETAR PLANEJAMENTOS (PERIODS)
-- Remove todos os períodos/planejamentos cadastrados
DELETE FROM periods;

-- 5. DELETAR TURMAS (CLASSES)
-- Remove todas as turmas cadastradas
DELETE FROM classes;

-- ============================================
-- VERIFICAÇÃO PÓS-LIMPEZA
-- ============================================
-- Execute estas queries para verificar o resultado:

-- Contar usuários restantes por role
SELECT role, COUNT(*) as total 
FROM users 
GROUP BY role;

-- Verificar se ainda existem documentos
SELECT COUNT(*) as total_documentos FROM documents;

-- Verificar se ainda existem planejamentos
SELECT COUNT(*) as total_planejamentos FROM periods;

-- Verificar se ainda existem turmas
SELECT COUNT(*) as total_turmas FROM classes;

-- Verificar escolas (devem permanecer intactas)
SELECT COUNT(*) as total_escolas FROM schools;

-- ============================================
-- RESETAR AUTO_INCREMENT (OPCIONAL)
-- ============================================
-- Descomente as linhas abaixo se quiser resetar os IDs

-- ALTER TABLE documents AUTO_INCREMENT = 1;
-- ALTER TABLE periods AUTO_INCREMENT = 1;
-- ALTER TABLE classes AUTO_INCREMENT = 1;
-- ALTER TABLE user_medals AUTO_INCREMENT = 1;
