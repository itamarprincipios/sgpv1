<?php
require_once __DIR__ . '/app/Core/Database.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    $email = 'superadmin@sgp.com';
    $password = 'i@nna2111';
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $name = 'Super Admin';
    $role = 'admin';
    
    // Check if exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    
    if ($stmt->rowCount() > 0) {
        $sql = "UPDATE users SET password = :password, role = :role WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['password' => $hash, 'role' => $role, 'email' => $email]);
        echo "Admin updated successfully with role '$role'!\n";
    } else {
        $sql = "INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['name' => $name, 'email' => $email, 'password' => $hash, 'role' => $role]);
        echo "Admin created successfully with role '$role'!\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
