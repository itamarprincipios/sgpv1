# 🧪 Checklist de Testes - Sistema IANNE

## 1️⃣ Verificação da Estrutura do Banco (SQL)

Execute este SQL no phpMyAdmin para confirmar:

```sql
-- Verificar tabela ai_queries
SHOW TABLES LIKE 'ai_queries';

-- Verificar colunas em documents
SHOW COLUMNS FROM documents WHERE Field IN ('content_text', 'content_extracted_at');

-- Ver estrutura completa da ai_queries
DESCRIBE ai_queries;
```

**Resultado esperado:**
- ✅ Tabela `ai_queries` existe
- ✅ Colunas `content_text` e `content_extracted_at` existem em `documents`

---

## 2️⃣ Teste de Upload (Extração Automática)

### Passos:
1. **Iniciar servidor** (se não estiver rodando):
   ```bash
   php -S localhost:8000 -t public
   ```

2. **Login como PROFESSOR**:
   - Email: `itamar@sgp.com`
   - Senha: `professor123` (ou a senha que estiver configurada)

3. **Fazer upload de um planejamento**:
   - Selecionar um período ativo
   - Fazer upload de um arquivo PDF ou DOCX
   - Aguardar conclusão

4. **Verificar no banco se foi extraído**:
   ```sql
   SELECT 
       id,
       title,
       SUBSTRING(content_text, 1, 100) as preview_conteudo,
       content_extracted_at 
   FROM documents 
   ORDER BY id DESC 
   LIMIT 3;
   ```

**Resultado esperado:**
- ✅ `content_text` preenchido com texto extraído
- ✅ `content_extracted_at` com timestamp da extração
- ⚠️ Se vier NULL, verificar logs de erro do PHP

---

## 3️⃣ Teste do Widget IANNE (Avatar Flutuante)

### Passos:
1. **Login como COORDENADOR**:
   - Email: `milza@sgp.com` ou `rosi@sgp.com`
   - Senha: `coordinator123` (ou a configurada)

2. **Acessar dashboard da escola**

3. **Verificar avatar IANNE**:
   - [ ] Avatar aparece no canto inferior direito?
   - [ ] Ao clicar, abre o modal de chat?
   - [ ] O modal tem campo de mensagem?

4. **Navegar para detalhes de planejamento**:
   - Clicar em "Controle de Envios" em algum planejamento
   - [ ] Avatar IANNE persiste na página?

**Se não aparecer:**
- Abrir Console do navegador (F12) → verificar erros JavaScript
- Verificar se arquivo existe: `app/Views/partials/coordinator_ai_widget.php`
- Verificar role do usuário no banco (deve ser 'coordinator')

---

## 4️⃣ Teste do Chat com IANNE

### Passos:
1. **Estando logado como coordenador**
2. **Clicar no avatar IANNE**
3. **Fazer uma pergunta**, por exemplo:
   - "Quantos professores enviaram planejamentos?"
   - "Liste os planejamentos pendentes"
   - "Quem está atrasado?"

4. **Aguardar resposta** (pode demorar 2-5 segundos)

5. **Verificar no banco se foi registrado**:
   ```sql
   SELECT 
       id,
       question,
       SUBSTRING(response, 1, 100) as resposta_preview,
       response_time_ms,
       created_at
   FROM ai_queries 
   ORDER BY id DESC 
   LIMIT 5;
   ```

**Resultado esperado:**
- ✅ IANNE responde com informações do banco
- ✅ Registro aparece na tabela `ai_queries`
- ✅ `response_time_ms` mostra tempo de processamento

**Se der erro:**
- Verificar console JavaScript (F12)
- Verificar logs do PHP
- Testar API Key: verificar se `.env` está correto
- Verificar créditos na conta OpenAI

---

## 5️⃣ Validação Final

### Checklist Completo:
- [ ] Banco de dados estruturado corretamente
- [ ] Upload de documento extrai conteúdo automaticamente
- [ ] Widget IANNE aparece para coordenadores
- [ ] Widget persiste em todas as páginas do coordenador
- [ ] Chat IANNE responde perguntas
- [ ] Histórico de perguntas é salvo no banco
- [ ] Sem erros no console JavaScript
- [ ] Sem erros nos logs PHP

---

## 🔧 Troubleshooting Rápido

### Avatar não aparece
```php
// Verificar no código-fonte da página (Ctrl+U) se existe:
<!-- IANNE Coordinator Widget -->
```

### Extração não funciona
```bash
# Ver logs de erro:
tail -f error_log
# ou verificar pasta de logs do XAMPP
```

### Chat não responde
1. Testar API Key diretamente:
   ```php
   <?php
   require_once 'app/Core/AIService.php';
   $ai = new AIService();
   echo $ai->test(); // Criar método test() se não existir
   ?>
   ```

### Erro 500 ao fazer upload
- Ver logs PHP
- Verificar permissões da pasta `app/Core/`
- Verificar se `DocumentExtractor.php` existe

---

## 📊 Comandos SQL Úteis para Monitoramento

```sql
-- Ver últimas extrações
SELECT id, title, content_extracted_at 
FROM documents 
WHERE content_text IS NOT NULL
ORDER BY content_extracted_at DESC;

-- Ver perguntas mais frequentes
SELECT question, COUNT(*) as vezes
FROM ai_queries 
GROUP BY question
ORDER BY vezes DESC;

-- Performance média da IA
SELECT 
    AVG(response_time_ms) as tempo_medio_ms,
    MIN(response_time_ms) as mais_rapido,
    MAX(response_time_ms) as mais_lento
FROM ai_queries;
```
