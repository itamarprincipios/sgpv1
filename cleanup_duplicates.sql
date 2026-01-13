-- ============================================
-- LIMPEZA DE DOCUMENTOS DUPLICADOS
-- Deleta documentos mais antigos, mantendo apenas o mais recente por período
-- ============================================

-- PASSO 1: VISUALIZAR duplicatas antes de deletar
-- Execute este primeiro para conferir o que será deletado
SELECT 
    d1.id,
    d1.user_id,
    u.name as professor_name,
    d1.period_id,
    p.name as period_name,
    d1.file_path,
    d1.submitted_at,
    d1.status,
    'SERÁ DELETADO' as acao
FROM documents d1
JOIN users u ON d1.user_id = u.id
JOIN periods p ON d1.period_id = p.id
WHERE EXISTS (
    SELECT 1 
    FROM documents d2 
    WHERE d2.user_id = d1.user_id 
    AND d2.period_id = d1.period_id 
    AND d2.submitted_at > d1.submitted_at
)
ORDER BY u.name, p.name, d1.submitted_at;

-- ============================================
-- PASSO 2: DELETAR duplicatas (CUIDADO!)
-- Execute apenas APÓS conferir o resultado acima
-- ============================================

-- Deletar documentos duplicados, mantendo apenas o mais recente
DELETE d1 FROM documents d1
WHERE EXISTS (
    SELECT 1 
    FROM documents d2 
    WHERE d2.user_id = d1.user_id 
    AND d2.period_id = d1.period_id 
    AND d2.submitted_at > d1.submitted_at
);

-- ============================================
-- PASSO 3: VERIFICAÇÃO (após deletar)
-- ============================================

-- Verificar se ainda há duplicatas
SELECT 
    user_id,
    period_id,
    COUNT(*) as qtd_documentos
FROM documents
GROUP BY user_id, period_id
HAVING COUNT(*) > 1;

-- Se retornar vazio = SUCESSO! Não há mais duplicatas
