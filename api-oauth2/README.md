# API OAuth2 - Client Credentials Flow

Esta é uma API em .NET Core 8.0 que implementa o fluxo OAuth2 Client Credentials para autenticação e autorização de consumidores de API.

## 🚀 Funcionalidades

- ✅ Autenticação OAuth2 com Client Credentials (client_id e client_secret)
- ✅ Geração de tokens JWT
- ✅ Endpoint protegido para listagem de informações básicas
- ✅ Validação de tokens em requisições

## 📋 Pré-requisitos

- .NET 8.0 SDK
- Visual Studio Code ou Visual Studio 2022

## 🔧 Instalação

1. Restaure as dependências:
```bash
dotnet restore
```

2. Execute a aplicação:
```bash
dotnet run
```

A API estará disponível em: `http://localhost:5000` ou `https://localhost:5001`

## 📡 Endpoints

### 1. Obter Token (POST /oauth/token)

Obtém um token de acesso usando Client Credentials.

**Request:**
```bash
curl -X POST http://localhost:5000/oauth/token ^
  -H "Content-Type: application/json" ^
  -d "{\"grant_type\":\"client_credentials\",\"client_id\":\"client-app-1\",\"client_secret\":\"secret-123456\"}"
```

**Response:**
```json
{
  "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "token_type": "Bearer",
  "expires_in": 3600
}
```

### 2. Listar Informações (GET /api/info) 🔒

Endpoint protegido que retorna informações básicas. Requer token válido.

**Request:**
```bash
curl -X GET http://localhost:5000/api/info ^
  -H "Authorization: Bearer {seu_token_aqui}"
```

**Response:**
```json
{
  "message": "Acesso autorizado com sucesso!",
  "clientId": "client-app-1",
  "timestamp": "2025-12-04T10:30:00Z",
  "data": [
    {
      "id": 1,
      "name": "Item 1",
      "description": "Informação básica 1"
    },
    {
      "id": 2,
      "name": "Item 2",
      "description": "Informação básica 2"
    },
    {
      "id": 3,
      "name": "Item 3",
      "description": "Informação básica 3"
    }
  ]
}
```

### 3. Health Check (GET /health)

Verifica o status da API (não requer autenticação).

**Request:**
```bash
curl -X GET http://localhost:5000/health
```

**Response:**
```json
{
  "status": "healthy",
  "timestamp": "2025-12-04T10:30:00Z"
}
```

## 🔑 Clientes Pré-configurados

A API possui os seguintes clientes configurados em memória:

| Client ID      | Client Secret      |
|----------------|-------------------|
| client-app-1   | secret-123456     |
| client-app-2   | secret-789012     |
| mobile-app     | mobile-secret-abc |

## 🧪 Testando o Fluxo Completo

### Passo 1: Obter o Token
```bash
curl -X POST http://localhost:5000/oauth/token ^
  -H "Content-Type: application/json" ^
  -d "{\"grant_type\":\"client_credentials\",\"client_id\":\"client-app-1\",\"client_secret\":\"secret-123456\"}"
```

### Passo 2: Usar o Token para Acessar o Endpoint Protegido
```bash
curl -X GET http://localhost:5000/api/info ^
  -H "Authorization: Bearer {cole_o_token_aqui}"
```

## ⚙️ Configurações

As configurações JWT estão em `appsettings.json`:

```json
{
  "JwtSettings": {
    "SecretKey": "MinhaChaveSecretaSuperSeguraComPeloMenos32Caracteres!",
    "Issuer": "OAuth2Api",
    "Audience": "OAuth2ApiClients",
    "ExpirationMinutes": "60"
  }
}
```

**⚠️ IMPORTANTE:** Em produção, mova a `SecretKey` para variáveis de ambiente ou Azure Key Vault!

## 🏗️ Estrutura do Projeto

```
OAuth2Api/
├── Program.cs                 # Configuração principal e endpoints
├── OAuth2Api.csproj          # Dependências do projeto
├── appsettings.json          # Configurações da aplicação
└── appsettings.Development.json
```

## 🔐 Segurança

- ✅ Tokens JWT com assinatura HMAC-SHA256
- ✅ Validação de issuer, audience e tempo de expiração
- ✅ Client credentials armazenados de forma segura (em produção, use banco de dados com hash)
- ⚠️ Para produção, considere:
  - Armazenar clientes em banco de dados
  - Usar hash para client_secret (BCrypt, PBKDF2)
  - HTTPS obrigatório
  - Rate limiting
  - Logging de tentativas de acesso

## 📝 Notas

- O token expira em 60 minutos por padrão
- Os clientes estão armazenados em memória para fins de demonstração
- Em produção, implemente armazenamento persistente e seguro de credenciais

## 🤝 Contribuindo

Sinta-se à vontade para abrir issues ou enviar pull requests!

## 📄 Licença

Este projeto é de código aberto e está disponível sob a licença MIT.
