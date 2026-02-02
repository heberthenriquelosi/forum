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

## Testes de Questions

### Listar Questions (Público)
```bash
curl -X GET http://localhost:8000/api/questions
```
 Status 200 + array de questions com autores

### Criar Question (Autenticado)
```bash
curl -X POST http://localhost:8000/api/questions \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer TOKEN_AQUI" \
  -d '{"title":"Como usar Laravel?","content":"Preciso de ajuda para começar com Laravel"}'
```
 Status 201 + question criada com autor e slug automático

### Ver Question Específica (Público)
```bash
curl -X GET http://localhost:8000/api/questions/UUID_DA_QUESTION
```
 Status 200 + question com dados do autor

### Editar Question (Só Autor)
```bash
curl -X PUT http://localhost:8000/api/questions/UUID_DA_QUESTION \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer TOKEN_AQUI" \
  -d '{"title":"Como usar Laravel - EDITADO","content":"Conteúdo editado"}'
```
 Status 200 + question atualizada com novo slug

### Deletar Question (Só Autor)
```bash
curl -X DELETE http://localhost:8000/api/questions/UUID_DA_QUESTION \
  -H "Authorization: Bearer TOKEN_AQUI"
```
 Status 200 + mensagem "Pergunta deletada com sucesso"

### Validações de Questions

#### Título Obrigatório
```bash
curl -X POST http://localhost:8000/api/questions \
  -H "Authorization: Bearer TOKEN_AQUI" \
  -d '{"content":"Só conteúdo"}'
```
 Status 422 - "The title field is required."

#### Conteúdo Obrigatório
```bash
curl -X POST http://localhost:8000/api/questions \
  -H "Authorization: Bearer TOKEN_AQUI" \
  -d '{"title":"Só título"}'
```
 Status 422 - "The content field is required."

#### Autorização (Tentar editar question de outro usuário)
```bash
curl -X PUT http://localhost:8000/api/questions/UUID_DE_OUTRO_USUARIO \
  -H "Authorization: Bearer TOKEN_DIFERENTE" \
  -d '{"title":"Tentativa","content":"Hack"}'
```
 Status 403 - "Não autorizado"

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

**Autenticação:**  Registro, Login, Logout, Verificação  
**Questions (Perguntas):**  CRUD completo, Validações, Autorização, Slug automático  
**Validações:**  Email único, senha mínima, campos obrigatórios  
**Segurança:**  Hash, tokens únicos, invalidação, middleware
