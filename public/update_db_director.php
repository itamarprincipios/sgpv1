<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';

try {
    $db = Database::getInstance();
    
    // 1. Add 'address' column if it doesn't exist
    $check = $db->query("SHOW COLUMNS FROM users LIKE 'address'");
    if (!$check->fetch()) {
        $db->query("ALTER TABLE users ADD COLUMN address TEXT DEFAULT NULL AFTER whatsapp");
        echo "Coluna 'address' adicionada com sucesso.<br>";
    } else {
        echo "Coluna 'address' já existe.<br>";
    }

    // 2. Modify 'role' enum to include 'director'
    // Note: modifying enum can be tricky if data exists, but adding a value is usually safe in newer mysql.
    // We strictly define the new set.
    $db->query("ALTER TABLE users MODIFY COLUMN role ENUM('semed','coordinator','professor','director','admin') NOT NULL");
    echo "Enum 'role' atualizado para incluir 'director'.<br>";

    echo "Migração concluída com sucesso!";

} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
