<?php

// Função simples para carregar .env
function loadEnv($path) {
    if (!file_exists($path)) {
        return;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Carregar .env se existir
loadEnv(__DIR__ . '/../.env');

// Detectar ambiente (Produção se não houver .env ou URL for prod)
// OBS: Em produção, você deve criar o arquivo .env com as credenciais reais OU configurar as variáveis no servidor.
// Se não houver .env, tenta usar os valores padrão abaixo (fallback) ou variáveis de ambiente do sistema.

$config = [
    'app' => [
        'name' => getenv('APP_NAME') ?: 'SGP - Sistema de Gestão Pedagógica',
        'url' => getenv('APP_URL') ?: 'https://sgprorainopolis.com',
        'timezone' => getenv('APP_TIMEZONE') ?: 'America/Sao_Paulo',
    ],
    'db' => [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'dbname' => getenv('DB_NAME') ?: 'sgp_db',
        'username' => getenv('DB_USER') ?: 'root',
        'password' => getenv('DB_PASS') ?: '',
        'charset' => 'utf8mb4'
    ]
];

return $config;
