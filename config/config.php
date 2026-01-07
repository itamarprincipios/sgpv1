<?php

// Detectar ambiente automaticamente
$isProduction = (
    isset($_SERVER['HTTP_HOST']) && 
    strpos($_SERVER['HTTP_HOST'], 'sgprorainopolis.com') !== false
);

// Configurações de produção
$productionConfig = [
    'app' => [
        'name' => 'SGP - Sistema de Gestão Pedagógica',
        'url' => 'https://sgprorainopolis.com',
        'timezone' => 'America/Sao_Paulo',
    ],
    'db' => [
        'host' => 'localhost',
        'dbname' => 'u199671261_dbsgp',
        'username' => 'u199671261_dbsgpuser',
        'password' => 'SgpAdmin2025',
        'charset' => 'utf8mb4'
    ]
];

// Configurações de desenvolvimento local
$localConfig = [
    'app' => [
        'name' => 'SGP - Sistema de Gestão Pedagógica [LOCAL]',
        'url' => 'http://localhost:8000',
        'timezone' => 'America/Sao_Paulo',
    ],
    'db' => [
        'host' => 'localhost',
        'dbname' => 'sgp_system',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4'
    ]
];

// Retornar configuração apropriada
return $isProduction ? $productionConfig : $localConfig;
