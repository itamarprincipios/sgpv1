<?php
// Simular o que acontece no public/index.php
function test_route($request_uri) {
    $_SERVER['REQUEST_URI'] = $request_uri;
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri_prepared = trim(str_replace('/public', '', $uri), '/');
    
    echo "URL: $request_uri\n";
    echo "Path extraído: $uri\n";
    echo "URI preparada: $uri_prepared\n";
    
    $routes = [
        'school/class/delete' => 'Match!',
        'school/planning/delete' => 'Match!',
        'school/professor/delete' => 'Match!'
    ];
    
    if (array_key_exists($uri_prepared, $routes)) {
        echo "Resultado: " . $routes[$uri_prepared] . "\n";
    } else {
        echo "Resultado: 404 - Não encontrado\n";
    }
    echo "---------------------------\n";
}

test_route('/school/class/delete?id=1');
test_route('/school/planning/delete?id=5');
test_route('/school/professor/delete?id=10');
test_route('/public/school/class/delete?id=1');
