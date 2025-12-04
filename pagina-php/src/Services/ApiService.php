<?php

namespace App\Services;

use App\Config\Config;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ApiService
{
    private $client;
    private $config;
    private $oauth2Service;

    public function __construct()
    {
        $this->config = Config::getInstance();
        $this->oauth2Service = new OAuth2Service();
        $this->client = new Client([
            'base_uri' => $this->config->get('api.base_url'),
            'timeout' => 30,
            'verify' => true,
        ]);
    }

    /**
     * Busca dados da API externa
     */
    public function fetchData(string $endpoint = '/api/info'): array
    {
        $token = $this->oauth2Service->getAccessToken();

        if (!$token) {
            return [
                'success' => false,
                'error' => 'Falha na autenticação OAuth2',
                'data' => []
            ];
        }

        try {
            $response = $this->client->get($endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => true,
                'data' => $data,
                'error' => null
            ];

        } catch (GuzzleException $e) {
            // Se token expirou, tenta limpar cache e fazer nova tentativa
            if ($e->getCode() === 401) {
                $this->oauth2Service->clearTokenCache();
                return $this->retryRequest($endpoint);
            }

            $this->logError('Erro ao buscar dados da API', [
                'endpoint' => $endpoint,
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ]);

            return [
                'success' => false,
                'error' => 'Erro ao buscar dados: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * Tenta fazer a requisição novamente
     */
    private function retryRequest(string $endpoint): array
    {
        $token = $this->oauth2Service->getAccessToken();

        if (!$token) {
            return [
                'success' => false,
                'error' => 'Falha na reautenticação',
                'data' => []
            ];
        }

        try {
            $response = $this->client->get($endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => true,
                'data' => $data,
                'error' => null
            ];

        } catch (GuzzleException $e) {
            return [
                'success' => false,
                'error' => 'Erro na segunda tentativa: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * Log de erros
     */
    private function logError(string $message, array $context = []): void
    {
        if ($this->config->get('app.debug')) {
            error_log(sprintf(
                '[ApiService] %s: %s',
                $message,
                json_encode($context)
            ));
        }
    }
}
