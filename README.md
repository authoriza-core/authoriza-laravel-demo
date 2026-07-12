# authoriza-laravel-demo

**Демонстрационный проект интеграции Авторизы для Laravel (PHP)**

Проект представляет собой веб-приложение на Laravel, демонстрирующее интеграцию с сервисом Авториза по протоколу OpenID Connect. Приложение реализует полный цикл аутентификации и работы с токенами в веб-среде.

---

## 📋 Назначение проекта

Данный проект является эталонным примером интеграции Авторизы для стека **Laravel (PHP)**. Он демонстрирует:

- Реализацию **OpenID Connect Authorization Code Flow** с **PKCE**.
- Использование **Discovery Endpoint** для автоматического получения конфигурации.
- Получение и отображение токенов (Access, ID, Refresh).
- Декодирование JWT-токенов и отображение их содержимого (Payload).
- Сохранение и восстановление сессии.
- Ручное и автоматическое обновление токенов.
- Выход из приложения с очисткой сессии.

---

## 🛠️ Стек технологий

| Компонент | Инструмент |
|-----------|------------|
| **Язык** | PHP 8.3+ |
| **Фреймворк** | Laravel 13.x |
| **OIDC клиент** | `kovah/laravel-socialite-oidc` (Laravel Socialite) |
| **HTTP-запросы** | `Illuminate\Support\Facades\Http` |
| **Декодирование JWT** | Встроенный `base64_decode` |
| **Сборка** | Composer |
| **Сервер** | PHP built-in server / Laragon / Apache / Nginx |

---

## ⚙️ Требования к окружению

Перед запуском убедитесь, что установлены следующие компоненты:

- **PHP 8.3** или новее (с расширениями: `openssl`, `curl`, `json`, `mbstring`, `session`).
- **Composer** — менеджер зависимостей для PHP.
- **Git** (опционально, для клонирования репозитория).
- **Laragon** (рекомендуется для Windows) или любой другой веб-сервер (Apache/Nginx).

---

## 🖥️ Установка локального сервера (Laragon)

**Laragon** — это легкий и быстрый локальный веб-сервер для Windows, который включает PHP, Apache/nginx и MySQL. Он идеально подходит для разработки на Laravel.

### 1. Скачайте и установите Laragon

