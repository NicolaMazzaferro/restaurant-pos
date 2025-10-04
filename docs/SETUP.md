# SETUP — Gestionale Pizzeria (Cassa) - MVP

Questa guida ti porta **da zero** al progetto avviato in Docker.

## Requisiti
- Docker Desktop (Windows con WSL2 attivo; macOS; Linux)

## 1) Clona e configura l'ambiente
```
git clone https://github.com/NicolaMazzaferro/restaurant-pos.git
cd gestionale-pizzeria-cassa
cp .env.example .env
```

## 2) Docker stack
```
docker compose build
docker compose up -d
```

## 3) Dipendenze e chiave app
```
docker compose exec app composer install
docker compose exec app php artisan key:generate
```

## 4) Migrazioni & seed
```
docker compose exec app php artisan migrate --seed
```

## 5) Accesso API
```
Base URL: http://localhost:8080

Login: POST /api/auth/login con admin@example.com / password

Autorizzazione: Authorization: Bearer <token>
```

## 6) Script utili
```
docker compose exec app php artisan migrate:fresh --seed
docker compose exec app php artisan test
docker compose logs -f app
```
