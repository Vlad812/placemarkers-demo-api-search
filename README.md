> Этот сервис: `api-placemarkers-search` является частью приложения [placemarkers-demo-workstation](https://github.com/Vlad812/placemarkers-demo-workstation).

# API Documentation: Placemarkers Search

**Стек технологий:**
- PHP 8.5
- Symfony 8
- RoadRunner
- PostgreSQL
- Doctrine DBAL

Сервис чтения (CQRS Read Model) для гео-меток, тегов и справочника типов. Отвечает за поиск по радиусу, получение метки по ID, недавние метки и списки тегов/типов. Все запросы выполняются только на чтение из **Replica** PostgreSQL через **Fetchers** — классы с сырыми SQL-запросами на базе **Doctrine DBAL** (без накладных расходов ORM).

Все бизнес-эндпоинты доступны под префиксом `/search`.

**Авторизация (общее):** Bearer Token (JWT). Пользователь видит только свои метки и теги.

**Формат ошибок (общий):**

```json
{
  "errors": [
    {
      "message": "Текст ошибки"
    }
  ]
}
```

---

## Поиск меток по области (Search Placemarkers)

Ищет метки текущего пользователя в радиусе от заданной точки. Опционально фильтрует по типам и тегам.

**URL:** `/search`  
**Метод:** `GET`  
**Авторизация:** Требуется (Bearer Token)

### Request (Запрос)

#### Заголовки (Headers)
* `Authorization: Bearer <token>`

#### Query Parameters

| Параметр | Тип | Обязательное | Описание | Пример |
| :--- | :--- | :---: | :--- | :--- |
| `lat` | `number` | **Да** | Широта центра поиска (−90…90). | `55.755826` |
| `lon` | `number` | **Да** | Долгота центра поиска (−180…180). | `37.617299` |
| `radius` | `number` | **Да** | Радиус поиска в метрах (> 0). | `1500` |
| `tags` | `string` / `array<string>` | Нет | Фильтр по тегам (логика **AND** — метка должна содержать все указанные теги). | `["coffee", "wifi"]` |
| `types` | `string` / `array<string>` | Нет | Фильтр по типам (логика **OR**). | `["cafe", "restaurant"]` |

#### Пример запроса

```http
GET /search?lat=55.755826&lon=37.617299&radius=1500&tags=coffee&types=cafe HTTP/1.1
Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
```

### Responses (Ответы)

#### 🟢 200 OK — Успешный поиск

Возвращает JSON-массив меток (может быть пустым `[]`). Сортировка: по `created_at DESC`.

```json
[
  {
    "id": "123e4567-e89b-12d3-a456-426614174000",
    "name": "Любимая кофейня",
    "lat": 55.755826,
    "lon": 37.617299,
    "type_id": "cafe",
    "tags": ["coffee", "wifi"],
    "description": "Отличный кофе"
  }
]
```

#### 🔴 401 Unauthorized — Ошибка авторизации

Возвращается, если пользователь не авторизован (отсутствует или недействителен токен).

#### 🔴 422 Unprocessable Entity — Ошибка валидации данных

Возвращается, если пропущены обязательные параметры или переданы невалидные значения.

```json
{
  "errors": [
    {
      "message": "Missing required parameter: lat"
    }
  ]
}
```

```json
{
  "errors": [
    {
      "message": "Search radius must be greater than zero."
    }
  ]
}
```

```json
{
  "errors": [
    {
      "message": "Invalid latitude: 91.000000. Must be between -90 and 90."
    }
  ]
}
```

#### 🔴 500 Internal Server Error — Внутренняя ошибка

```json
{
  "errors": [
    {
      "message": "Internal server error."
    }
  ]
}
```

---

## Получение метки по ID (Get Placemarker)

Возвращает одну метку текущего пользователя по UUID.

**URL:** `/search/placemarkers/{id}`  
**Метод:** `GET`  
**Авторизация:** Требуется (Bearer Token)

### Request (Запрос)

#### Заголовки (Headers)
* `Authorization: Bearer <token>`

#### Параметры пути (Path Parameters)

| Параметр | Тип | Обязательное | Описание | Пример |
| :--- | :--- | :---: | :--- | :--- |
| `id` | `uuid` | **Да** | Идентификатор метки. | `"123e4567-e89b-12d3-a456-426614174000"` |

#### Пример запроса

```http
GET /search/placemarkers/123e4567-e89b-12d3-a456-426614174000 HTTP/1.1
Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
```

### Responses (Ответы)

#### 🟢 200 OK — Метка найдена

```json
{
  "id": "123e4567-e89b-12d3-a456-426614174000",
  "name": "Любимая кофейня",
  "lat": 55.755826,
  "lon": 37.617299,
  "type_id": "cafe",
  "tags": ["coffee", "wifi"],
  "description": "Отличный кофе",
  "created_at": "2026-07-13 10:30:00"
}
```

#### 🔴 401 Unauthorized — Ошибка авторизации

Возвращается, если пользователь не авторизован (отсутствует или недействителен токен).

#### 🔴 404 Not Found — Метка не найдена

Метка не существует или принадлежит другому пользователю.

```json
{
  "errors": [
    {
      "message": "Placemarker with id \"123e4567-e89b-12d3-a456-426614174000\" was not found."
    }
  ]
}
```

#### 🔴 422 Unprocessable Entity — Ошибка валидации данных

```json
{
  "errors": [
    {
      "message": "Missing required parameter: id"
    }
  ]
}
```

```json
{
  "errors": [
    {
      "message": "Value \"invalid-id\" is not a valid UUID."
    }
  ]
}
```

---

## Недавние метки (Get Recent Placemarkers)

Возвращает последние метки текущего пользователя.

**URL:** `/search/recent`  
**Метод:** `GET`  
**Авторизация:** Требуется (Bearer Token)

### Request (Запрос)

#### Заголовки (Headers)
* `Authorization: Bearer <token>`

#### Query Parameters

| Параметр | Тип | Обязательное | Описание | Пример |
| :--- | :--- | :---: | :--- | :--- |
| `limit` | `integer` | Нет | Количество записей. По умолчанию `10`. Должен быть > 0. | `5` |

#### Пример запроса

```http
GET /search/recent?limit=5 HTTP/1.1
Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
```

### Responses (Ответы)

#### 🟢 200 OK — Успешный ответ

JSON-массив меток. Сортировка: `created_at DESC`.

```json
[
  {
    "id": "123e4567-e89b-12d3-a456-426614174000",
    "name": "Любимая кофейня",
    "lat": 55.755826,
    "lon": 37.617299,
    "type_id": "cafe",
    "tags": ["coffee"],
    "description": null
  }
]
```

#### 🔴 401 Unauthorized — Ошибка авторизации

Возвращается, если пользователь не авторизован (отсутствует или недействителен токен).

#### 🔴 422 Unprocessable Entity — Ошибка валидации данных

```json
{
  "errors": [
    {
      "message": "Parameter limit must be numeric"
    }
  ]
}
```

```json
{
  "errors": [
    {
      "message": "Parameter limit must be greater than 0"
    }
  ]
}
```

---

## Список тегов пользователя (Get User Tags)

Возвращает все теги, созданные текущим пользователем.

**URL:** `/search/tags`  
**Метод:** `GET`  
**Авторизация:** Требуется (Bearer Token)

### Request (Запрос)

#### Заголовки (Headers)
* `Authorization: Bearer <token>`

#### Пример запроса

```http
GET /search/tags HTTP/1.1
Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
```

### Responses (Ответы)

#### 🟢 200 OK — Успешный ответ

JSON-массив тегов. Сортировка: `created_at DESC`.

```json
[
  {
    "id": "a1b2c3d4-e5f6-7890-abcd-ef1234567890",
    "name": "coffee",
    "description": "Места с хорошим кофе"
  }
]
```

#### 🔴 401 Unauthorized — Ошибка авторизации

Возвращается, если пользователь не авторизован (отсутствует или недействителен токен).

---

## Список типов меток (Get Placemarker Types)

Возвращает справочник типов меток (глобальный, не привязан к пользователю).

**URL:** `/search/types`  
**Метод:** `GET`  
**Авторизация:** Требуется (Bearer Token)

### Request (Запрос)

#### Заголовки (Headers)
* `Authorization: Bearer <token>`

#### Пример запроса

```http
GET /search/types HTTP/1.1
Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
```

### Responses (Ответы)

#### 🟢 200 OK — Успешный ответ

JSON-массив типов. Сортировка: `name ASC`.

```json
[
  {
    "slug": "cafe",
    "name": "кафе"
  },
  {
    "slug": "default",
    "name": "default"
  }
]
```

#### 🔴 401 Unauthorized — Ошибка авторизации

Возвращается, если пользователь не авторизован (отсутствует или недействителен токен).

---

## Проверка состояния сервиса (Health Check)

Проверяет доступность сервиса. Используется для мониторинга и оркестрации (Docker, Kubernetes).

**URL:** `/health`  
**Метод:** `GET`  
**Авторизация:** Не требуется

### Request (Запрос)

#### Пример запроса

```http
GET /health HTTP/1.1
```

### Responses (Ответы)

#### 🟢 200 OK — Сервис доступен

```json
{
  "status": "ok"
}
```
