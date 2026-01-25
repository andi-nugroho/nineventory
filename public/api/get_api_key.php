<?php

require_once __DIR__ . '/../../config/app.php';

header('Content-Type: application/json');

try {
    $apiKey = $_ENV['GEMINI_API_KEY'] ?? null;

    if (empty($apiKey)) {
        echo json_encode([
            'success' => false,
            'message' => 'API key not configured'
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'api_key' => $apiKey
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to load API key'
    ]);
}
