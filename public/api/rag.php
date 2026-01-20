<?php
/**
 * API Endpoint para RAG
 * POST /api/rag.php
 */

session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/Core/Database.php';
require_once __DIR__ . '/../../app/Helpers/functions.php'; // Load auth helpers
require_once __DIR__ . '/../../app/Controllers/RAGController.php';

// Verificar método HTTP
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new RAGController();
    $controller->query();
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new RAGController();
    $controller->getHistory();
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
}
