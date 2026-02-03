# Forum - Sistema de Fórum Laravel

Sistema de fórum desenvolvido em Laravel.

## Como Rodar o Projeto

### Pré-requisitos
- Docker e Docker Compose
- PHP 8.1+ 

### Instalação

1. **Clone o repositório**
```bash
git clone https://github.com/heberthenriquelosi/forum.git
cd forum
```

2. **Instale as dependências**
```bash
composer install
```

3. **Configure o ambiente**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Suba o banco de dados com Docker**
```bash
docker-compose up -d
```

5. **Execute as migrations e popule o banco**
```bash
php artisan migrate --seed
```

**💡 Dados de teste criados:**
- **Email:** `test@example.com`
- **Senha:** `password`

6. **Inicie o servidor**
```bash
php artisan serve
```

**O projeto estará rodando em:** `http://localhost:8000`

### Verificação
```bash
# Verificar se o Docker está rodando
docker-compose ps

# Testar conexão com o banco
php artisan migrate:status
```

### Como Rodar os Testes
```bash
# Executar todos os testes
php artisan test

# Executar testes específicos
php artisan test --filter AuthTest
php artisan test --filter QuestionTest
php artisan test --filter AnswerTest
```

## 🔌 Rotas da API (Endpoints)

### Autenticação
- `POST /api/register` - Criar conta
- `POST /api/login` - Entrar e receber Token Bearer
- `POST /api/logout` - Sair (requer token)
- `GET /api/user` - Dados do usuário logado (requer token)

### Perguntas (Questions)
- `GET /api/questions` - Listar perguntas
- `POST /api/questions` - Criar pergunta (requer token)
- `GET /api/questions/{id}` - Ver pergunta específica
- `PUT /api/questions/{id}` - Editar pergunta (apenas autor)
- `DELETE /api/questions/{id}` - Deletar pergunta (apenas autor)

### Respostas (Answers)
- `GET /api/questions/{id}/answers` - Listar respostas de uma pergunta
- `POST /api/questions/{id}/answers` - Responder pergunta (requer token)
- `GET /api/answers/{id}` - Ver resposta específica
- `PUT /api/answers/{id}` - Editar resposta (apenas autor)
- `DELETE /api/answers/{id}` - Deletar resposta (apenas autor)

### 📝 Exemplo de Uso Rápido
```bash
# 1. Registrar usuário
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test User","email":"test@example.com","password":"12345678"}'

# 2. Criar pergunta (usar o token retornado)
curl -X POST http://localhost:8000/api/questions \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer SEU_TOKEN_AQUI" \
  -d '{"title":"Como usar Laravel?","content":"Preciso de ajuda"}'
```

## Decisões Técnicas Tomadas

### UUID v4 como Identificador
- **Decisão:** Usar UUID v4 ao invés de auto-increment
- **Motivo:** Maior segurança, impossibilita enumeration attacks, melhor para sistemas distribuídos
- **Implementação:** Trait nativo `HasUuids` do Laravel

### Relacionamento Polimórfico para Anexos
- **Decisão:** Uma tabela `attachments` para Questions e Answers
- **Motivo:** Evita duplicação de código e tabelas, mantém flexibilidade
- **Implementação:** `morphMany` e `morphTo` do Eloquent

### Docker para Banco de Dados
- **Decisão:** MySQL 8.0 via Docker Compose
- **Motivo:** Facilita setup do ambiente, isolamento, reprodutibilidade
- **Configuração:** Porta 3307 para evitar conflitos

### Laravel Sanctum para Autenticação
- **Decisão:** Usar Laravel Sanctum ao invés de JWT
- **Motivo:** Nativo do Laravel, mais simples de implementar

### Policies para Autorização
- **Decisão:** Implementar Policies ao invés de verificações manuais
- **Motivo:** Separação de responsabilidades, reutilização, Laravel best practices
- **Implementação:** `QuestionPolicy` e `AnswerPolicy` com `$this->authorize()`

### Form Requests para Validação
- **Decisão:** Extrair validação para classes dedicadas
- **Motivo:** Organização do código, reutilização, separação de responsabilidades
- **Implementação:** `RegisterRequest` e `LoginRequest`

### Estrutura de Models
- **Decisão:** Relacionamentos com chaves estrangeiras nomeadas
- **Motivo:** Clareza no código, facilita manutenção
- **Exemplo:** `author_id` ao invés de `user_id` genérico

## Sistema de Autenticação

### Fluxo de Autenticação

1. **Registro/Login:** Retorna token Bearer
2. **Requisições:** Incluir `Authorization: Bearer {token}` no header
3. **Logout:** Invalida o token atual

### Segurança
- Tokens únicos por sessão
- Senhas com hash bcrypt
- Middleware de proteção em rotas sensíveis
- **Policies de autorização** para Questions e Answers
- **Form Requests** para validação consistente
- Verificação automática de propriedade (só autor edita/deleta)

## Sistema de Questions (Perguntas)

