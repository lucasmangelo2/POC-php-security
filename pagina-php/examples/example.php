<?php
/**
 * Exemplo de uso da ApiService
 * 
 * Este arquivo demonstra como usar os serviços de forma programática
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Services\ApiService;
use App\Config\Config;

// Exemplo 1: Buscar dados básicos
$apiService = new ApiService();
$result = $apiService->fetchData('/users');

if ($result['success']) {
    echo "✓ Dados obtidos com sucesso!\n";
    echo json_encode($result['data'], JSON_PRETTY_PRINT);
} else {
    echo "✗ Erro: " . $result['error'] . "\n";
}

// Exemplo 2: Buscar dados de diferentes endpoints
$endpoints = ['/users', '/posts', '/comments'];

foreach ($endpoints as $endpoint) {
    echo "\n=== Buscando: $endpoint ===\n";
    $result = $apiService->fetchData($endpoint);
    
    if ($result['success']) {
        echo "Total de registros: " . count($result['data']) . "\n";
    } else {
        echo "Erro: " . $result['error'] . "\n";
    }
}
