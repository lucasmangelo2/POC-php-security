# Projeto PHP com OAuth2 Seguro

Este projeto implementa uma aplicação PHP que consome uma API externa com autenticação OAuth2 (Client Credentials Grant), seguindo as melhores práticas de segurança.

## 🔒 Características de Segurança

- **Credenciais protegidas**: Client ID e Client Secret nunca são expostos ao cliente
- **Variáveis de ambiente**: Credenciais armazenadas em arquivo `.env` (não versionado)
- **Cache de token**: Tokens são armazenados em memória para evitar requisições desnecessárias
- **Headers de segurança**: X-Frame-Options, X-Content-Type-Options, etc.
- **HTTPS**: Verificação SSL/TLS habilitada
- **Sanitização**: Output escapado para prevenir XSS
- **Retry logic**: Renovação automática de token expirado

## 📋 Pré-requisitos

- PHP >= 7.4
- Composer
- Extensões PHP: curl, json, mbstring

## 🚀 Instalação

1. **Instale as dependências:**
```bash
composer install
```

2. **Configure as variáveis de ambiente:**
```bash
copy .env.example .env
```

3. **Edite o arquivo `.env` com suas credenciais:**
```env
OAUTH_CLIENT_ID=seu_client_id
OAUTH_CLIENT_SECRET=seu_client_secret
OAUTH_TOKEN_URL=https://api.example.com/oauth/token
API_BASE_URL=https://api.example.com/api
```

## 🏃 Executando

### Desenvolvimento (servidor embutido do PHP):
```bash
php -S localhost:8000 -t public
```

### Produção:
Configure seu servidor web (Apache/Nginx) para apontar para a pasta `public/`.

## 📁 Estrutura do Projeto

```
.
├── public/
│   └── index.php          # Ponto de entrada da aplicação
├── src/
│   ├── Config/
│   │   └── Config.php     # Gerenciamento de configurações
│   └── Services/
│       ├── OAuth2Service.php  # Autenticação OAuth2
│       └── ApiService.php     # Consumo da API externa
├── .env.example           # Exemplo de configuração
├── .gitignore            # Arquivos ignorados pelo Git
├── composer.json         # Dependências do projeto
└── README.md            # Este arquivo
```

## 🔧 Configuração

### Variáveis de Ambiente

| Variável | Descrição | Exemplo |
|----------|-----------|---------|
| `OAUTH_CLIENT_ID` | ID do cliente OAuth2 | `abc123` |
| `OAUTH_CLIENT_SECRET` | Secret do cliente OAuth2 | `xyz789` |
| `OAUTH_TOKEN_URL` | Endpoint para obter token | `https://api.example.com/oauth/token` |
| `API_BASE_URL` | URL base da API | `https://api.example.com/api` |
| `APP_ENV` | Ambiente da aplicação | `development` ou `production` |
| `APP_DEBUG` | Modo debug | `true` ou `false` |

## 🛡️ Boas Práticas Implementadas

1. **Separação de responsabilidades**: Classes específicas para configuração, OAuth2 e API
2. **Singleton para configuração**: Evita múltiplas leituras do arquivo .env
3. **Injeção de dependências**: Serviços desacoplados
4. **PSR-4 Autoloading**: Organização de código seguindo padrões
5. **Error handling**: Tratamento adequado de exceções
6. **Logging**: Logs apenas em modo debug
7. **Timeout configurado**: Evita travamentos em requisições
8. **Prevent cloning/serialization**: Singleton protegido

## 📝 Como Usar com Diferentes APIs

Para usar com uma API real, ajuste o arquivo `.env` com os dados corretos e, se necessário, modifique o método `fetchData()` em `ApiService.php` para especificar o endpoint correto.

### Exemplo com GitHub API (usando OAuth Apps):
```env
OAUTH_CLIENT_ID=seu_github_client_id
OAUTH_CLIENT_SECRET=seu_github_client_secret
OAUTH_TOKEN_URL=https://github.com/login/oauth/access_token
API_BASE_URL=https://api.github.com
```

## 🐛 Debug

Para habilitar logs de erro, configure no `.env`:
```env
APP_ENV=development
APP_DEBUG=true
```

Os logs serão salvos no error_log do PHP.

## 📦 Dependências

- **vlucas/phpdotenv**: Carregamento de variáveis de ambiente
- **guzzlehttp/guzzle**: Cliente HTTP moderno e robusto

## 🔐 Segurança em Produção

Antes de colocar em produção:

1. ✅ Configure `APP_ENV=production` e `APP_DEBUG=false`
2. ✅ Use HTTPS (SSL/TLS)
3. ✅ Defina permissões corretas no arquivo `.env` (600 ou 400)
4. ✅ Configure headers de segurança no servidor web
5. ✅ Implemente rate limiting
6. ✅ Configure logs adequados
7. ✅ Use cache externo (Redis/Memcached) para tokens em ambientes distribuídos

## 📄 Licença

Este projeto é open source e está disponível sob a licença MIT.
