<?php

// Função simples para carregar .env
// DEBUG: Habilitar erros temporariamente
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// die('O CONFIG CARREGOU!');

if (!function_exists('loadEnv')) {
    function loadEnv($path) {
        if (!file_exists($path)) {
            // die("DEBUG: Arquivo .env não encontrado em: " . $path); // Descomente para testar caminho
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
}

// Carregar .env se existir
loadEnv(__DIR__ . '/../.env');

// Detectar ambiente (Produção se não houver .env ou URL for prod)
// OBS: Em produção, você deve criar o arquivo .env com as credenciais reais OU configurar as variáveis no servidor.
// Se não houver .env, tenta usar os valores padrão abaixo (fallback) ou variáveis de ambiente do sistema.

// Função helper para pegar variável de ambiente
if (!function_exists('env')) {
    function env($key, $default = null) {
        if (array_key_exists($key, $_ENV)) {
            return $_ENV[$key];
        }
        if (array_key_exists($key, $_SERVER)) {
            return $_SERVER[$key];
        }
        $val = getenv($key);
        return $val !== false ? $val : $default;
    }
}

$config = [
    'app' => [
        'name' => env('APP_NAME', 'SGP - Sistema de Gestão Pedagógica'),
        'url' => env('APP_URL', 'https://sgprorainopolis.com'),
        'timezone' => env('APP_TIMEZONE', 'America/Sao_Paulo'),
    ],
    'db' => [
        'host' => env('DB_HOST', 'localhost'),
        'dbname' => env('DB_NAME', 'sgp_db'),
        'username' => env('DB_USER', 'root'),
        'password' => env('DB_PASS', ''),
        'charset' => 'utf8mb4'
    ]
];

return $config;
