# CLAUDE.md

Этот файл содержит руководство для Claude Code (claude.ai/code) при работе с кодом в этом репозитории.

## Обзор проекта

TriPlan API — это бэкенд на Laravel 12 для планирования путешествий, который управляет поездками, точками (локациями) и тегами. Приложение использует двухфакторную аутентификацию по email с Laravel Sanctum и PostgreSQL с расширением PostGIS для геопространственных данных.

## Команды разработки

### Тестирование
```bash
# Первоначальная настройка тестовой базы данных
# 1. Скопировать .env.testing.example в .env.testing
cp .env.testing.example .env.testing

# 2. Создать тестовую базу данных (если еще не создана)
mysql -h 127.0.0.1 -u root -e "CREATE DATABASE IF NOT EXISTS trip_plan_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 3. Запустить миграции для тестовой БД
php artisan migrate:fresh --env=testing --force

# Запустить все тесты с помощью Pest
php artisan test

# Запустить тесты параллельно (быстрее)
php artisan test --parallel

# Запустить конкретный файл с тестами
php artisan test tests/Feature/TripControllerTest.php

# Запустить тесты с покрытием
php artisan test --coverage

# Запустить конкретный тест по имени
php artisan test --filter=test_user_can_create_trip
```

### Качество кода
```bash
# Форматировать код с помощью Laravel Pint
./vendor/bin/pint

# Проверить стиль кода без исправления
./vendor/bin/pint --test
```

### Сервер разработки
```bash
# Запустить окружение разработки (сервер + очередь + логи + vite)
composer dev

# Или вручную:
php artisan serve
php artisan queue:listen --tries=1
php artisan pail --timeout=0
```

### База данных
```bash
# Запустить миграции
php artisan migrate

# Пересоздать базу данных с сидами
php artisan migrate:fresh --seed

# Создать новую миграцию
php artisan make:migration create_table_name
```

### API Документация
```bash
# Сгенерировать API документацию (использует Scramble)
# Доступна по адресу /docs/api после запуска сервера
# Документация автоматически генерируется из роутов, контроллеров и FormRequests
```

### IDE Помощники
```bash
# Сгенерировать файлы IDE helper (запускается автоматически после composer update)
php artisan ide-helper:generate
php artisan ide-helper:meta
```

### Отладка с Ray
```bash
# Ray - инструмент для отладки от Spatie
# Требует установки desktop приложения Ray: https://myray.app

# Использование в коде:
ray($variable);                    # Вывести переменную
ray()->showQueries();              # Отслеживать SQL запросы
ray()->pause();                    # Приостановить выполнение
ray()->measure(fn() => /* ... */); # Измерить время выполнения
ray()->json($data);                # Форматировать как JSON
ray()->table($array);              # Вывести как таблицу

# Ray автоматически работает только в local/development окружении
```

## Архитектура

### Требования к настройке базы данных

Проект требует PostgreSQL с расширением PostGIS:

```sql
CREATE SCHEMA postgis;
CREATE EXTENSION postgis SCHEMA postgis;
ALTER DATABASE trip_plan SET search_path=public,postgis;
```

### Основные модели предметной области

В приложении есть пять основных сущностей с UUID первичными ключами:

1. **User** - Пользователи, владеющие поездками и точками
2. **Point** - Географические локации с координатами PostGIS (долгота/широта)
3. **Tag** - Метки для категоризации точек (с иконкой и цветом)
4. **Trip** - Планы путешествий с диапазонами дат
5. **TripPoint** - Промежуточная модель, связывающая поездки с точками и содержащая данные планирования (день, время, порядок, примечание)

### Ключевые связи

- **Trip → Points**: Многие-ко-многим через промежуточную таблицу `TripPoint` (пользовательская Pivot модель)
- **Point → Tags**: Многие-ко-многим через таблицу `point_tag`
- Все модели принадлежат **User** и автоматически фильтруются через `UserScope`

### Паттерн пользовательской области видимости

Все модели (Point, Tag, Trip, TripPoint) автоматически применяют глобальную область видимости `UserScope`, которая фильтрует все запросы по ID аутентифицированного пользователя. Это гарантирует, что пользователи могут получать доступ только к своим данным без явных where-условий в контроллерах.

```php
// UserScope применяется автоматически - ручная фильтрация не нужна
$trips = Trip::all(); // Возвращает только поездки текущего пользователя
```

### Поток аутентификации

Двухфакторная аутентификация через email:
1. **Registration**: POST `/api/auth/registration` - Создает пользователя, отправляет код подтверждения
2. **Get Code**: POST `/api/auth/get_code` - Отправляет 2FA код на email
3. **Get Token**: POST `/api/auth/get_token` - Обменивает код на Bearer токен
4. **Protected Routes**: Все роуты под `/api`, кроме auth, требуют middleware `auth:sanctum`

### DTO с Spatie Laravel Data

Проект использует `spatie/laravel-data` для типобезопасных объектов передачи данных. DTO находятся в `app/DTO/`:

- Form DTO валидируют и структурируют входящие запросы
- Используются в классах FormRequest для валидации
- Обеспечивают автоматическое приведение типов и валидацию

