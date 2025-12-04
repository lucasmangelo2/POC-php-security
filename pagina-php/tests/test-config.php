<?php
/**
 * Script de teste de configuração
 * Execute: php tests/test-config.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Config;

echo "=== Teste de Configuração ===\n\n";

try {
    $config = Config::getInstance();
    
    // Testa se .env foi carregado
    echo "✓ Arquivo .env carregado com sucesso\n\n";
    
    // Verifica configurações OAuth
    echo "OAuth Configuration:\n";
    echo "  Client ID: " . ($config->get('oauth.client_id') ? '✓ Configurado' : '✗ Não configurado') . "\n";
    echo "  Client Secret: " . ($config->get('oauth.client_secret') ? '✓ Configurado' : '✗ Não configurado') . "\n";
    echo "  Token URL: " . ($config->get('oauth.token_url') ?: '✗ Não configurado') . "\n\n";
    
    // Verifica configurações da API
    echo "API Configuration:\n";
    echo "  Base URL: " . ($config->get('api.base_url') ?: '✗ Não configurado') . "\n\n";
    
    // Verifica configurações da aplicação
    echo "App Configuration:\n";
    echo "  Environment: " . $config->get('app.env') . "\n";
    echo "  Debug: " . ($config->get('app.debug') ? 'Enabled' : 'Disabled') . "\n\n";
    
    // Verifica extensões PHP necessárias
    echo "PHP Extensions:\n";
    $extensions = ['curl', 'json', 'mbstring'];
    foreach ($extensions as $ext) {
        echo "  $ext: " . (extension_loaded($ext) ? '✓ Instalado' : '✗ Não instalado') . "\n";
    }
    
    echo "\nVersão do PHP: " . phpversion() . "\n";
    
    // Avisos
    if (!$config->get('oauth.client_id') || $config->get('oauth.client_id') === 'your_client_id_here') {
        echo "\n⚠️  AVISO: Configure suas credenciais OAuth2 no arquivo .env\n";
    }
    
    if ($config->get('app.env') === 'production' && $config->get('app.debug')) {
        echo "\n⚠️  AVISO: Debug está habilitado em ambiente de produção!\n";
    }
    
} catch (Exception $e) {
    echo "✗ Erro: " . $e->getMessage() . "\n";
    echo "\nVerifique se:\n";
    echo "  1. O arquivo .env existe\n";
    echo "  2. As dependências foram instaladas (composer install)\n";
    echo "  3. O arquivo .env tem as permissões corretas\n";
    exit(1);
}

echo "\n=== Teste Concluído ===\n";
