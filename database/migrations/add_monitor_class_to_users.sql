-- Migration: Adicionar suporte a perfil duplo (Titular e Monitor M.A.E)
-- Execute este SQL no phpMyAdmin da Hostinger

ALTER TABLE users ADD COLUMN monitor_class_id INT(11) DEFAULT NULL AFTER class_id;

ALTER TABLE users ADD CONSTRAINT fk_monitor_class 
FOREIGN KEY (monitor_class_id) REFERENCES classes(id) ON DELETE SET NULL;

-- Log de confirmação (opcional)
-- SELECT 'Coluna monitor_class_id adicionada com sucesso' as status;