### Funcionalidades
-  CRUD completo de questions
-  **Form Requests** para validação de título e conteúdo
-  **Policy-based authorization** (só autor pode editar/deletar)
-  Slug automático único
-  Relacionamento com autor
-  Route Model Binding (busca por UUID)

## Sistema de Answers (Respostas)

### Funcionalidades
-  CRUD completo de answers
-  Validação de conteúdo via inline validation
-  **Policy-based authorization** (só autor pode editar/deletar)
-  Relacionamento com question e autor

## Arquitetura e Boas Práticas Implementadas

### Policies (Autorização)
- **QuestionPolicy:** Controla acesso a operações de Questions
- **AnswerPolicy:** Controla acesso a operações de Answers
- **AuthServiceProvider:** Registra mapeamento Model → Policy
- **Benefícios:** Código limpo, reutilizável, testável

### Form Requests (Validação)
- **RegisterRequest:** Validação de registro de usuário
- **LoginRequest:** Validação de login
- **Benefícios:** Separação de responsabilidades, validação centralizada

### Services (Lógica de Negócio)
- **Estrutura preparada:** Models com relacionamentos polimórficos para anexos
- **Benefícios:** Base sólida para implementação futura de upload de arquivos

### Resources (Padronização de Saída)
- **Estrutura preparada:** Models prontos para implementação de Resources
- **Benefícios:** Base para padronização futura da saída da API

### Testes Automatizados (PHPUnit)
- **AuthTest:** Testes de autenticação (registro, login, logout)
- **QuestionTest:** Testes de CRUD de questions com autorização
- **AnswerTest:** Testes de CRUD de answers com autorização
- **Factories:** QuestionFactory e AnswerFactory para dados de teste
- **Metodologia:** Baseados nos testes manuais com curl documentados em CURL-TESTES.md
- **Benefícios:** Garantia de estabilidade, cobertura dos fluxos principais

## O que Optei por Não Implementar e Por Quê

### Sistema de Anexos
- **Por quê:** Estrutura de banco implementada, mas Services e integração nos controllers não foram desenvolvidos
- **Impacto:** Apenas estrutura polimórfica existe, sem funcionalidade de upload
- **Decisão:** Priorizar CRUD funcional e testes sobre funcionalidades secundárias

### Interfaces/Contratos
- **Por quê:** Para este escopo, Policies e Services são suficientes sem over-engineering
- **Impacto:** Menor flexibilidade para injeção de dependência avançada
- **Decisão:** Evitar complexidade desnecessária conforme orientação do teste

### Rate Limiting
- **Por quê:** Foco na funcionalidade core primeiro
- **Impacto:** Vulnerável a ataques de força bruta

### Refresh Tokens
- **Por quê:** Sanctum já gerencia expiração automaticamente
- **Impacto:** Tokens não podem ser renovados sem novo login

### Verificação de Email
- **Por quê:** Adiciona complexidade desnecessária para MVP
- **Impacto:** Contas podem ser criadas com emails inválidos

## Pontos que Melhoraria com Mais Tempo

### Sistema de Anexos
- Integrar AttachmentService nos controllers de Question e Answer
- Implementar endpoints para upload/download de arquivos
- Adicionar validação de tipos de arquivo e tamanhos
- Criar testes para upload de arquivos

### Interfaces e Contratos
- Criar interfaces para Services (AttachmentServiceInterface)
- Adicionar Service Providers customizados para injeção de dependência

### Arquitetura
- Implementar Resources para User, Question e Answer
- Adicionar Form Requests para Answer (create/update)
- Criar Services para Question e Answer (extrair lógica dos Controllers)
- Implementar Event/Listener para ações do sistema

### Autenticação
- Implementar rate limiting (5 tentativas/minuto)
- Sistema de recuperação de senha
- Verificação de email obrigatória

### Segurança
- Logs de tentativas de login
- Detecção de IPs suspeitos
- Implementar CORS adequado para produção

## Estrutura Atual do Banco

### Users
- `id` (UUID), `name`, `email`, `password`, `timestamps`

### Questions  
- `id` (UUID), `author_id` (FK), `title`, `content`, `slug`, `timestamps`

### Answers
- `id` (UUID), `question_id` (FK), `author_id` (FK), `content`, `timestamps`

### Attachments (Polimórfico)
- `id` (UUID), `attachable_id`, `attachable_type`, `filename`, `path`, `mime_type`, `size`, `timestamps`

## Tecnologias Utilizadas

- **Laravel 12:** Framework PHP
- **MySQL 8.0:** Banco de dados
- **Docker:** Containerização
- **Eloquent ORM:** Mapeamento objeto-relacional
- **UUID:** Identificadores únicos universais
- **Laravel Sanctum:** Autenticação via token
- **Policies:** Sistema de autorização do Laravel
- **Form Requests:** Validação estruturada
- **Services:** Camada de lógica de negócio
- **Resources:** Padronização de saída da API
- **PHPUnit:** Framework de testes automatizados
- **Factories:** Geração de dados para testes

## Documentação Adicional

- **[CURL-TESTES.md](CURL-TESTES.md)** - Testes da API realizados com curl