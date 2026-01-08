<?php
require_once __DIR__ . '/app/Core/Database.php';

$db = new Database();
$email = 'rosi@sgp.com';
$newPass = password_hash('123456', PASSWORD_DEFAULT);

$stmt = $db->query("UPDATE users SET password = :pass WHERE email = :email", [
    'pass' => $newPass,
    'email' => $email
]);

echo "Senha de $email resetada para 123456!";
