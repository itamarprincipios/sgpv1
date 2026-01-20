<?php
/**
 * TESTE DE ROTAS E AUTENTICAÇÃO - ADMIN
 * Acesse: https://sgprorainopolis.com/test_admin.php
 */

session_start();

echo "<h1>🔍 Diagnóstico de Autenticação - ADMIN</h1>";
echo "<style>
    body { font-family: Arial; padding: 20px; background: #f5f5f5; }
    .section { background: white; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #007bff; }
    .success { border-left-color: #28a745; }
    .error { border-left-color: #dc3545; }
    .warning { border-left-color: #ffc107; }
    pre { background: #f8f9fa; padding: 10px; border-radius: 3px; overflow-x: auto; }
    h2 { margin-top: 0; color: #333; }
</style>";

// 1. Verificar Sessão
echo "<div class='section'>";
echo "<h2>1️⃣ Sessão Atual</h2>";
if (isset($_SESSION['user'])) {
    echo "<div class='success'><strong>✅ Sessão ativa!</strong></div>";
    echo "<pre>";
    print_r($_SESSION['user']);
    echo "</pre>";
    
    $role = $_SESSION['user']['role'] ?? 'não definido';
    echo "<p><strong>Role detectada:</strong> <code>$role</code></p>";
    echo "<p><strong>Role em minúsculo:</strong> <code>" . strtolower(trim($role)) . "</code></p>";
} else {
    echo "<div class='error'><strong>❌ Nenhuma sessão ativa</strong></div>";
    echo "<p>Faça login primeiro em: <a href='/login'>https://sgprorainopolis.com/login</a></p>";
}
echo "</div>";

// 2. Verificar Banco de Dados
echo "<div class='section'>";
echo "<h2>2️⃣ Verificação do Banco de Dados</h2>";

// Handle Auto-Fix Request
if (isset($_POST['fix_admin_role'])) {
    try {
        require_once __DIR__ . '/../app/Core/Database.php';
        $db = Database::getInstance()->getConnection();
        
        // 1. Check Schema First
        $desc = $db->query("DESCRIBE users role")->fetch(PDO::FETCH_ASSOC);
        echo "<div class='warning'><strong>🔍 Schema da coluna ROLE:</strong> <pre>" . print_r($desc, true) . "</pre></div>";

        $stmt = $db->prepare("UPDATE users SET role = 'admin' WHERE email = 'admin@sgp.com'");
        if ($stmt->execute()) {
             echo "<div class='success'><strong>✅ UPDATE executado!</strong></div>";
             
             // Check for warnings (like truncated data for ENUM)
             $warnings = $db->query("SHOW WARNINGS")->fetchAll(PDO::FETCH_ASSOC);
             if ($warnings) {
                 echo "<div class='error'><strong>⚠️ Avisos do MySQL:</strong> <pre>" . print_r($warnings, true) . "</pre></div>";
                 echo "<p>Isso geralmente significa que 'admin' não é um valor válido para esta coluna (ENUM).</p>";
                 
                 // Fallback: try 'Administrador' if 'admin' failed
                 echo "<p>Tentando reverter para 'Administrador'...</p>";
                 $db->exec("UPDATE users SET role = 'Administrador' WHERE email = 'admin@sgp.com'");
             } else {
                 echo "<div class='success'>Nenhum aviso do MySQL. Atualize a página.</div>";
             }
        } else {
             echo "<div class='error'>❌ Falha ao atualizar role.</div>";
        }
    } catch (Exception $e) {
        echo "<div class='error'>Error: " . $e->getMessage() . "</div>";
    }
}

try {
    // FIX PROD PATH: Use ../app because we are in public/
    if (file_exists(__DIR__ . '/../app/Core/Database.php')) {
        require_once __DIR__ . '/../app/Core/Database.php';
    } else {
         throw new Exception("Arquivo de banco de dados não encontrado em ../app/Core/Database.php");
    }

    $db = Database::getInstance()->getConnection();
    
    echo "<div class='section'>";
    echo "<h2>SCHEMA DATABASE</h2>";
    $stm = $db->query("DESCRIBE users");
    $schema = $stm->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre style='max-height: 200px; overflow:auto;'>" . print_r($schema, true) . "</pre>";
    echo "</div>";

    echo "<div class='section'>";
    echo "<h2>2️⃣ Verificação do Banco de Dados</h2>";
    echo "<div class='success'><strong>✅ Conexão com banco OK</strong></div>";
    
    // Buscar usuário admin
    $stmt = $db->prepare("SELECT id, name, email, role FROM users WHERE email = 'admin@sgp.com'");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo "<div class='success'><strong>✅ Usuário admin encontrado</strong></div>";
        echo "<pre>";
        print_r($admin);
        echo "</pre>";
        
        $roleValue = $admin['role'];
        if ($roleValue === null) $roleValue = '(NULL)';
        if ($roleValue === '') $roleValue = '(Vazio)';

        echo "<p><strong>Role no banco:</strong> <code>" . htmlspecialchars($roleValue) . "</code></p>";
        
        // Verificar se é "admin"
        if ($admin['role'] === 'admin') {
            echo "<div class='success'>✅ Role está correta: 'admin'</div>";
        } else {
            echo "<div class='error'>❌ Role incorreta ou ausente! Encontrado: '$roleValue'</div>";
            echo "<form method='post' style='margin-top:10px;'>
                    <input type='hidden' name='fix_admin_role' value='1'>
                    <button type='submit' style='background:#dc3545; color:white; border:none; padding:10px 20px; border-radius:5px; cursor:pointer; font-size:16px;'>
                        🚨 DEBUG & CORRIGIR ROLE (Check ENUM)
                    </button>
                  </form>";
        }
    } else {
        echo "<div class='error'><strong>❌ Usuário admin@sgp.com não encontrado</strong></div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'><strong>❌ Erro crítico:</strong> " . $e->getMessage() . "</div>";
}
echo "</div>";

// 3. Teste de Redirecionamento
echo "<div class='section'>";
echo "<h2>3️⃣ Teste de Redirecionamento</h2>";
if (isset($_SESSION['user'])) {
    $role = strtolower(trim($_SESSION['user']['role']));
    
    echo "<p><strong>Role processada:</strong> <code>$role</code></p>";
    
    $redirects = [
        'admin' => '/admin/dashboard',
        'administrador' => '/admin/dashboard',
        'semed' => '/semed/dashboard',
        'coordinator' => '/school/dashboard',
        'director' => '/school/dashboard',
        'professor' => '/professor/dashboard',
        'supervisor_edfis' => '/supervisor-edfis/dashboard',
        'supervisor_monitor' => '/supervisor-monitor/dashboard'
    ];
    
    if (isset($redirects[$role])) {
        echo "<div class='success'>✅ Redirecionamento esperado: <strong>" . $redirects[$role] . "</strong></div>";
        echo "<p><a href='" . $redirects[$role] . "' class='btn'>Ir para o dashboard</a></p>";
    } else {
        echo "<div class='error'>❌ Role '$role' não tem redirecionamento definido!</div>";
        echo "<p>Roles válidas:</p><ul>";
        foreach (array_keys($redirects) as $validRole) {
            echo "<li><code>$validRole</code></li>";
        }
        echo "</ul>";
    }
} else {
    echo "<div class='warning'>⚠️ Faça login primeiro para testar redirecionamento</div>";
}
echo "</div>";

// 4. Verificar Rotas
echo "<div class='section'>";
echo "<h2>4️⃣ Rotas Disponíveis</h2>";
try {
    require_once __DIR__ . '/public/index.php';
    echo "<div class='success'>✅ Arquivo de rotas carregado</div>";
} catch (Exception $e) {
    echo "<div class='error'>❌ Erro ao carregar rotas: " . $e->getMessage() . "</div>";
}
echo "</div>";

// 5. Ações Rápidas
echo "<div class='section'>";
echo "<h2>5️⃣ Ações Rápidas</h2>";
echo "<p><a href='/login'>🔐 Ir para Login</a></p>";
echo "<p><a href='/admin/dashboard'>📊 Tentar acessar Admin Dashboard</a></p>";
if (isset($_SESSION['user'])) {
    echo "<p><a href='/logout'>🚪 Fazer Logout</a></p>";
}
echo "</div>";

echo "<hr>";
echo "<p style='text-align: center; color: #666;'>Criado para diagnóstico - " . date('Y-m-d H:i:s') . "</p>";
?>
