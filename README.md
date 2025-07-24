# Student-Attendance-Tool
This project is a challenge task to create a simple web application to register the student attendance to different classes.


composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

docker build -t sat-api .
docker run -p 8000:8000 sat-api

# Student Attendance Tool (SAT)

This repository contains a fullstack application designed to manage student attendance, built with:

- **Back-end**: Symfony 6 (API Platform)
- **Front-end**: Vue.js 3 (to be added)
- **Database**: PostgreSQL (via Docker)
- **Dev environment**: Docker

---

## 🐳 Getting Started with Docker

### Prerequisites

- [Docker](https://www.docker.com/) installed on your machine.
- [Docker Compose](https://docs.docker.com/compose/) (optional, to be added soon).

---

### 🧱 Backend (SAT-API) - Docker Setup

```bash
# 1. Go to the SAT-API folder
cd SAT-API

# 2. Build the Docker image
docker build -t sat-api .

# 3. Run the container
docker run -it --rm -p 8001:8000 sat-api
```

> ℹ️ You can now access the API at: [http://localhost:8001](http://localhost:8001)

---

## 🧪 API Health Check

To verify the API is working, open your browser or use a tool like Postman or `curl`:

```http
GET http://localhost:8001
GET http://localhost:8001/api
```

If successful, you’ll see a JSON response or the default API Platform landing page.

---

## 🗃️ Database Setup (PostgreSQL)

> ⚠️ The PostgreSQL service will be containerized in the future. These instructions are to prepare that setup.

### `.env` Database Configuration (example)

```env
DATABASE_URL="pgsql://sat_user:sat_pass@db:5432/sat_database"
```

This means:

- **User**: `sat_user`
- **Password**: `sat_pass`
- **Host**: `db` (container name, set in Docker Compose)
- **Port**: `5432`
- **Database**: `sat_database`

You can change these credentials in the `.env` and later in the Docker Compose file.

---

### Database Commands (after container is up)

```bash
# Create the database
php bin/console doctrine:database:create

# Run pending migrations
php bin/console doctrine:migrations:migrate
```

---

## 🧰 Useful Composer and Symfony Commands

```bash
composer install                          # Install dependencies
composer dump-autoload                    # Update the autoloader
php bin/console                           # List Symfony CLI commands
php bin/console make:entity               # Create a new Entity class
php bin/console doctrine:migrations:diff  # Generate migration from schema
php bin/console doctrine:migrations:migrate # Apply migrations
```

---

## 🖼️ Frontend (Vue.js 3)

> ⚠️ Not yet implemented. It will be added soon inside a folder called `SAT-Frontend/`.

### Planned stack:

- Vue 3 with Composition API
- Vue Router
- Pinia (or Vuex)
- Axios (for calling Symfony API)

Development port (planned): `http://localhost:8000`

Once configured, the Vue app will send requests to the Symfony API at `http://localhost:8001`.

Make sure CORS is configured to allow that in the Symfony backend (already handled via `nelmio/cors-bundle`).

---

## 🔌 CORS Configuration

The file `config/packages/nelmio_cors.yaml` includes:

```yaml
nelmio_cors:
    defaults:
        origin_regex: true
        allow_origin: ['%env(CORS_ALLOW_ORIGIN)%']
        allow_methods: ['GET', 'OPTIONS', 'POST', 'PUT', 'PATCH', 'DELETE']
        allow_headers: ['Content-Type', 'Authorization']
        expose_headers: ['Link']
        max_age: 3600
    paths:
        '^/': null
```

And in your `.env` file:

```env
CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$'
```

---

## 🐘 PostgreSQL Volumes in Docker

> 🔒 Where is the database stored?

When using Docker with volumes, PostgreSQL data is usually stored inside a named volume, not in your project folder.

Example (to be configured in `docker-compose.yml`):

```yaml
volumes:
  pgdata:
```

To free up space or reset your database, run:

```bash
docker volume rm yourproject_pgdata
```

This is much cleaner than installing PostgreSQL locally.

---

## 📦 Project Structure

```
student-attendance-tool/
│
├── SAT-API/              # Symfony 6 REST API (this project)
│   ├── config/
│   ├── src/
│   ├── public/
│   ├── migrations/
│   └── Dockerfile
│
├── SAT-Frontend/         # Vue.js frontend (to be created)
│
└── docker-compose.yml    # Docker orchestration (to be added)
```

---

## 🛠️ What’s Next?

- [ ] Create the `SAT-Frontend/` Vue.js application.
- [ ] Set up `docker-compose.yml` with:
  - Backend container (`sat-api`)
  - Frontend container (`sat-frontend`)
  - PostgreSQL container (`postgres`)
- [ ] Auto-run migrations on container startup.
- [ ] Add Makefile or `scripts/` for convenience.

---

## 📝 License

This project was developed for an internship evaluation. Not intended for production use.