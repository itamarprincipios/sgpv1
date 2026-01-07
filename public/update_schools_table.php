<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "<h1>Atualizando Tabela Schools</h1>";
    
    // Verificar quais colunas faltam
    $columns = [
        'address' => "ALTER TABLE schools ADD COLUMN address TEXT DEFAULT NULL",
        'director_name' => "ALTER TABLE schools ADD COLUMN director_name VARCHAR(255) DEFAULT NULL",
        'director_phone' => "ALTER TABLE schools ADD COLUMN director_phone VARCHAR(20) DEFAULT NULL"
    ];
    
    foreach ($columns as $col => $sql) {
        try {
            // Tenta selecionar a coluna para ver se existe
            $db->query("SELECT $col FROM schools LIMIT 1");
            echo "<p>ℹ️ Coluna <strong>$col</strong> já existe.</p>";
        } catch (PDOException $e) {
            // Se der erro, é porque não existe, então cria
            echo "<p>➕ Criando coluna <strong>$col</strong>...</p>";
            $db->exec($sql);
            echo "<p>✅ Coluna <strong>$col</strong> criada com sucesso.</p>";
        }
    }
    
    echo "<hr><h3>🎉 Atualização Concluída!</h3>";
    echo "<p>Agora você pode cadastrar escolas sem erro 500.</p>";
    echo "<p><em>Por favor, remova este arquivo após o uso.</em></p>";

} catch (PDOException $e) {
    echo "<div style='color:red; border:1px solid red; padding:10px;'>";
    echo "<h3>❌ Erro Fatal:</h3>";
    echo $e->getMessage();
    echo "</div>";
}
