<?php

namespace App\Config;

use Dotenv\Dotenv;

class Config
{
    private static $instance = null;
    private $config = [];

    private function __construct()
    {
        $this->loadEnv();
        $this->loadConfig();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function loadEnv(): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
    }

    private function loadConfig(): void
    {
        $this->config = [
            'oauth' => [
                'client_id' => $_ENV['OAUTH_CLIENT_ID'] ?? '',
                'client_secret' => $_ENV['OAUTH_CLIENT_SECRET'] ?? '',
                'token_url' => $_ENV['OAUTH_TOKEN_URL'] ?? '',
            ],
            'api' => [
                'base_url' => $_ENV['API_BASE_URL'] ?? '',
            ],
            'app' => [
                'env' => $_ENV['APP_ENV'] ?? 'production',
                'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
            ]
        ];
    }

    public function get(string $key, $default = null)
    {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }

    // Previne clonagem
    private function __clone() {}

    // Previne unserialize
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }
}
