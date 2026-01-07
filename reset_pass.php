<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/Core/Database.php';

$db = Database::getInstance();
$user = $db->query("SELECT * FROM users WHERE email = 'coord1@sgp.com'")->fetch();

if ($user) {
    echo "Usuário encontrado: " . $user['email'] . "\n";
    $testPassword = 'password';
    if (password_verify($testPassword, $user['password'])) {
        echo "Senha 'password' está CORRETA.\n";
    } else {
        echo "Senha 'password' está INCORRETA.\n";
        // Tentar resetar para 'password'
        $newHash = password_hash('password', PASSWORD_DEFAULT);
        $db->query("UPDATE users SET password = :pass WHERE id = :id", ['pass' => $newHash, 'id' => $user['id']]);
        echo "Senha resetada para 'password'.\n";
    }
} else {
    echo "Usuário não encontrado.\n";
}
