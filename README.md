## Weather Project · Laravel edition

Учебный проект переведён с набором разрозненных PHP-скриптов на полноценный Laravel 12 стек с простым паттерном Service + Repository.

### Что внутри

- `laravel/` — основное приложение (контроллеры, сервисы, репозитории, DTO, миграции, сиды, Blade‑шаблон панели погоды и REST API).
- `docker-compose.yml` + `Dockerfile.php` — контейнеры nginx + php-fpm + (опционально) queue/scheduler + Postgres.
- `dynamic/`, `static/` — прежние материалы оставлены для справки и плавного переезда.

### Быстрый старт

```bash
# Собрать и поднять базу
docker compose up -d postgres

# Применить миграции/сиды (пересоздаст таблицы)
docker compose run --rm php php artisan migrate --seed

# Запустить весь стек (nginx + php + очередь + шедулер)
docker compose --profile workers up -d
```

> ⚠️ Если раньше использовался старый стенд, очистите volume `postgres_data`
> (`docker compose down -v`) либо удалите таблицы вручную — иначе миграции
> наткнутся на уже существующие объекты.

### Основные команды

- `docker compose run --rm php php artisan key:generate` — переинициализация ключа.
- `docker compose run --rm php php artisan test` — тесты (использует sqlite in‑memory, требует расширение `pdo_sqlite`, уже в образе).
- `docker compose run --rm php php artisan queue:work` и `schedule:work` — локальный запуск воркеров, если не хотите поднимать профили `workers`.

### Архитектура приложения

- `app/Services/WeatherService` инкапсулирует бизнес-логику.
- `app/Repositories/*` — интерфейс + Eloquent-реализация; биндинг настраивается в `app/Providers/RepositoryServiceProvider`.
- `app/DTO/WeatherData` отделяет транспортный слой от хранения.
- Контроллеры `App\Http\Controllers\WeatherController` (Blade) и `App\Http\Controllers\Api\WeatherController` (REST) используют сервис.
- API доступно по `GET /api/weather/current`, `GET /api/weather/history`, `POST /api/weather`.

### Дальнейшие шаги

1. Подключить авторизацию Laravel Breeze/Jetstream, если нужна полноценная админка.
2. Вынести работу с внешними погодными провайдерами в отдельные сервисы/драйверы.
3. Настроить CI, чтобы автоматически выполнять `composer test` + статический анализ.
