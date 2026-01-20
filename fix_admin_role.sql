-- ============================================
-- FIX ADMIN ROLE IN DATABASE
-- ============================================
-- Este script corrige a role "Administrador" para "admin"
-- Execute no phpMyAdmin ou MySQL Workbench

-- 1. Verificar usuários com role "Administrador"
SELECT id, name, email, role 
FROM users 
WHERE role = 'Administrador';

-- 2. Atualizar para "admin" (padrão do sistema)
UPDATE users 
SET role = 'admin' 
WHERE role = 'Administrador';

-- 3. Verificar se a mudança foi aplicada
SELECT id, name, email, role 
FROM users 
WHERE email = 'admin@sgp.com';

-- ============================================
-- RESULTADO ESPERADO:
-- role deve estar como "admin" (minúsculo)
-- ============================================
