# 🚀 Guia Rápido de Instalação

## Passo 1: Instalar o Composer

Se você ainda não tem o Composer instalado:

1. Baixe o Composer em: https://getcomposer.org/download/
2. Execute o instalador do Windows
3. Reinicie o terminal/CMD

## Passo 2: Instalar Dependências

Abra o terminal na pasta do projeto e execute:

```bash
composer install
```

## Passo 3: Configurar Credenciais

Edite o arquivo `.env` com suas credenciais OAuth2:

```env
OAUTH_CLIENT_ID=seu_client_id_aqui
OAUTH_CLIENT_SECRET=seu_client_secret_aqui
OAUTH_TOKEN_URL=https://api.example.com/oauth/token
API_BASE_URL=https://api.example.com/api
```

## Passo 4: Executar o Servidor

```bash
php -S localhost:8000 -t public
```

## Passo 5: Acessar

Abra no navegador:
http://localhost:8000

---

## 🧪 Testando com API Mockada

Se quiser testar sem uma API real, você pode usar um serviço de mock como:
- https://reqres.in/
- https://jsonplaceholder.typicode.com/
- https://httpbin.org/

Exemplo de configuração para teste:

```env
OAUTH_CLIENT_ID=test
OAUTH_CLIENT_SECRET=test
OAUTH_TOKEN_URL=https://httpbin.org/post
API_BASE_URL=https://jsonplaceholder.typicode.com
```

---

## ❓ Problemas Comuns

### Composer não encontrado
- Instale via: https://getcomposer.org/
- Adicione ao PATH do Windows

### Erro de permissões no .env
- No Windows, clique com botão direito > Propriedades > Segurança

### PHP não encontrado
- Baixe em: https://windows.php.net/download/
- Adicione ao PATH do sistema

---

## 📞 Suporte

Veja o README.md completo para mais detalhes.
