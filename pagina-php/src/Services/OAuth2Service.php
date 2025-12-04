<?php

namespace App\Services;

use App\Config\Config;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class OAuth2Service
{
    private $client;
    private $config;
    private $tokenCache = null;
    private $tokenExpiry = null;

    public function __construct()
    {
        $this->config = Config::getInstance();
        $this->client = new Client([
            'timeout' => 30,
            'verify' => true, // Verificar certificados SSL em produção
        ]);
    }

    /**
     * Obtém um token de acesso válido (usando cache quando possível)
     */
    public function getAccessToken(): ?string
    {
        // Verifica se há token em cache e se ainda é válido
        if ($this->tokenCache && $this->tokenExpiry && time() < $this->tokenExpiry) {
            return $this->tokenCache;
        }

        // Requisita novo token
        return $this->requestNewToken();
    }

    /**
     * Requisita um novo token OAuth2
     */
    private function requestNewToken(): ?string
    {
        try {
            $response = $this->client->post($this->config->get('oauth.token_url'), [
                'json' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->config->get('oauth.client_id'),
                    'client_secret' => $this->config->get('oauth.client_secret'),
                ],
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (isset($data['access_token'])) {
                $this->tokenCache = $data['access_token'];
                
                // Define expiração (padrão 3600 segundos, com margem de segurança)
                $expiresIn = $data['expires_in'] ?? 3600;
                $this->tokenExpiry = time() + ($expiresIn - 60);

                return $this->tokenCache;
            }

            $this->logError('Token não encontrado na resposta', $data);
            return null;

        } catch (GuzzleException $e) {
            $this->logError('Erro ao obter token OAuth2', [
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            return null;
        }
    }

    /**
     * Limpa o cache do token
     */
    public function clearTokenCache(): void
    {
        $this->tokenCache = null;
        $this->tokenExpiry = null;
    }

    /**
     * Log de erros (apenas em modo debug)
     */
    private function logError(string $message, array $context = []): void
    {
        if ($this->config->get('app.debug')) {
            error_log(sprintf(
                '[OAuth2Service] %s: %s',
                $message,
                json_encode($context)
            ));
        }
    }
}
