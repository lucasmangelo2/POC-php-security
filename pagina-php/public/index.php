<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Services\ApiService;
use App\Config\Config;

// Previne acesso direto a arquivos PHP
if (php_sapi_name() === 'cli') {
    die('Este script não pode ser executado via CLI');
}

// Headers de segurança
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Content-Type: text/html; charset=UTF-8');

// Inicializa configuração
$config = Config::getInstance();

// Tratamento de erros
if (!$config->get('app.debug')) {
    ini_set('display_errors', '0');
    error_reporting(0);
}

$apiService = new ApiService();
$result = $apiService->fetchData();

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Dados - OAuth2 Secure</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .header h1 {
            color: #333;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 14px;
        }

        .content {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .alert {
            padding: 15px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-error {
            background: #fee;
            border-left: 4px solid #c33;
            color: #c33;
        }

        .alert-success {
            background: #efe;
            border-left: 4px solid #3c3;
            color: #3c3;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .data-table th,
        .data-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .data-table th {
            background: #f5f5f5;
            font-weight: 600;
            color: #333;
        }

        .data-table tr:hover {
            background: #fafafa;
        }

        .data-card {
            background: #f9f9f9;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid #667eea;
        }

        .data-card h3 {
            color: #333;
            margin-bottom: 10px;
        }

        .data-card p {
            color: #666;
            line-height: 1.6;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .refresh-btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: background 0.3s;
        }

        .refresh-btn:hover {
            background: #5568d3;
        }

        .json-view {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
            border-radius: 5px;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.5;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 600;
            background: #667eea;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔒 Sistema com OAuth2 Seguro</h1>
            <p>Autenticação OAuth2 Client Credentials com credenciais protegidas no servidor</p>
        </div>

        <div class="content">
            <?php if (!$result['success']): ?>
                <div class="alert alert-error">
                    <strong>Erro:</strong> <?php echo htmlspecialchars($result['error']); ?>
                </div>
                <p>Verifique as configurações no arquivo <code>.env</code></p>
            <?php else: ?>
                <div class="alert alert-success">
                    ✓ Dados carregados com sucesso via OAuth2!
                </div>

                <div style="margin-bottom: 20px;">
                    <button class="refresh-btn" onclick="location.reload()">
                        🔄 Atualizar Dados
                    </button>
                </div>

                <?php if (is_array($result['data']) && !empty($result['data'])): ?>
                    <?php if (isset($result['data'][0]) && is_array($result['data'][0])): ?>
                        <!-- Tabela para array de objetos -->
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <?php foreach (array_keys($result['data'][0]) as $key): ?>
                                        <th><?php echo htmlspecialchars(ucfirst($key)); ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($result['data'] as $item): ?>
                                    <tr>
                                        <?php foreach ($item as $value): ?>
                                            <td><?php echo htmlspecialchars(is_scalar($value) ? $value : json_encode($value)); ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <!-- Cards para objeto único ou estrutura complexa -->
                        <?php foreach ($result['data'] as $key => $value): ?>
                            <div class="data-card">
                                <h3><span class="badge"><?php echo htmlspecialchars($key); ?></span></h3>
                                <p><?php echo htmlspecialchars(is_scalar($value) ? $value : json_encode($value, JSON_PRETTY_PRINT)); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <details style="margin-top: 30px;">
                        <summary style="cursor: pointer; font-weight: 600; margin-bottom: 10px;">Ver JSON completo</summary>
                        <div class="json-view">
                            <pre><?php echo htmlspecialchars(json_encode($result['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
                        </div>
                    </details>
                <?php else: ?>
                    <div class="loading">
                        <p>Nenhum dado disponível no momento.</p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
