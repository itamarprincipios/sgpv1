<?php
require_once __DIR__ . '/app/Core/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "<h1>Recuperação de Banco de Dados</h1>";
    
    // 1. Criar tabela user_schools
    $sql = "CREATE TABLE IF NOT EXISTS user_schools (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        school_id INT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_user_school (user_id, school_id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $db->exec($sql);
    echo "<p>✅ Tabela <strong>user_schools</strong> verificada/criada com sucesso.</p>";
    
    // 2. Migrar dados existentes (users.school_id -> user_schools)
    // Para usuários que têm school_id na tabela users, mas não têm entrada na pivot
    echo "<h2>Migrando relacionamentos existentes...</h2>";
    
    $users = $db->query("SELECT id, school_id FROM users WHERE school_id IS NOT NULL")->fetchAll(PDO::FETCH_ASSOC);
    
    $count = 0;
    foreach ($users as $user) {
        $stmt = $db->prepare("INSERT IGNORE INTO user_schools (user_id, school_id) VALUES (:uid, :sid)");
        $stmt->execute(['uid' => $user['id'], 'sid' => $user['school_id']]);
        if ($stmt->rowCount() > 0) {
            $count++;
        }
    }
    
    echo "<p>✅ Migração concluída: <strong>$count</strong> novos vínculos criados.</p>";
    
    // 3. Garantir que o ADMIN SEMED tenha acesso a todas as escolas (Opcional, mas útil)
    // Descomente se quiser vincular Admin a todas
    /*
    $semedUser = $db->query("SELECT id FROM users WHERE role = 'semed' LIMIT 1")->fetch();
    if ($semedUser) {
        $schools = $db->query("SELECT id FROM schools")->fetchAll();
        foreach ($schools as $school) {
             $db->query("INSERT IGNORE INTO user_schools (user_id, school_id) VALUES ({$semedUser['id']}, {$school['id']})");
        }
        echo "<p>✅ Admin SEMED vinculado a todas as escolas.</p>";
    }
    */

} catch (PDOException $e) {
    echo "<div style='color:red; border:1px solid red; padding:10px;'>";
    echo "<h3>❌ Erro Fatal:</h3>";
    echo $e->getMessage();
    echo "</div>";
}
