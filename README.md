# Weather Project

Учебный проект на Laravel: веб‑приложение с погодным API, графиками, загрузкой PDF и панелью админа. Всё разворачивается через Docker Compose.

## Архитектура

- `nginx` — фронтовой сервер, раздаёт `laravel-app/public`, проксирует PHP в `php-fpm`.
- `php-fpm` — `php:8.2-fpm` + Laravel, OPcache, Redis ext.
- `postgres` — БД (инициализация `postgres/init/01-init.sql`).
- `redis` — сессии/кеш/предпочтения.

Основные пути:

| Путь | Назначение |
| --- | --- |
| `laravel-app/` | Все исходники Laravel (app, routes, public, lang). |
| `laravel-app/docker/php/opcache.ini` | Настройки OPcache. |
| `nginx/` | Конфигурация nginx. |
| `docs/api-spec.md` | Спецификация REST API. |

## Требования

- Docker 24+ и плагин `docker compose`.
- Свободные порты `80` и `443`.
- Файл `laravel-app/.env` (пример: `.env.example` внутри `laravel-app`). В docker-compose php использует именно его.
- Root `.env` — для общих значений (например, ADMIN_* если нужно).

Сессии/кеш — Redis; файлы и графики — `storage/app/public`.

## Быстрый старт

```bash
docker compose up --build
# после поднятия:
docker compose exec php php artisan migrate --seed
docker compose exec php php artisan storage:link
# по желанию: docker compose exec php php artisan config:cache route:cache view:cache
```

Полезные URL:

- Главная: `http://localhost/`
- Статистика с графиками: `http://localhost/stats`
- Админка: `http://localhost/admin`
- API: `http://localhost/api/weather`, `http://localhost/api/users`, `http://localhost/api/uploads`

## Управление

| Действие | Команда |
| --- | --- |
| Остановить контейнеры | `docker compose down` |
| Очистить данные Postgres и Redis | `docker compose down -v` |
| Пересобрать php-fpm после изменений зависимостей | `docker compose build php` |
| Просмотреть логи сервиса | `docker compose logs -f nginx` |
| Войти в php-контейнер | `docker compose exec php bash` |

## Частые сценарии

- **Загрузка PDF** — в `storage/app/public/uploads` (доступ через `/storage/uploads/...`).
- **Предпочтения** — в Redis (сессии) и в cookies (тема/язык/логин).
- **Графики** — PNG в `storage/app/public/charts`, API `/api/charts/{daily|weekly|monthly}`.
- **API тестирование** — см. `docs/api-spec.md`.

## Тестирование/Отладка

- Проверяйте, что Postgres и Redis поднялись (`docker compose ps`). Если `php` не стартует, смотрите логи `docker compose logs php`.
- При изменении конфигов Nginx перезапускайте только веб‑контейнер: `docker compose restart nginx`.
- Чтобы сбросить данные и заново применить `postgres/init/01-init.sql`, выполните `docker compose down -v && docker compose up --build`.
- Зависимости PHP уже в `vendor`; при обновлении: `docker compose exec php composer install --no-dev --optimize-autoloader`.
- Кэш для прод: `php artisan config:cache && php artisan route:cache && php artisan view:cache` (внутри контейнера).

## Полезные заметки

- Nginx → PHP-FPM (upstream `php_fpm` в `nginx/nginx.conf`).
- Контейнеры в сети `weather_network`: сервисы доступны по именам (`postgres`, `redis`, `php`).
- Если нужны другие порты — правьте `ports` в `docker-compose.yml`.
