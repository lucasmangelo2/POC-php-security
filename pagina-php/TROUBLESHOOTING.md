# 🔧 Guia de Troubleshooting

## Problemas Comuns e Soluções

### 1. "Falha na autenticação OAuth2"

**Causa**: Credenciais incorretas ou endpoint inválido

**Solução**:
- Verifique se `OAUTH_CLIENT_ID` e `OAUTH_CLIENT_SECRET` estão corretos no `.env`
- Confirme se `OAUTH_TOKEN_URL` é o endpoint correto
- Teste o endpoint manualmente com curl:
```bash
curl -X POST https://api.example.com/oauth/token \
  -d "grant_type=client_credentials" \
  -d "client_id=seu_client_id" \
  -d "client_secret=seu_client_secret"
```

### 2. "Class 'Dotenv\Dotenv' not found"

**Causa**: Dependências não instaladas

**Solução**:
```bash
composer install
```

### 3. Página em branco / Sem erros visíveis

**Causa**: Erros do PHP não estão sendo exibidos

**Solução**:
- Habilite debug no `.env`:
```env
APP_ENV=development
APP_DEBUG=true
```
- Verifique logs do PHP:
  - Windows: `C:\php\logs\php_error.log`
  - Linux: `/var/log/php/error.log`

### 4. "Cannot load Dotenv\Dotenv"

**Causa**: Autoload não gerado

**Solução**:
```bash
composer dump-autoload
```

### 5. Token expirando muito rápido

**Causa**: Servidor com horário incorreto

**Solução**:
- Sincronize o relógio do servidor
- Windows: `w32tm /resync`
- Linux: `sudo ntpdate -s time.nist.gov`

### 6. CORS errors no navegador

**Causa**: API externa bloqueando requisições

**Solução**:
- O projeto já faz requisições server-side (não sofre CORS)
- Se precisar fazer requisições do cliente, implemente um proxy PHP

### 7. "SSL certificate problem"

**Causa**: Certificado SSL não confiável

**Solução para desenvolvimento** (NÃO use em produção):
```php
// Em OAuth2Service.php ou ApiService.php
$this->client = new Client([
    'verify' => false, // APENAS PARA DESENVOLVIMENTO
]);
```

**Solução para produção**:
- Instale certificados CA atualizados
- Windows: Baixe `cacert.pem` de https://curl.se/docs/caextract.html
- Configure no `php.ini`:
```ini
curl.cainfo = "C:\php\extras\ssl\cacert.pem"
```

### 8. "Maximum execution time exceeded"

**Causa**: API externa muito lenta

**Solução**:
- Aumente o timeout em `OAuth2Service.php` e `ApiService.php`:
```php
$this->client = new Client([
    'timeout' => 60, // 60 segundos
]);
```

### 9. Erro 401 mesmo com credenciais corretas

**Causa**: Token expirado ou cache corrompido

**Solução**:
- O sistema já renova automaticamente
- Se persistir, verifique o formato do header Authorization
- Algumas APIs usam `Bearer token`, outras `token`

### 10. Composer muito lento no Windows

**Solução**:
```bash
composer config --global repo.packagist composer https://mirrors.aliyun.com/composer/
```

---

## 🔍 Comandos Úteis para Debug

### Testar configuração:
```bash
php tests/test-config.php
```

### Verificar sintaxe PHP:
```bash
php -l src/Services/OAuth2Service.php
```

### Ver informações do PHP:
```bash
php -i
```

### Testar servidor local:
```bash
php -S localhost:8000 -t public
```

### Limpar cache do Composer:
```bash
composer clear-cache
```

---

## 📊 Logs e Monitoramento

### Habilitar log detalhado:

Adicione no início dos arquivos de serviço:
```php
error_log("OAuth2: Iniciando requisição de token");
error_log("API: Response = " . json_encode($response));
```

### Ver logs em tempo real:

**Windows PowerShell**:
```powershell
Get-Content php_error.log -Wait -Tail 30
```

**Linux**:
```bash
tail -f /var/log/php/error.log
```

---

## 🆘 Ainda com Problemas?

1. Verifique se todas as extensões PHP estão instaladas:
```bash
php -m
```

2. Teste a conexão com a API:
```bash
php examples/example.php
```

3. Verifique permissões do arquivo `.env`:
- Deve ser legível pelo usuário do servidor web
- Não deve ser acessível publicamente

4. Revise as configurações do servidor web:
- Apache: Verifique se `mod_rewrite` está habilitado
- Nginx: Verifique o arquivo de configuração

---

## 📞 Recursos Adicionais

- [Documentação OAuth2](https://oauth.net/2/)
- [Guzzle Documentation](https://docs.guzzlephp.org/)
- [PHP dotenv](https://github.com/vlucas/phpdotenv)
- [PSR-4 Autoloading](https://www.php-fig.org/psr/psr-4/)
