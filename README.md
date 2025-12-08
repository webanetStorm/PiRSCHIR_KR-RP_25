
# Quelyd — Платформа пользовательских квестов

Quelyd — это веб-платформа, позволяющая пользователям создавать, выполнять и управлять собственными квестами. Проект реализован в виде серверной части интернет-ресурса с поддержкой REST API, аутентификации, модерации контента и ролевой системы доступа.

## Функциональные возможности

### Для пользователей
- **Регистрация и аутентификация** (email/пароль)
- **Создание квестов** с указанием типа (индивидуальный, коллективный, с лимитом времени), награды, описания и дедлайна
- **Просмотр списка доступных квестов**
- **Просмотр и редактирование собственных квестов**
- **Публикация квеста** (после создания он находится в черновиках)
- **Удаление собственных квестов**

### Для администраторов
- **Модерация квестов**: просмотр списка неодобренных квестов, их одобрение или удаление

### Для разработчиков (через API)
- **Полный доступ к функционалу сайта** через REST API с аутентификацией по токену
- **Создание, просмотр, редактирование, публикация и удаление квестов**
- **Аутентификация (login/register)** через API

## Технологии

- **Язык программирования**: PHP 8.2
- **База данных**: MySQL 8.0
- **Библиотека работы с БД**: [krugozor/database](https://github.com/Vasiliy-Makogon/Database)
- **Архитектура**: самописный MVC-фреймворк (Model-View-Controller)
- **Контейнеризация**: Docker (Docker Compose)
- **Тестирование**: PHPUnit

## Установка и запуск

### Шаги

1. Клонируйте репозиторий:
   ```bash
   git clone https://github.com/webanetStorm/PiRSCHIR_KR-RP_25.git
   cd PiRSCHIR_KR-RP_25
   ```

2. Установите зависимости:
   ```bash
   composer install
   ```

3. Запустите проект с помощью Docker Compose:
   ```bash
   docker-compose up --build
   ```

4. Откройте в браузере: `http://localhost:8080`

### Учетная запись администратора
- **Email**: `admin@quelyd.local`
- **Пароль**: `password`

## Использование сайта

### Регистрация и вход
1. Перейдите на страницу `/auth/register` для регистрации.
2. Или на `/auth/login` для входа, если уже зарегистрированы.

### Создание квеста
1. После входа в систему перейдите на `/quests/create`.
2. Заполните форму и нажмите "Создать квест". Квест появится в ваших черновиках.
3. Чтобы опубликовать квест, перейдите в "Мои квесты" (`/quests/my`) и нажмите "Опубликовать".

### Модерация (для администраторов)
1. Войдите как администратор.
2. Перейдите в раздел "Модерация" в меню.
3. Здесь вы увидите список квестов, ожидающих одобрения. Вы можете их одобрить или удалить.

## Использование API

### Аутентификация
Большинство API-методов требуют аутентификации. Для этого:
1. Выполните `POST /api/auth/login` или `POST /api/auth/register`.
2. Получите токен из ответа.
3. Передавайте токен в заголовке `Authorization: Bearer <token>` для защищенных запросов.

### Примеры API-запросов

#### Зарегистрировать новый аккаунт
Пример запроса:
```http
POST http://localhost:8080/api/auth/register
Content-Type: application/json

{
    "email": "newuser@example.com",
    "password": "password123",
    "name": "Новый Пользователь"
}
```
Пример ответа:
```http
{
    "success": true,
    "message": "Успешная регистрация",
    "data": {
        "token": "*******************************************",
        "user": {
            "id": 1,
            "email": "newuser@example.com",
            "name": "Новый Пользователь",
            "role": "user"
        }
    }
}
```

#### Войти в систему
Пример запроса:
```http
POST http://localhost:8080/api/auth/login
Content-Type: application/json

{
    "email": "admin@quelyd.local",
    "password": "password"
}
```
Пример ответа:
```http
{
    "success": true,
    "message": "Успешная авторизация",
    "data": {
        "token": "***************************************",
        "user": {
            "id": 1,
            "email": "admin@quelyd.local",
            "name": "Матвей Блантер",
            "role": "admin"
        }
    }
}
```

#### Получить профиль текущего пользователя
Пример запроса:
```http
GET http://localhost:8080/api/auth/profile
Authorization: Bearer ********************
```
Пример ответа:
```http
{
    "success": true,
    "data": {
        "id": 1,
        "email": "user@example.com",
        "name": "Test User",
        "role": "user"
    }
}
```

#### Получить список одобренных квестов
Пример запроса:
```http
GET http://localhost:8080/api/quests
Authorization: Bearer *********************
```
Пример ответа:
```http
{
    "success": true,
    "data": [
        {
            "id": 1,
            "user_id": 1,
            "title": "Поиск древнего артефакта",
            "description": "Найти древний артефакт в руинах старого храма",
            "type": "collective",
            "reward": 200,
            "min_participants": 3,
            "deadline": "2025-12-31 23:59:59",
            "status": "active",
            "is_approved": true,
            "created_at": 1733154000,
            "updated_at": 1733154000
        }
    ]
}
```

#### Получить квест по ID
Пример запроса:
```http
GET http://localhost:8080/api/quests/1
Authorization: Bearer ********************
```
Пример ответа:
```http
{
    "success": true,
    "data": {
        "id": 1,
        "user_id": 1,
        "title": "Поиск древнего артефакта",
        "description": "Найти древний артефакт в руинах старого храма",
        "type": "collective",
        "reward": 200,
        "min_participants": 3,
        "deadline": "2025-12-31 23:59:59",
        "status": "active",
        "is_approved": true,
        "created_at": 1733154000,
        "updated_at": 1733154000,
        "is_owner": true
    }
}
```

#### Получить квесты текущего пользователя
Пример запроса:
```http
GET http://localhost:8080/api/quests/my
Authorization: Bearer ***************************
```
Пример ответа:
```http
{
    "success": true,
    "data": [
        {
            "id": 1,
            "user_id": 1,
            "title": "Мой первый квест",
            "description": "Этот квест ещё в черновиках",
            "type": "individual",
            "reward": 30,
            "min_participants": null,
            "deadline": null,
            "status": "draft",
            "is_approved": false,
            "created_at": 1733154000,
            "updated_at": 1733154000
        }
    ]
}
```

#### Создать новый квест
Пример запроса:
```http
POST http://localhost:8080/api/quests/create
Content-Type: application/json
Authorization: Bearer ******************

{
    "title": "Новый квест API",
    "description": "Квест созданный через API",
    "type": "timed",
    "reward": 75,
    "min_participants": 2,
    "deadline": "2025-12-25 18:00:00"
}
```
Пример ответа:
```http
{
    "success": true,
    "message": "Квест успешно создан",
    "data": {
        "id": 3,
        "user_id": 1,
        "title": "Новый квест API",
        "description": "Квест созданный через API",
        "type": "timed",
        "reward": 75,
        "min_participants": 2,
        "deadline": "2025-12-25 18:00:00",
        "status": "draft",
        "is_approved": false,
        "created_at": 1733154200,
        "updated_at": 1733154200
    }
}
```

#### Обновить квест
Пример запроса:
```http
PATCH http://localhost:8080/api/quests/3/update
Content-Type: application/json
Authorization: Bearer ******************

{
    "title": "Обновлённый квест API",
    "description": "Обновлённое описание",
    "reward": 100
}
```
Пример ответа:
```http
{
    "success": true,
    "message": "Квест успешно обновлен",
    "data": {
        "id": 3,
        "user_id": 1,
        "title": "Обновлённый квест API",
        "description": "Обновлённое описание",
        "type": "timed",
        "reward": 100,
        "min_participants": 2,
        "deadline": "2025-12-25 18:00:00",
        "status": "draft",
        "is_approved": false,
        "created_at": 1733154200,
        "updated_at": 1733154300
    }
}
```

#### Опубликовать квест (отправить на модерацию)
Пример запроса:
```http
POST http://localhost:8080/api/quests/3/publish
Authorization: Bearer ******************************
```
Пример ответа:
```http
{
    "success": true,
    "message": "Квест успешно опубликован",
    "data": {
        "id": 3,
        "user_id": 1,
        "title": "Обновлённый квест API",
        "description": "Обновлённое описание",
        "type": "timed",
        "reward": 100,
        "min_participants": 2,
        "deadline": "2025-12-25 18:00:00",
        "status": "active",
        "is_approved": false,
        "created_at": 1733154200,
        "updated_at": 1733154400
    }
}
```

#### Удалить квест
Пример запроса:
```http
DELETE http://localhost:8080/api/quests/3/delete
Authorization: Bearer **********************
```
Пример ответа:
```http
{
    "success": true,
    "message": "Квест успешно удален",
    "data": {
        "id": 3,
        "user_id": 1,
        "title": "Обновлённый квест API",
        "description": "Обновлённое описание",
        "type": "timed",
        "reward": 100,
        "min_participants": 2,
        "deadline": "2025-12-25 18:00:00",
        "status": "draft",
        "is_approved": false,
        "created_at": 1733154200,
        "updated_at": 1733154400
    }
}
```

#### Получить список квестов на модерации
Пример запроса:
```http
GET http://localhost:8080/api/admin
Authorization: Bearer *******************
```
Пример ответа:
```http
{
    "success": true,
    "data": [
        {
            "id": 3,
            "user_id": 1,
            "title": "Квест на модерации",
            "description": "Этот квест ожидает одобрения",
            "type": "individual",
            "reward": 50,
            "min_participants": null,
            "deadline": null,
            "status": "active",
            "is_approved": false,
            "created_at": 1733154000,
            "updated_at": 1733154000
        }
    ]
}
```

#### Одобрить квест
Пример запроса:
```http
POST http://localhost:8080/api/admin/approve/3
Authorization: Bearer *********************
```
Пример ответа:
```http
{
    "success": true,
    "message": "Квест одобрен",
    "data": {
        "id": 3,
        "user_id": 1,
        "title": "Квест на модерации",
        "description": "Этот квест ожидает одобрения",
        "type": "individual",
        "reward": 50,
        "min_participants": null,
        "deadline": null,
        "status": "active",
        "is_approved": true,
        "created_at": 1733154000,
        "updated_at": 1733154000
    }
}
```

#### Отклонить квест
Пример запроса:
```http
POST http://localhost:8080/api/admin/reject/3
Authorization: Bearer ****************************
```
Пример ответа:
```http
{
    "success": true,
    "message": "Квест удалён",
    "data": {
        "id": 3,
        "user_id": 1,
        "title": "Квест на модерации",
        "description": "Этот квест ожидает одобрения",
        "type": "individual",
        "reward": 50,
        "min_participants": null,
        "deadline": null,
        "status": "active",
        "is_approved": false,
        "created_at": 1733154000,
        "updated_at": 1733154000
    }
}
```

## Структура проекта

- `application/` — основной код приложения (контроллеры, модели, сервисы, репозитории)
- `public/` — статические файлы и точка входа `index.php`
- `tests/` — юнит- и интеграционные тесты
- `docker/` — конфигурация Docker
- `init.sql` — скрипт инициализации базы данных
- `docker-compose.yml` — конфигурация запуска сервисов
