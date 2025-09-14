# Task Scheduler 

- **Backend:** Laravel (PHP 8.2) + MySQL + Sanctum (Bearer tokens)
- **Frontend:** Vue 3 + TypeScript (built SPA)
- **Infra:** Docker (nginx, php-fpm, mysql)

## Structure

```
/task-scheduler
├─ backend/ # Laravel API
├─ frontend/ # Vue 3 + TS (build → frontend/dist)
├─ docker/ # nginx.conf, Dockerfiles
└─ docker-compose.yml
```

## Start (setup)

```bash
# 0) Start containers
docker compose up -d --build

# 1) Backend setup
docker compose exec app composer install
docker compose exec app cp .env.example .env || true
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan storage:link || true

# 2) Frontend (build & dev)
# Build production SPA → frontend/dist
cd frontend && npm i && npm run build 

# Preview the built SPA locally (uses dist/)
cd frontend && npm run preview

# Local development (hot reload, Vite dev server)
# Vite runs at http://localhost:5173 by default
cd frontend && npm i && npm run dev
```
## Typical dev flow
Run backend via Docker: 
```docker compose up -d```

Run frontend locally with hot reload: 
```cd frontend && npm run dev ```

Access app at http://localhost:5173/

## Frontend env (Vite) 
!!!!!! ONLY FOR LOCAL DEVELOPMENT

frontend/.env.development
VITE_API_BASE_URL=http://localhost:8080

## Access and links

* **Frontend (SPA):** [http://localhost:8080/](http://localhost:8080/)
* **API base:** [http://localhost:8080/api/v1](http://localhost:8080/api/v1)
* **OpenAPI (file):** backend/openapi.yaml

## Seeded Admin

* **login:** admin@admin.com
* **password:** admin

## More commands

```aiignore
# Clean cache routes/config
docker compose exec app sh -lc 'php artisan route:clear && php artisan config:clear && php artisan cache:clear'
```
### Best Practice (Repos)

For growing teams, consider two repositories:

task-scheduler-frontend (Vue 3 + TS): lint/test/build, static deploy

task-scheduler-backend (Laravel): tests/migrations, containerized deploy

Benefits: cleaner CI/CD, separate versioning/releases, clearer ownership.
Mono-repo is fine; if you keep it, set up per-folder CI jobs and CODEOWNERS.