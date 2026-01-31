# Forum - Sistema de Fórum Laravel

Sistema de fórum desenvolvido em Laravel.

## Como Rodar o Projeto

### Pré-requisitos
- Docker e Docker Compose
- PHP 8.1+ 

### Instalação

1. **Clone o repositório**
```bash
git clone <url-do-repositorio>
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

5. **Execute as migrations**
```bash
php artisan migrate
```

### Verificação
```bash
# Verificar se o Docker está rodando
docker-compose ps

# Testar conexão com o banco
php artisan migrate:status
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

### Estrutura de Models
- **Decisão:** Relacionamentos com chaves estrangeiras nomeadas
- **Motivo:** Clareza no código, facilita manutenção
- **Exemplo:** `author_id` ao invés de `user_id` genérico

## O que Optei por Não Implementar e Por Quê

----

## Pontos que Melhoraria com Mais Tempo

------

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