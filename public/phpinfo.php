<?php
/**
 * PHP Configuration Info
 * IMPORTANTE: Remover este arquivo após verificar as configurações em produção
 * por questões de segurança!
 */

// Verificar se está em produção
$isProduction = (strpos($_SERVER['HTTP_HOST'], 'sgprorainopolis.com') !== false);

if ($isProduction) {
    // Em produção, mostrar apenas informações essenciais
    echo "<!DOCTYPE html>";
    echo "<html><head><title>PHP Info - SGP</title>";
    echo "<style>body{font-family:Arial;padding:20px;} .info{background:#f0f0f0;padding:10px;margin:10px 0;border-radius:5px;}</style>";
    echo "</head><body>";
    echo "<h1>🔧 Configurações PHP - SGP</h1>";
    echo "<p><strong>⚠️ ATENÇÃO:</strong> Remova este arquivo após verificação!</p>";
    
    echo "<div class='info'><strong>Versão PHP:</strong> " . phpversion() . "</div>";
    echo "<div class='info'><strong>Servidor:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</div>";
    echo "<div class='info'><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</div>";
    
    echo "<h2>Extensões Necessárias</h2>";
    $extensions = ['pdo', 'pdo_mysql', 'mbstring', 'fileinfo', 'json', 'session'];
    foreach ($extensions as $ext) {
        $loaded = extension_loaded($ext);
        $status = $loaded ? '✅' : '❌';
        echo "<div class='info'>$status <strong>$ext:</strong> " . ($loaded ? 'Instalado' : 'NÃO INSTALADO') . "</div>";
    }
    
    echo "<h2>Configurações Importantes</h2>";
    echo "<div class='info'><strong>upload_max_filesize:</strong> " . ini_get('upload_max_filesize') . "</div>";
    echo "<div class='info'><strong>post_max_size:</strong> " . ini_get('post_max_size') . "</div>";
    echo "<div class='info'><strong>max_execution_time:</strong> " . ini_get('max_execution_time') . "s</div>";
    echo "<div class='info'><strong>memory_limit:</strong> " . ini_get('memory_limit') . "</div>";
    
    echo "<h2>Teste de Conexão com Banco</h2>";
    try {
        require_once __DIR__ . '/../config/config.php';
        require_once __DIR__ . '/../app/Core/Database.php';
        
        $db = Database::getInstance()->getConnection();
        echo "<div class='info' style='background:#d4edda;color:#155724;'>✅ <strong>Conexão com banco de dados:</strong> SUCESSO</div>";
        
        // Testar se as tabelas existem
        $tables = ['users', 'schools', 'classes', 'periods', 'documents'];
        echo "<h3>Tabelas do Banco</h3>";
        foreach ($tables as $table) {
            $stmt = $db->query("SHOW TABLES LIKE '$table'");
            $exists = $stmt->rowCount() > 0;
            $status = $exists ? '✅' : '❌';
            echo "<div class='info'>$status Tabela <strong>$table</strong>: " . ($exists ? 'Existe' : 'NÃO EXISTE') . "</div>";
        }
        
    } catch (Exception $e) {
        echo "<div class='info' style='background:#f8d7da;color:#721c24;'>❌ <strong>Erro na conexão:</strong> " . htmlspecialchars($e->getMessage()) . "</div>";
    }
    
    echo "<h2>Permissões de Pastas</h2>";
    $folders = [
        __DIR__ . '/uploads' => 'uploads',
        __DIR__ . '/css' => 'css',
        __DIR__ . '/img' => 'img'
    ];
    
    foreach ($folders as $path => $name) {
        if (file_exists($path)) {
            $writable = is_writable($path);
            $status = $writable ? '✅' : '⚠️';
            $perms = substr(sprintf('%o', fileperms($path)), -4);
            echo "<div class='info'>$status <strong>$name/</strong> - Permissão: $perms - " . ($writable ? 'Gravável' : 'Somente Leitura') . "</div>";
        } else {
            echo "<div class='info'>❌ <strong>$name/</strong> - Pasta não existe</div>";
        }
    }
    
    echo "<hr><p style='color:#666;'><small>Acesse <a href='/'>← Voltar para o sistema</a></small></p>";
    echo "</body></html>";
    
} else {
    // Em desenvolvimento local, mostrar phpinfo completo
    phpinfo();
}