- Перейдите на официальный сайт [laragon.org](https://laragon.org) и скачайте последнюю версию (Full-version).
- Запустите установщик и следуйте инструкциям (рекомендуется оставить путь по умолчанию `C:\laragon`).
- После установки запустите Laragon. В системном трее появится его значок.

### 2. Настройка и запуск

- В главном окне Laragon нажмите кнопку **"Start All"**. Это запустит веб-сервер Apache и базу данных MySQL.
- Laragon автоматически создаёт виртуальные хосты. Ваш проект будет доступен по адресу:
  ```
  http://authoriza-laravel-demo.test
  ```
  (если папка проекта называется `authoriza-laravel-demo`).

### 3. Создание проекта через Laragon

- Кликните правой кнопкой мыши по иконке Laragon в трее → **"Quick app"** → **"Laravel"**.
- Введите имя проекта (например, `authoriza-laravel-demo`). Laragon автоматически создаст проект в `C:\laragon\www\` и настроит виртуальный хост.

### 4. Альтернатива: использование встроенного PHP-сервера

Если вы не хотите устанавливать Laragon, можно использовать встроенный PHP-сервер (см. раздел **"🚀 Запуск проекта"**).

---

## 📦 Установка зависимостей

### 1. Клонирование репозитория

```bash
git clone https://github.com/authoriza-core/authoriza-laravel-demo
cd authoriza-laravel-demo
```

### 2. Установка зависимостей через Composer

```bash
composer install
```

### 3. Создание файла `.env`

Скопируйте файл `.env.example` в `.env`:

```bash
cp .env.example .env
```

### 4. Генерация ключа приложения

```bash
php artisan key:generate
```

---

## 🔐 Настройка приложения в Авторизе

Для работы приложения необходимо зарегистрировать его в Авторизе и получить **Client ID** и **Client Secret**.

### 1. Войдите в интерфейс Авторизы
### 2. Создайте новое приложение

| Параметр | Значение |
|----------|----------|
| **Имя** | `Laravel Demo` (любое) |
| **Тип** | `Confidential: Web Application` |
| **Redirect URI** | `http://127.0.0.1:8000/auth/callback` (для встроенного сервера)<br>или `http://authoriza-laravel-demo.test/auth/callback` (для Laragon) |
| **Состояние** | `ВКЛ` |

### 3. Сохраните приложение и скопируйте **Client ID** и **Client Secret**

---

## ⚙️ Настройка конфигурации (.env)

Отредактируйте файл `.env` и укажите полученные данные:

```env
# ===== OpenID Connect (Авториза) =====
OIDC_ISSUER_URL=https://oidc.authoriza.ru/oidc
OIDC_CLIENT_ID=ваш_client_id
OIDC_CLIENT_SECRET=ваш_client_secret
OIDC_REDIRECT_URI=http://127.0.0.1:8000/auth/callback
```

> ⚠️ **Важно:** `OIDC_REDIRECT_URI` должен точно совпадать с тем, что указан в настройках приложения Авторизы (включая порт и путь). Если вы используете Laragon с виртуальным хостом, замените на `http://authoriza-laravel-demo.test/auth/callback`.

---

## 🚀 Запуск проекта

### Вариант 1: Использование встроенного PHP-сервера (быстрый старт)

```bash
php artisan serve
```

По умолчанию сервер запускается на `http://127.0.0.1:8000`.

Откройте в браузере: `http://127.0.0.1:8000`

### Вариант 2: Использование Laragon (рекомендуется для Windows)

1. Убедитесь, что Laragon запущен и Apache/nginx работает.
2. Скопируйте проект в папку `C:\laragon\www\authoriza-laravel-demo` (если вы не создавали его через Quick app).
3. Откройте в браузере: `http://authoriza-laravel-demo.test`

---

## ✅ Проверка основных сценариев

### 1. Авторизация (Login)

1. Нажмите **«Login»** или перейдите по адресу `/auth/login`.
2. Откроется страница входа Авторизы.
3. Введите логин и пароль.
4. После успешного входа вы будете перенаправлены на страницу `/auth/tokens`.

**Ожидаемый результат:**
- Статус: `Авторизован ✅`
- Отображаются `Access Token`, `Refresh Token`, `ID Token`
- Отображаются декодированные Payload токенов
- Отображается ответ от Token Endpoint

---

### 2. Обновление токенов (Refresh)

- Нажмите **«Обновить токены»** или перейдите по адресу `/auth/refresh`.

**Ожидаемый результат:**
- Время истечения Access Token обновилось
- `last_refresh` обновилось
- Access Token изменился (стал другим)

---

### 3. Автоматическое обновление

- На странице `/auth/tokens` работает **JavaScript-таймер** (каждые 60 секунд).
- При запросе к `/auth/auto-refresh` токен обновляется, если до истечения осталось **≤ 5 минут**.
- Время `Access истекает` и `Последнее обновление` обновляются на странице без перезагрузки.

**Ожидаемый результат:**
- Страница автоматически обновляет время истечения
- Пользователь не замечает процесса обновления (бесшовно)

> **Примечание:** Токены обновляются в фоне, а страница отображает актуальное время истечения. Всплывающие уведомления отсутствуют.

---

### 4. Выход (Logout)

1. Нажмите **«Выйти»** или перейдите по адресу `/auth/logout`.
2. Сессия очищается, приложение перенаправляется на главную страницу (`/`).

**Ожидаемый результат:**
- Все токены удалены из сессии
- Пользователь видит стандартную страницу приветствия Laravel
- При переходе на `/auth/tokens` пользователь перенаправляется на `/auth/login`

---

### 5. Восстановление сессии

- Сессия восстанавливается автоматически при каждом запросе (Laravel Session).
- Если Access Token скоро истечёт (≤ 5 минут), он обновится через Middleware.

**Ожидаемый результат:**
- После перезапуска приложения сессия сохраняется
- Токены доступны на странице `/auth/tokens`

---

## 📁 Структура проекта

```
authoriza-laravel-demo/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── AuthController.php          # Тонкий контроллер
│   │   └── Middleware/
│   │       └── RefreshTokens.php            # Автообновление токенов
│   ├── Providers/
│   │   └── AppServiceProvider.php           # Регистрация OIDC-провайдера
│   └── Services/
│       ├── Oidc/
│       │   ├── OidcService.php              # Главный OIDC-сервис
│       │   ├── PkceGenerator.php            # Генерация PKCE
│       │   ├── TokenManager.php             # Управление токенами
│       │   ├── SessionManager.php           # Управление сессией
│       │   └── JwtDecoder.php               # Декодирование JWT
│       └── OidcDiscoveryService.php         # Сервис Discovery Endpoint
│
├── bootstrap/
│   └── app.php                              # Регистрация middleware
│
├── config/
│   ├── app.php                              # Настройки приложения (timezone)
│   └── services.php                         # Конфигурация OIDC-провайдера
│
├── routes/
│   └── web.php                              # Маршруты приложения
│
├── resources/views/
│   └── tokens.blade.php                     # Страница отображения токенов
│
├── .env.example                             # Шаблон конфигурации
├── .gitignore                               # Исключения для Git
└── README.md                                # Документация проекта
```

---

## 📂 Описание ключевых файлов

| Файл | Назначение | Что происходит |
|------|------------|----------------|
| **`AuthController.php`** | Контроллер аутентификации | Тонкий контроллер (~100 строк). Перенаправляет на Авторизу, обрабатывает callback, вызывает сервисы для обмена токенов, обновления и выхода. |
| **`RefreshTokens.php`** | Middleware для автообновления | Проверяет при каждом запросе, сколько осталось до истечения Access Token. Если ≤ 5 минут — обновляет токен через Refresh Token. |
| **`OidcService.php`** | Главный OIDC-сервис | Объединяет все подсервисы. Содержит методы для генерации PKCE, обработки callback, обновления токенов, проверки аутентификации. |
| **`PkceGenerator.php`** | Генератор PKCE | Генерирует `code_verifier` и `code_challenge` для Authorization Code Flow. |
| **`TokenManager.php`** | Управление токенами | Отправляет HTTP-запросы к Token Endpoint для обмена кода на токены и обновления токенов. |
| **`SessionManager.php`** | Управление сессией | Сохраняет токены в сессию, очищает сессию, проверяет наличие Access Token. |
| **`JwtDecoder.php`** | Декодер JWT | Декодирует JWT-токены и возвращает Payload в виде массива. |
| **`AppServiceProvider.php`** | Провайдер приложения | Регистрирует OIDC-провайдер для Laravel Socialite через `Event::listen()`. |
| **`OidcDiscoveryService.php`** | Сервис Discovery | Получает конфигурацию OIDC-сервера через `.well-known/openid-configuration`. |
| **`services.php`** | Конфигурация сервисов | Хранит настройки OIDC: `base_url`, `client_id`, `client_secret`, `redirect`, `scopes`. |
| **`web.php`** | Маршруты приложения | Определяет все маршруты: `/auth/login`, `/auth/callback`, `/auth/tokens`, `/auth/refresh`, `/auth/logout`, `/auth/auto-refresh`. |
| **`tokens.blade.php`** | Шаблон страницы токенов | Отображает токены, Payload, время истечения. Содержит JavaScript-таймер для автообновления. |
| **`.env`** | Переменные окружения | Хранит чувствительные данные: `OIDC_ISSUER_URL`, `OIDC_CLIENT_ID`, `OIDC_CLIENT_SECRET`, `OIDC_REDIRECT_URI`. |

---

## 🔧 Используемые команды

### Создание и установка проекта

| Команда | Назначение |
|---------|------------|
| `composer create-project laravel/laravel authoriza-laravel-demo` | Создание нового Laravel-проекта |
| `composer install` | Установка зависимостей |
| `php artisan key:generate` | Генерация ключа приложения |

### Установка пакетов

| Команда | Назначение |
|---------|------------|
| `composer require kovah/laravel-socialite-oidc` | Установка OIDC-драйвера для Socialite |
| `composer dump-autoload` | Обновление автозагрузчика Composer (после добавления/удаления классов) |

### Создание файлов

| Команда | Назначение |
|---------|------------|
| `php artisan make:controller AuthController` | Создание контроллера |
| `php artisan make:middleware RefreshTokens` | Создание middleware |
| `php artisan make:view tokens` | Создание Blade-шаблона |

### Очистка кэша

| Команда | Назначение |
|---------|------------|
| `php artisan config:clear` | Очистка кэша конфигурации |
| `php artisan cache:clear` | Очистка кэша приложения |
| `php artisan view:clear` | Очистка кэша шаблонов |
| `php artisan optimize:clear` | Полная очистка всех кэшей |

### Запуск и отладка

| Команда | Назначение |
|---------|------------|
| `php artisan serve` | Запуск сервера на `http://127.0.0.1:8000` |
| `php artisan tinker` | Интерактивная консоль для отладки |
| `php artisan route:list` | Просмотр всех маршрутов |
| `tail -f storage/logs/laravel.log` | Просмотр логов в реальном времени |

---

## 🐛 Возможные проблемы и решения

### 🔴 Критические ошибки (блокируют работу)

#### 1. Ошибка `invalid_client`

**Описание:** При попытке входа пользователь видит страницу с ошибкой `invalid_client` от Авторизы.

**Причины:**
- `OIDC_CLIENT_ID` в `.env` не совпадает с Client ID, полученным при регистрации приложения в Авторизе.
- `OIDC_REDIRECT_URI` в `.env` не совпадает с Redirect URI, указанным в настройках приложения Авторизы.
- В `config/services.php` указан неверный `base_url`.

**Решение:**

1. Проверьте файл `.env`:
   ```env
   OIDC_CLIENT_ID=ваш_реальный_client_id
   OIDC_REDIRECT_URI=http://127.0.0.1:8000/auth/callback
   ```
2. Войдите в интерфейс Авторизы → настройки приложения → скопируйте **точные** значения Client ID и Redirect URI.
3. Убедитесь, что `Redirect URI` в Авторизе и `OIDC_REDIRECT_URI` в `.env` совпадают **полностью** (включая порт и путь).
4. Проверьте `config/services.php`:
   ```php
   'base_url' => env('OIDC_ISSUER_URL', 'https://oidc.authoriza.ru/oidc'),
   ```
5. Очистите кэш конфигурации:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```
6. Перезапустите сервер и попробуйте войти снова.

---

#### 2. Ошибка `invalid_grant`

**Описание:** После входа пользователь видит ошибку `invalid_grant` при обмене кода на токены.

**Причины:**
- Код авторизации уже использован (повторный запрос).
- `code_verifier` не совпадает с `code_challenge` (проблема PKCE).
- `code_verifier` не сохранён в сессии или потерян.

**Решение:**

1. Убедитесь, что `code_verifier` сохраняется в сессию в `AuthController::redirectToProvider()`:
   ```php
   session(['code_verifier' => $pkce['verifier']]);
   ```
2. Проверьте, что в `AuthController::handleCallback()` `code_verifier` читается из сессии:
   ```php
   $verifier = session('code_verifier');
   if (!$verifier) return $this->error('Ошибка PKCE');
   ```
3. Убедитесь, что PKCE реализован корректно в `PkceGenerator`:
   ```php
   $verifier = $this->pkce->generateCodeVerifier();
   $challenge = $this->pkce->generateCodeChallenge($verifier);
   ```
4. Очистите сессию и попробуйте войти заново (выйдите из системы и зайдите снова).
5. Проверьте, что сессия сохраняется между запросами (не отключается).

---

#### 3. ID Token не отображается

**Описание:** На странице `/auth/tokens` отсутствует ID Token и его Payload.

**Причины:**
- Некоторые OIDC-серверы не выдают `id_token` при первом входе (только при обновлении).
- В запросе не передан scope `openid`.
- Сервер не поддерживает выдачу ID Token.

**Решение:**

1. Убедитесь, что в `AuthController::redirectToProvider()` есть scope `openid`:
   ```php
   ->scopes(['openid', 'profile', 'email', 'offline_access'])
   ```
2. Нажмите кнопку **«Обновить токены»** — после этого ID Token должен появиться.
3. Проверьте логи, чтобы убедиться, что сервер возвращает `id_token`:
   ```bash
   tail -f storage/logs/laravel.log
   ```
   Ищите запись `Ручной ответ от Token Endpoint`.
4. Если ID Token не появляется даже после обновления, проверьте `TokenManager::exchangeCode()` — возможно, сервер не включает `id_token` в ответ.
   ```php
   $idToken = $data['id_token'] ?? null;
   ```

---

#### 4. Ошибка 500 при входе

**Описание:** После нажатия «Login» возникает ошибка 500.

**Причины:**
- Проблемы с сервисами (неправильные зависимости).
- Ошибка в `AppServiceProvider` или `OidcDiscoveryService`.
- Отсутствует `base_url` в `config/services.php`.

**Решение:**

1. Проверьте логи:
   ```bash
   tail -n 50 storage/logs/laravel.log
   ```
2. Убедитесь, что все сервисы зарегистрированы в `AppServiceProvider`:
   ```php
   $this->app->singleton(OidcService::class, function ($app) {
       return new OidcService(
           new PkceGenerator(),
           new TokenManager(),
           new SessionManager(),
           new JwtDecoder()
       );
   });
   ```
3. Проверьте `config/services.php` — должен быть заполнен `base_url`.

---

### 🟡 Сложности при настройке

#### 5. Автообновление не работает

**Описание:** JavaScript-таймер на странице `/auth/tokens` не обновляет токены, время не меняется.

**Причины:**
- В браузере отключён JavaScript или заблокирован `fetch` API.
- В консоли браузера есть ошибки (F12 → Console).
- Маршрут `/auth/auto-refresh` не защищён middleware или возвращает ошибку.
- Refresh Token истек.

**Решение:**

1. Откройте консоль разработчика (F12) и проверьте наличие ошибок.
2. Проверьте маршрут:
   ```bash
   php artisan route:list | grep auto-refresh
   ```
3. Проверьте логи:
   ```bash
   tail -f storage/logs/laravel.log
   ```
   Ищите записи: `RefreshTokens check`, `Обновление токена запущено`, `Токены обновлены успешно`.
4. Убедитесь, что Refresh Token существует в сессии. Проверьте в `AuthController::autoRefresh()`:
   ```php
   $rt = session('refresh_token');
   if (!$rt) return response()->json(['error'=>'Нет Refresh Token'],401);
   ```
5. Если Refresh Token истек, пользователь будет перенаправлен на `/auth/login`. Это нормальное поведение.

---

#### 6. Ошибка 401 при автообновлении

**Описание:** В консоли браузера появляется ошибка 401, и пользователь перенаправляется на страницу входа.

**Причины:**
- Refresh Token истек (сервер вернул `invalid_grant`).
- Refresh Token не сохранён в сессии.
- Пользователь был разлогинен вручную.

**Решение:**

1. Это нормальное поведение — если Refresh Token истек, сессия автоматически очищается:
   ```php
   session()->flush();
   return response()->json(['error'=>'Сессия истекла'],401);
   ```
2. Пользователь должен войти заново через `/auth/login`.
3. Если ошибка появляется слишком часто, проверьте время жизни Refresh Token (обычно 24 часа) в `SessionManager::storeTokens()`.

---

#### 7. Laragon не видит проект

**Описание:** После клонирования репозитория проект не открывается по адресу `http://authoriza-laravel-demo.test`.

**Причины:**
- Проект находится не в папке `C:\laragon\www\`.
- Виртуальный хост не создан.
- Apache/nginx не запущен.

**Решение:**

1. Убедитесь, что проект находится в `C:\laragon\www\authoriza-laravel-demo`.
2. В Laragon нажмите **"Start All"** и убедитесь, что Apache/nginx и MySQL запущены (зелёные индикаторы).
3. Перезапустите Laragon (меню → **"Restart All"**).
4. Если виртуальный хост не создан, создайте его вручную: меню Laragon → **"Quick app"** → **"Laravel"** (повторно).

---

#### 8. Порт 8000 занят

**Описание:** При запуске `php artisan serve` появляется ошибка, что порт 8000 уже используется.

**Причины:**
- Другое приложение использует порт 8000.
- Предыдущий экземпляр сервера не был остановлен.

**Решение:**

1. Найти процесс, использующий порт 8000:
   ```bash
   netstat -ano | findstr :8000
   ```
2. Завершить процесс (PID указан в выводе):
   ```bash
   taskkill /PID <номер_процесса> /F
   ```
3. Или запустить сервер на другом порту:
   ```bash
   php artisan serve --port=8080
   ```
4. После изменения порта обновите `OIDC_REDIRECT_URI` в `.env` и в настройках Авторизы.

---

### 🟢 Ошибки после изменения кода

#### 9. Ошибка `Target class [cache] does not exist`

**Описание:** После обновления кода или удаления файлов появляется ошибка `Target class [cache] does not exist`.

**Причины:**
- Удалён или изменён файл, который использовался в `composer.json` для автозагрузки.
- Старый кэш автозагрузки сохранился.

**Решение:**

1. Обновите автозагрузку Composer:
   ```bash
   composer dump-autoload
   ```
2. Очистите кэш Laravel:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   php artisan route:clear
   php artisan optimize:clear
   ```
3. Перезапустите сервер.

---

#### 10. Ошибка `Call to undefined method ...`

**Описание:** После рефакторинга появляется ошибка о несуществующем методе.

**Причины:**
- В контроллере вызывается метод, который был перенесён в сервис.
- В сервисе используется метод, который не объявлен.

**Решение:**

1. Проверьте, что все методы перенесены в сервисы корректно.
2. Проверьте, что в `__construct()` переданы правильные зависимости.
3. Проверьте, что методы объявлены с правильными именами и сигнатурами.
4. Проверьте импорт классов в контроллере и сервисах.

---

#### 11. Ошибка `Class "App\Services\Oidc\..." not found`

**Описание:** После создания новых сервисов появляется ошибка, что класс не найден.

**Причины:**
- Класс создан, но не обновлена автозагрузка Composer.
- Неправильное пространство имён.

**Решение:**

1. Проверьте пространство имён:
   ```php
   namespace App\Services\Oidc;
   ```
2. Проверьте, что файл лежит по правильному пути:
   ```
   app/Services/Oidc/ИмяКласса.php
   ```
3. Выполните:
   ```bash
   composer dump-autoload
   php artisan config:clear
   ```

---

### 📋 Быстрая диагностика

Если проблема не описана выше:

1. Проверьте логи:
   ```bash
   tail -n 100 storage/logs/laravel.log
   ```
2. Проверьте маршруты:
   ```bash
   php artisan route:list
   ```
3. Проверьте конфигурацию:
   ```bash
   php artisan config:show services.oidc
   ```
4. Проверьте, что `.env` содержит все необходимые переменные:
   ```bash
   cat .env | grep OIDC
   ```
5. Проверьте, что сессия работает:
   ```bash
   php artisan tinker
   ```
   ```php
   session(['test' => 'ok']);
   session('test');
   ```

---

## 📝 Полезные ссылки

- [Документация Авторизы](https://a-kalinin-authoriza-frontend-stand-a5dc.twc1.net/docs/)
- [Laravel Socialite Documentation](https://laravel.com/docs/socialite)
- [Laravel OIDC Socialite Provider](https://github.com/kovah/laravel-socialite-oidc)
- [Laragon Official Site](https://laragon.org)

---

## 👤 Автор

**Кристина**  
Проект выполнен в рамках практики по интеграции Авторизы для стека Laravel (PHP).  
[GitHub: kristenyn](https://github.com/kristenyn)
```
