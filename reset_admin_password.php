<?php
/**
 * Script temporário para resetar a senha do Super Admin
 * IMPORTANTE: Delete este arquivo após usar!
 */

require_once __DIR__ . '/app/Core/Database.php';

// Nova senha que você quer definir
$newPassword = 'admin123';

// Gerar hash bcrypt da nova senha
$hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

echo "=== RESET DE SENHA DO SUPER ADMIN ===\n\n";
echo "Nova senha: {$newPassword}\n";
echo "Hash gerado: {$hashedPassword}\n\n";

// Conectar ao banco de dados
try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Atualizar a senha do admin
    $sql = "UPDATE users SET password = :password WHERE email = 'admin@sgp.com'";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['password' => $hashedPassword]);
    
    if ($stmt->rowCount() > 0) {
        echo "✅ Senha do Super Admin atualizada com sucesso!\n\n";
        echo "Credenciais de acesso:\n";
        echo "Email: admin@sgp.com\n";
        echo "Senha: {$newPassword}\n\n";
        echo "⚠️  IMPORTANTE: Delete este arquivo (reset_admin_password.php) após usar!\n";
    } else {
        echo "❌ Nenhum usuário encontrado com email 'admin@sgp.com'\n";
        echo "Verifique se o usuário existe no banco de dados.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro ao conectar ao banco de dados:\n";
    echo $e->getMessage() . "\n";
}
