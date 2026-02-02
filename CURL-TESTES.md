# Testes com curl - Forum Laravel

Documentação dos testes realizados com curl durante o desenvolvimento.

## Configuração

- Docker rodando (`docker-compose up -d`)
- Laravel ativo (`php artisan serve`)
- Base URL: `http://localhost:8000/api`

## Testes com curl

### Registro
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Forum User","email":"forum@teste.com","password":"12345678"}'
```
 Status 201 + token Bearer

### Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"forum@teste.com","password":"12345678"}'
```
 Status 200 + novo token

### Verificar Usuário
```bash
curl -X GET http://localhost:8000/api/user \
  -H "Authorization: Bearer TOKEN_AQUI"
```
 Status 200 com token / 401 sem token

### Logout
```bash
curl -X POST http://localhost:8000/api/logout \
  -H "Authorization: Bearer TOKEN_AQUI"
```
 Status 200 + token invalidado

## Validações Testadas

### Email Duplicado
```bash
curl -X POST http://localhost:8000/api/register \
  -d '{"name":"Outro","email":"forum@teste.com","password":"12345678"}'
```
 Status 422 - "The email has already been taken."

### Senha Curta
```bash
curl -X POST http://localhost:8000/api/register \
  -d '{"name":"Teste","email":"novo@teste.com","password":"123"}'
```
 Status 422 - "Password must be at least 8 characters."

### Campo Obrigatório
```bash
curl -X POST http://localhost:8000/api/register \
  -d '{"email":"teste@teste.com","password":"12345678"}'
```
 Status 422 - "The name field is required."

### Credenciais Inválidas
```bash
curl -X POST http://localhost:8000/api/login \
  -d '{"email":"forum@teste.com","password":"senha-errada"}'
```
 Status 401 - "Credenciais inválidas"

## Verificações de Segurança

-  Senhas hasheadas com bcrypt ($2y$)
-  Tokens únicos por login
-  Token invalidado após logout
-  Middleware de autenticação funcionando

## Resumo

**Funcionalidades:**  Registro, Login, Logout, Verificação  
**Validações:**  Email único, senha mínima, campos obrigatórios  
**Segurança:**  Hash, tokens únicos, invalidação, middleware
