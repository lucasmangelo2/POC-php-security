using Microsoft.AspNetCore.Authentication.JwtBearer;
using Microsoft.AspNetCore.Authorization;
using Microsoft.IdentityModel.Tokens;
using System.IdentityModel.Tokens.Jwt;
using System.Security.Claims;
using System.Text;

var builder = WebApplication.CreateBuilder(args);

// Configurar JWT Authentication
var jwtSettings = builder.Configuration.GetSection("JwtSettings");
var secretKey = jwtSettings["SecretKey"] ?? throw new InvalidOperationException("SecretKey não configurada");

builder.Services.AddAuthentication(options =>
{
    options.DefaultAuthenticateScheme = JwtBearerDefaults.AuthenticationScheme;
    options.DefaultChallengeScheme = JwtBearerDefaults.AuthenticationScheme;
})
.AddJwtBearer(options =>
{
    options.TokenValidationParameters = new TokenValidationParameters
    {
        ValidateIssuer = true,
        ValidateAudience = true,
        ValidateLifetime = true,
        ValidateIssuerSigningKey = true,
        ValidIssuer = jwtSettings["Issuer"],
        ValidAudience = jwtSettings["Audience"],
        IssuerSigningKey = new SymmetricSecurityKey(Encoding.UTF8.GetBytes(secretKey))
    };
});

builder.Services.AddAuthorization();
builder.Services.AddSingleton<IClientService, ClientService>();

var app = builder.Build();

app.UseAuthentication();
app.UseAuthorization();

// Endpoint para obter token OAuth2 (Client Credentials)
app.MapPost("/oauth/token", async (TokenRequest request, IClientService clientService, IConfiguration config) =>
{
    // Validar grant_type
    if (request.grant_type != "client_credentials")
    {
        return Results.BadRequest(new { error = "unsupported_grant_type", error_description = "Grant type não suportado. Use 'client_credentials'." });
    }

    // Validar client_id e client_secret
    if (!clientService.ValidateClient(request.client_id, request.client_secret))
    {
        return Results.Unauthorized();
    }

    // Gerar token JWT
    var jwtSettings = config.GetSection("JwtSettings");
    var secretKey = jwtSettings["SecretKey"] ?? "";
    var issuer = jwtSettings["Issuer"] ?? "";
    var audience = jwtSettings["Audience"] ?? "";
    var expirationMinutes = int.Parse(jwtSettings["ExpirationMinutes"] ?? "60");

    var securityKey = new SymmetricSecurityKey(Encoding.UTF8.GetBytes(secretKey));
    var credentials = new SigningCredentials(securityKey, SecurityAlgorithms.HmacSha256);

    var claims = new[]
    {
        new Claim(JwtRegisteredClaimNames.Sub, request.client_id),
        new Claim(JwtRegisteredClaimNames.Jti, Guid.NewGuid().ToString()),
        new Claim("client_id", request.client_id)
    };

    var token = new JwtSecurityToken(
        issuer: issuer,
        audience: audience,
        claims: claims,
        expires: DateTime.UtcNow.AddMinutes(expirationMinutes),
        signingCredentials: credentials
    );

    var tokenString = new JwtSecurityTokenHandler().WriteToken(token);

    var response = new TokenResponse
    {
        access_token = tokenString,
        token_type = "Bearer",
        expires_in = expirationMinutes * 60
    };

    return Results.Ok(response);
});

// Endpoint protegido - Lista informações básicas
app.MapGet("/api/info", [Authorize] (HttpContext context) =>
{
    var clientId = context.User.FindFirst("client_id")?.Value ?? "desconhecido";
    
    var info = new InfoResponse
    {
        Message = "Acesso autorizado com sucesso!",
        ClientId = clientId,
        Timestamp = DateTime.UtcNow,
        Data = new List<InfoItem>
        {
            new InfoItem { Id = 1, Name = "Item 1", Description = "Informação básica 1" },
            new InfoItem { Id = 2, Name = "Item 2", Description = "Informação básica 2" },
            new InfoItem { Id = 3, Name = "Item 3", Description = "Informação básica 3" }
        }
    };

    return Results.Ok(info);
});

// Endpoint de health check (não protegido)
app.MapGet("/health", () => Results.Ok(new { status = "healthy", timestamp = DateTime.UtcNow }));

app.Run();

// Models
public record TokenRequest(string grant_type, string client_id, string client_secret);

public record TokenResponse
{
    public string access_token { get; set; } = string.Empty;
    public string token_type { get; set; } = string.Empty;
    public int expires_in { get; set; }
}

public record InfoResponse
{
    public string Message { get; set; } = string.Empty;
    public string ClientId { get; set; } = string.Empty;
    public DateTime Timestamp { get; set; }
    public List<InfoItem> Data { get; set; } = new();
}

public record InfoItem
{
    public int Id { get; set; }
    public string Name { get; set; } = string.Empty;
    public string Description { get; set; } = string.Empty;
}

// Client Service Interface
public interface IClientService
{
    bool ValidateClient(string clientId, string clientSecret);
}

// Client Service Implementation
public class ClientService : IClientService
{
    // Em produção, isso deveria vir de um banco de dados
    private readonly Dictionary<string, string> _clients = new()
    {
        { "client-app-1", "secret-123456" },
        { "client-app-2", "secret-789012" },
        { "mobile-app", "mobile-secret-abc" }
    };

    public bool ValidateClient(string clientId, string clientSecret)
    {
        if (string.IsNullOrEmpty(clientId) || string.IsNullOrEmpty(clientSecret))
            return false;

        return _clients.TryGetValue(clientId, out var storedSecret) && storedSecret == clientSecret;
    }
}
