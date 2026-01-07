<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "<h1>Atualizando Tabela Users - Foto de Perfil</h1>";
    
    // Check if column exists
    try {
        $db->query("SELECT profile_photo FROM users LIMIT 1");
        echo "<p>ℹ️ Coluna <strong>profile_photo</strong> já existe.</p>";
    } catch (PDOException $e) {
        // Create column
        $sql = "ALTER TABLE users ADD COLUMN profile_photo VARCHAR(255) DEFAULT NULL";
        $db->exec($sql);
        echo "<p>✅ Coluna <strong>profile_photo</strong> criada com sucesso!</p>";
    }
    
    // Create uploads/avatars directory if not exists
    $avatarDir = __DIR__ . '/uploads/avatars';
    if (!file_exists($avatarDir)) {
        if (mkdir($avatarDir, 0777, true)) {
            echo "<p>✅ Diretório <strong>public/uploads/avatars</strong> criado.</p>";
        } else {
            echo "<p>❌ Falha ao criar diretório de uploads.</p>";
        }
    } else {
        echo "<p>ℹ️ Diretório de uploads já existe.</p>";
    }

    echo "<hr><h3>🎉 Atualização Concluída!</h3>";
    echo "<p>Agora você pode fazer upload de foto de perfil.</p>";
    echo "<p><em>Por favor, remova este arquivo após o uso.</em></p>";

} catch (PDOException $e) {
    echo "<div style='color:red; border:1px solid red; padding:10px;'>";
    echo "<h3>❌ Erro Fatal:</h3>";
    echo $e->getMessage();
    echo "</div>";
}
