# Zievo API

API RESTful para gerenciamento de biblioteca com sistema de empréstimos de livros.

## Tecnologias

- PHP 8.3+
- Laravel 13
- Laravel Sanctum (autenticação)
- SQLite
- Node.js 18+

## Requisitos

Certifique-se de ter instalado:

- [PHP 8.3+](https://www.php.net/downloads)
- [Composer](https://getcomposer.org/download)
- [Node.js 18+](https://nodejs.org)
- SQLite

### Extensões PHP necessárias

- pdo
- pdo_sqlite
- mbstring
- openssl
- tokenizer
- xml
- ctype
- json
- bcmath

## Instalação

**1. Clone o repositório**
```bash
git clone https://github.com/KpK36/Zievo.git
cd Zievo
```

**2. Instale as dependências PHP**
```bash
composer install
```

**3. Copie o arquivo de ambiente**
```bash
# Linux/Mac
cp .env.example .env

# Windows
copy .env.example .env
```

**4. Gere a chave da aplicação**
```bash
php artisan key:generate
```

**5. Crie o banco de dados**
```bash
# Linux/Mac
touch database/database.sqlite

# Windows
type nul > database/database.sqlite
```

**6. Execute as migrations e seeders**
```bash
php artisan migrate --seed
```

**7. Instale as dependências Node**
```bash
npm install
```

## Executando o projeto

O projeto pode ser iniciado de duas formas:

### Opção 1 — Comando único (recomendado)
```bash
composer run dev
```

Esse comando inicia simultaneamente:
- Servidor Laravel (`php artisan serve`)
- Fila de jobs (`php artisan queue:listen`)
- Log em tempo real (`php artisan pail`)
- Servidor Vite (`npm run dev`)

### Opção 2 — Terminais separados
```bash
# Terminal 1 - Servidor
php artisan serve

# Terminal 2 - Fila
php artisan queue:work

# Terminal 3 - Agendamento
php artisan schedule:work

# Terminal 4 - Frontend
npm run dev
```

## Testes
```bash
composer run test
```

Ou diretamente:
```bash
php artisan test
```

## Endpoints

### Auth

| Método | Rota | Autenticação | Descrição |
|--------|------|--------------|-----------|
| POST | /api/register | Não | Registro de usuário |
| POST | /api/login | Não | Login |
| POST | /api/logout | Sim | Logout |

### Livros

| Método | Rota | Autenticação | Descrição |
|--------|------|--------------|-----------|
| GET | /api/books | Não | Listagem paginada |
| GET | /api/books/{id} | Não | Detalhes do livro |
| GET | /api/books/search?title= | Não | Busca por título |
| POST | /api/books | Sim | Cadastrar livro |
| PUT | /api/books/{id} | Sim | Atualizar livro |
| DELETE | /api/books/{id} | Sim | Deletar livro |

### Empréstimos

| Método | Rota | Autenticação | Descrição |
|--------|------|--------------|-----------|
| POST | /api/borrow/{id} | Sim | Pegar livro emprestado |
| POST | /api/return/{id} | Sim | Devolver livro |

## Regras de negócio

- A listagem e detalhes dos livros são públicos
- Apenas usuários autenticados podem cadastrar livros
- Apenas o dono do livro pode editá-lo ou deletá-lo
- Um livro não pode ser deletado enquanto estiver emprestado
- Um usuário pode ter no máximo **3 livros** emprestados simultaneamente
- O prazo de devolução é de **2 dias** após o empréstimo
- Um e-mail de aviso é enviado automaticamente **12 horas antes** do prazo de devolução

## Variáveis de ambiente

| Variável | Descrição | Padrão |
|----------|-----------|--------|
| APP_NAME | Nome da aplicação | Zievo |
| APP_ENV | Ambiente | local |
| APP_TIMEZONE | Fuso horário | America/Sao_Paulo |
| DB_CONNECTION | Driver do banco | sqlite |
| DB_DATABASE | Caminho do banco | database/database.sqlite |
| MAIL_MAILER | Driver de e-mail | log |
| MAIL_FROM_ADDRESS | E-mail remetente | noreply@zievo.com |
| QUEUE_CONNECTION | Driver da fila | database |
