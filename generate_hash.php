<?php
/**
 * Gerador de hash de senha para Super Admin
 * Execute este arquivo e copie o hash gerado
 */

// Nova senha que você quer definir
$newPassword = 'admin123';

// Gerar hash bcrypt da nova senha
$hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

echo "\n=== GERADOR DE HASH DE SENHA ===\n\n";
echo "Senha escolhida: {$newPassword}\n";
echo "Hash gerado: {$hashedPassword}\n\n";
echo "=== INSTRUÇÕES ===\n";
echo "1. Copie o hash acima\n";
echo "2. Acesse o phpMyAdmin ou seu gerenciador de banco de dados\n";
echo "3. Execute este SQL:\n\n";
echo "UPDATE users SET password = '{$hashedPassword}' WHERE email = 'admin@sgp.com';\n\n";
echo "4. Faça login com:\n";
echo "   Email: admin@sgp.com\n";
echo "   Senha: {$newPassword}\n\n";
echo "⚠️  Delete este arquivo após usar!\n\n";
