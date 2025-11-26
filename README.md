# Weather Project

Небольшой учебный стек для практической работы №4: статический лендинг на HTML/CSS + динамическое PHP‑приложение с API, панелью администратора и хранилищем загрузок. Всё разворачивается через Docker Compose.

## Архитектура

- `nginx` — единственный фронтовой веб‑сервер, раздаёт статику и проксирует PHP‑запросы в `php-fpm`.
- `php-fpm` — контейнер на `php:8.1-fpm`, исполняет код из каталога `dynamic/`.
- `postgres` — хранит данные погоды и пользователей (инициализация в `postgres/init/01-init.sql`).
- `redis` — используется PHP частью для сохранения пользовательских настроек (тема/логин/язык).
По
Основные директории:

| Путь | Назначение |
| --- | --- |
| `static/` | Лендинг, стили и JS для публичной страницы. |
| `dynamic/` | Весь PHP (главная с формой, API `/api/*.php`, загрузка PDF в `dynamic/uploads`). |
| `dynamic/admin/` | Админка (`index.php` + `login.php`). |
| `docs/api-spec.md` | Подробная спецификация REST API. |
| `nginx/` | Конфигурация reverse‑proxy (`nginx.conf`, `conf.d/weather.conf`). |

## Требования

- Docker 24+ и плагин `docker compose`.
- Свободные порты `80` и `443`.
- Файл `.env` (есть пример в репозитории). Доступны переменные:

```
ADMIN_USERNAME=admin
ADMIN_PASSWORD=password
REDIS_HOST=redis
REDIS_PORT=6379
```

`ADMIN_*` также выступают fallback-учёткой для входа в админку.

## Быстрый старт

```bash
docker compose up --build
```

Первый запуск:

1. Compose соберёт `php-fpm` образ из `Dockerfile.php-fpm` и прогонит миграции Postgres из `postgres/init/01-init.sql`.
2. После сообщения `nginx entered RUNNING state` приложение доступно на `http://localhost`.

Полезные URL:

- Лендинг: `http://localhost/`
- Динамическая страница: `http://localhost/index.php`
- Админка: `http://localhost/admin/`  
  Логин/пароль — значения из `.env` (`admin` / `password` по умолчанию).
- Статистика с графиками: `http://localhost/stats.php`.
- API: `http://localhost/api/weather.php`, `http://localhost/api/users.php` (подробности в `docs/api-spec.md`).

## Управление

| Действие | Команда |
| --- | --- |
| Остановить контейнеры | `docker compose down` |
| Очистить данные Postgres и Redis | `docker compose down -v` |
| Пересобрать php-fpm после изменений зависимостей | `docker compose build php` |
| Просмотреть логи сервиса | `docker compose logs -f nginx` |

## Частые сценарии

- **Загрузка PDF** — форма на динамической странице складывает файлы в `dynamic/uploads`. Папка проброшена в контейнер, поэтому файлы сохраняются на хосте.
- **Настройки пользователя** — сохраняются в Redis (ключ `user:preferences:{session}`) и дублируются в cookie на 30 дней.
- **API тестирование** — можно использовать Postman/HTTPie; примеры тел и кодов ответов уже описаны в `docs/api-spec.md`.

## Тестирование/Отладка

- Проверяйте, что Postgres и Redis поднялись (`docker compose ps`). Если `php` не стартует, смотрите логи `docker compose logs php`.
- При изменении конфигов Nginx перезапускайте только веб‑контейнер: `docker compose restart nginx`.
- Чтобы сбросить данные и заново применить `postgres/init/01-init.sql`, выполните `docker compose down -v && docker compose up --build`.
- PHP-зависимости (Faker, JpGraph) ставятся через Composer: `cd dynamic && composer install` (vendor каталог монтируется в `php-fpm`).

## Полезные заметки

- Никакого Apache больше нет: Nginx напрямую общается с PHP-FPM (upstream `php_fpm` в `nginx/nginx.conf`).
- Контейнеры используют общий bridge‑нетворк `weather_network`, поэтому хосты внутри стека доступны по именам сервисов (`postgres`, `redis`, `php`).
- Если нужно поменять публичные порты, правьте `ports` в `docker-compose.yml` (сейчас проброшены 80/443 → 80/443 контейнера).
