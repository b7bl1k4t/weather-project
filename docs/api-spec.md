# API проекта (Laravel)

Бекенд на Laravel, база PostgreSQL. Базовый URL внутри docker-compose:

```
http://localhost/api/<endpoint>
```

Все запросы/ответы — JSON (`Content-Type: application/json; charset=utf-8`). Ошибки:

```json
{ "error": "Сообщение об ошибке" }
```

## Сущности
1) `weather_data` — погодные данные.  
2) `users` — администраторы (пароли хранятся как bcrypt).  
3) `uploads` — загруженные PDF (метаданные в БД, файлы в `storage/app/public/uploads`).

## Weather
| Метод | URL | Описание |
| --- | --- | --- |
| GET | `/api/weather?limit=10` | Список (по умолчанию 20, макс. 100). |
| GET | `/api/weather/{id}` | Получить запись. |
| POST | `/api/weather` | Создать запись. |
| PUT/PATCH | `/api/weather/{id}` | Полное обновление. |
| DELETE | `/api/weather/{id}` | Удалить запись. |

### Пример создания
```
POST /api/weather
```
```json
{
  "temperature": 23.4,
  "humidity": 58,
  "pressure": 1011,
  "wind_speed": 4.2,
  "description": "Ясно",
  "icon": "☀️"
}
```
Успех → `201 Created` + JSON созданной записи. Все поля обязательны. Диапазоны: температура -99.99..99.99, влажность 0..100, давление >0 (до 2000), ветер 0..99.99, icon max 10 символов.

### Ошибки
- `404` — запись не найдена.  
- `422` — валидация.  
- `500` — системная ошибка.

## Users
| Метод | URL | Описание |
| --- | --- | --- |
| GET | `/api/users?limit=10` | Список пользователей (без паролей). |
| GET | `/api/users/{id}` | Данные пользователя. |
| POST | `/api/users` | Создать пользователя. |
| PUT/PATCH | `/api/users/{id}` | Обновить. Передавайте только изменяемые поля. |
| DELETE | `/api/users/{id}` | Удалить. |

### Пример создания
```
POST /api/users
```
```json
{
  "username": "editor",
  "password": "secret123",
  "email": "editor@example.com"
}
```
Успех → `201 Created` с `id`, `username`, `email`, `created_at`.

### Правила
- `username` 3..50, уникален.  
- `password` ≥6, хэшируется bcrypt.  
- `email` опционален, но валиден.

### Ошибки
- `404` — не найден.  
- `409` — username уже существует.  
- `422` — валидация.  
- `500` — системная ошибка.

## Uploads (PDF)
| Метод | URL | Описание |
| --- | --- | --- |
| GET | `/api/uploads` | Список загруженных файлов (id, имена, mime, размер, автор, created_at). |
| POST | `/api/uploads` | Загрузка PDF (`file`, макс. 5 МБ; опц. `uploaded_by`). |
| DELETE | `/api/uploads/{id}` | Удалить файл и метаданные. |
| GET | `/api/uploads/{id}/download` | Скачать PDF. |

### Ошибки
- `404` — файл/метаданные не найдены.  
- `422` — неверный тип/размер.  
- `500` — системная ошибка.

## Charts
| Метод | URL | Описание |
| --- | --- | --- |
| GET | `/api/charts/daily` | PNG график за сутки. |
| GET | `/api/charts/weekly` | PNG график за неделю. |
| GET | `/api/charts/monthly` | PNG график за месяц. |

## Нюансы
- Аутентификация не требуется (требование учебного задания).  
- Лимит списка по умолчанию 20, макс. 100.  
- Формат времени: `Y-m-d H:i:s`.  
- Коды успеха: `200 OK`, `201 Created`, `204 No Content` (delete).  
- Ошибки: `4xx/5xx` с полем `error`.