Пример:
```php
class PointForm extends Data {
    public function __construct(
        public string $name,
        public ?string $address,
        public LocationData $location,
        public ?string $note,
        public ?array $tags = null,
    ) {}
}
```

### Интеграция PostGIS

Точки используют PostGIS для географических координат через пользовательский `PointCast`:

- Хранит координаты как `POINT(latitude longitude)` в формате PostGIS
- Преобразует в/из формата массива: `['longitude' => float, 'latitude' => float]`
- Использует SRID 4326 (система координат WGS 84)

**Важно**: При установке данных локации cast ожидает `['latitude' => float, 'longitude' => float]`, но сохраняет в формате `POINT(latitude longitude)`.

### Паттерн Observer

Наблюдатели моделей автоматически устанавливают `user_id` при создании, используя `UserIdTrait`:

- `PointObserver`
- `TagObserver`
- `TripObserver`
- `TripPointObserver`

Зарегистрированы в `AppServiceProvider::boot()`.

### Планирование TripPoint

`TripPoint` — это пользовательская Pivot модель (не просто таблица) с дополнительными полями:

- `day` (integer): Номер дня в поездке (обязательное, мин: 1)
- `time` (string|null): Опциональное время посещения точки
- `order` (integer): Порядок сортировки внутри дня (автоматически назначается, если null)
- `note` (string|null): Заметки пользователя для этой конкретной точки в поездке

Точки сортируются по `day` ASC, `order` ASC при получении.

### API Resources

Laravel API Resources трансформируют модели для JSON ответов:

- `PointResource` - включает связь tags
- `TripResource` - включает points через промежуточную TripPoint
- `TripPointResource` - включает полные данные Point
- `TagResource`, `UserResource`

`JsonResource::withoutWrapping()` включен глобально (нет обертки `data`).

### API Документация

Использует `dedoc/scramble` для автоматической генерации OpenAPI 3.1 документации:

- Документация генерируется из роутов, контроллеров и FormRequests
- Доступна по эндпоинту `/docs/api`
- Сохраняется в `api.json` для версионного контроля
- Настроена аутентификация Bearer токеном
- Документирует только роуты, начинающиеся с `api/`

### Тестирование

Использует Pest PHP для тестирования:

- **Отдельная тестовая база MySQL** `trip_plan_test` (не затрагивает основную БД)
- Настройки тестового окружения в `.env.testing`
- Feature тесты для всех контроллеров в `tests/Feature/`
- Тесты покрывают полные CRUD операции и авторизацию
- Запускаются командой `php artisan test`
- При первом запуске нужно выполнить: `php artisan migrate:fresh --env=testing`

**Важно**: Используется MySQL вместо SQLite из-за зависимости от PostGIS для географических данных.

## Общие паттерны

### Создание нового ресурса

1. **Migration**: Создать таблицу с UUID первичным ключом, внешним ключом `user_id`
2. **Model**:
   - Использовать трейт `HasUuids`
   - Установить `protected $primaryKey = 'uuid'` и `protected $keyType = 'string'`
   - Установить `public $incrementing = false`
   - Применить глобальную область видимости `UserScope`
   - Определить связи
3. **DTO/FormRequest**: Создать класс DTO и правила валидации
4. **Observer**: Создать наблюдатель для установки `user_id` при создании
5. **Controller**: Стандартный resource контроллер с авторизацией
6. **Resource**: API Resource для JSON трансформации
7. **Routes**: Добавить в `api.php` с middleware `auth:sanctum`
8. **Tests**: Feature тесты для CRUD операций

### Авторизация

Используйте Laravel Policies для детальной авторизации. `UserScope` обрабатывает базовую изоляцию пользователей, но политики могут добавлять дополнительные проверки (например, проверка владения поездкой перед добавлением точек).

### Формат UUID

Все модели используют UUID в качестве первичных ключей. При создании связей используйте соглашение об именовании `{model}_uuid` (например, `trip_uuid`, `point_uuid`).

## Структура проекта

```
app/
├── Casts/              # Пользовательские Eloquent касты (например, PointCast для PostGIS)
├── DTO/                # Объекты Spatie Laravel Data
├── Http/
│   ├── Controllers/    # API контроллеры
│   ├── Requests/       # Классы FormRequest с валидацией
│   └── Resources/      # API Resources для JSON трансформации
├── Models/
│   └── Scopes/         # Глобальные области видимости запросов (UserScope)
├── Observers/          # Наблюдатели моделей для автоматической установки user_id
├── Policies/           # Политики авторизации
└── Traits/             # Переиспользуемые трейты (UserIdTrait)

database/migrations/    # Миграции схемы базы данных
tests/Feature/          # Feature тесты Pest
routes/api.php          # Определения API роутов
```

## Заметки

- Laravel 12 с PHP 8.3+
- Использует UUID v4 для всех первичных ключей
- PostGIS требуется для географических данных
- 2FA на основе email (без хранения пароля)
- Все API ответы в формате JSON без обертки
- Токены Sanctum для API аутентификации
- Глобальная пользовательская область видимости предотвращает утечку данных между пользователями

## MCP Context7
https://context7.com/laravel/docs
