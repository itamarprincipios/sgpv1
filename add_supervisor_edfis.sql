-- ============================================
-- SUPERVISORA SEMED - EDUCAÇÃO FÍSICA
-- Adicionar novo role ao sistema
-- ============================================

-- PASSO 1: Alterar ENUM da tabela users para incluir novo role
ALTER TABLE `users` 
MODIFY COLUMN `role` ENUM('semed','coordinator','professor','director','admin','supervisor_edfis') NOT NULL;

-- PASSO 2: Verificar se alteração foi bem-sucedida
DESCRIBE users;

-- ============================================
-- CRIAR PRIMEIRA SUPERVISORA (Exemplo)
-- Execute após alterar o ENUM
-- ============================================

-- Inserir supervisora de exemplo (AJUSTE OS DADOS!)
INSERT INTO users (name, email, password, role, whatsapp, created_at) 
VALUES (
    'Supervisora Ed. Física SEMED',
    'supervisor.edfis@sgp.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- Senha: password
    'supervisor_edfis',
    '95991234567',
    NOW()
);

-- ============================================
-- VERIFICAÇÃO
-- ============================================

-- Ver supervisoras cadastradas
SELECT id, name, email, role, whatsapp 
FROM users 
WHERE role = 'supervisor_edfis';

-- Ver professores de Ed. Física (que ela vai supervisionar)
SELECT id, name, email, school_id, is_physical_education 
FROM users 
WHERE role = 'professor' 
AND is_physical_education = 1;
