<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "<h1>Restaurando Super Admin</h1>";

    // 1. Atualizar a coluna ROLE para aceitar 'admin'
    echo "<p>🔄 Atualizando tabela 'users' para permitir role 'admin'...</p>";
    $sqlAlter = "ALTER TABLE users MODIFY COLUMN role ENUM('semed', 'coordinator', 'professor', 'admin') NOT NULL";
    $db->exec($sqlAlter);
    echo "<p>✅ Tabela atualizada com sucesso.</p>";

    // 2. Criar ou Atualizar o Usuário Admin
    $email = 'admin@sgp.com';
    $password = 'SgpMaster2025!';
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $name = 'Super Administrador';

    // Verificar se já existe
    $stmt = $db->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
        // Atualizar
        $stmt = $db->prepare("UPDATE users SET password = :pass, role = 'admin', name = :name WHERE id = :id");
        $stmt->execute(['pass' => $hash, 'name' => $name, 'id' => $user['id']]);
        echo "<p>✅ Usuário Admin <strong>atualizado</strong> com sucesso.</p>";
    } else {
        // Criar
        $stmt = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (:name, :email, :pass, 'admin')");
        $stmt->execute(['name' => $name, 'email' => $email, 'pass' => $hash]);
        echo "<p>✅ Usuário Admin <strong>criado</strong> com sucesso.</p>";
    }

    echo "<div style='background:#d4edda; padding:15px; border:1px solid #c3e6cb; border-radius:5px;'>";
    echo "<h3>🚀 Dados de Acesso:</h3>";
    echo "<p><strong>URL:</strong> <a href='/login'>/login</a></p>";
    echo "<p><strong>Email:</strong> $email</p>";
    echo "<p><strong>Senha:</strong> $password</p>";
    echo "<p><em>⚠️ Por segurança, apague este arquivo após o uso!</em></p>";
    echo "</div>";

} catch (PDOException $e) {
    echo "<div style='color:red; border:1px solid red; padding:10px;'>";
    echo "<h3>❌ Erro Fatal:</h3>";
    echo $e->getMessage();
    echo "</div>";
}
