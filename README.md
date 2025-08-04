# Student Attendance Tool (SAT)

Este projeto é uma aplicação web fullstack para controle de presença de alunos, construída com:

- **Back-end**: Symfony 6 (API Platform) (em desenvolvimento)
- **Front-end**: Vue.js 3 (em desenvolvimento)
- **Banco de Dados**: PostgreSQL (via Docker)
- **Ambiente de Desenvolvimento**: Docker + Docker Compose

---

## 📦 Pré-requisitos

Antes de começar, instale:

- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)

---

## 🚀 Subindo o Projeto com Docker Compose

### 1. Clone o repositório

```bash
git clone https://github.com/Sacullla10/Student-Attendance-Tool.git
cd student-attendance-tool
```

### 2. Suba os containers com Docker Compose

```bash
docker-compose up -d --build
```

> Isso irá subir:
> - O container do backend (Symfony API)
> - O container do banco PostgreSQL

### 3. Acesse o container do Symfony (backend)

```bash
docker exec -it sat-api-1 bash
```

### 4. Instale as dependências e configure o banco

Dentro do container do Symfony, execute:

```bash
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

> Essas instruções vão criar o banco, aplicar as migrations e popular com dados de exemplo.

---

## 🌐 Acessando a Aplicação

- API Symfony: [http://localhost:8000](http://localhost:8000)
- Painel do API Platform: [http://localhost:8000/api](http://localhost:8000/api)

---

## 🗂️ Estrutura do Projeto

```
student-attendance-tool/
│
├── SAT-API/              # Symfony 6 REST API
│   ├── config/
│   ├── src/
│   ├── public/
│   ├── migrations/
│   └── Dockerfile
│
├── SAT-Frontend/         # Vue.js frontend 
│
└── docker-compose.yml    # Orquestração Docker
```

---

## ⚙️ Configuração do Banco de Dados

Seu arquivo `.env` já deve conter:

```env
DATABASE_URL="pgsql://sat_user:sat_pass@db:5432/sat_database"
```

> Esses valores são definidos também no `docker-compose.yml`.

---

## 🔧 Comandos Úteis Symfony

```bash
composer install                          # Instala dependências
php bin/console doctrine:database:create  # Cria banco
php bin/console doctrine:migrations:migrate  # Aplica migrations
php bin/console doctrine:fixtures:load    # Carrega dados fictícios
```

---

## 🌍 CORS

O backend já está configurado para aceitar requisições do frontend (localhost). Confira:

Arquivo: `config/packages/nelmio_cors.yaml`

```yaml
allow_origin: ['%env(CORS_ALLOW_ORIGIN)%']
```

No `.env`:

```env
CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$'
```

---

## 🧪 Testar a API

```bash
curl http://localhost:8000/api
```

Você deve receber uma resposta JSON da API Platform.

---

## 🖼️ Frontend (Vue.js 3)

O frontend está implementado e funcional.

- **Tecnologias utilizadas**:
  - Vue 3 + Composition API
  - Vue Router
  - Pinia
  - Axios

A aplicação frontend estará acessível em: [http://localhost:8080](http://localhost:8080)

Ela se comunica com a API Symfony disponível em: [http://localhost:8000](http://localhost:8000)

Certifique-se de que ambos os containers (frontend e backend) estão em execução via Docker Compose.


---

## 🐘 Dados do PostgreSQL

O volume do banco está definido no `docker-compose.yml`:

```yaml
volumes:
  pgdata:
```

Para resetar o banco:

```bash
docker volume rm student-attendance-tool_pgdata
```

---

## 📝 Licença

Projeto desenvolvido para fins avaliativos. Não recomendado para uso em produção.
