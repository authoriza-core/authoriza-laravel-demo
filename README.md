```markdown
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

## 🚀 Быстрый старт (план действий для разработчика)

Этот краткий план поможет быстро развернуть проект и запустить его локально.

### 1. Клонирование репозитория
```bash
git clone -b develop https://github.com/authoriza-core/authoriza-laravel-demo.git
cd authoriza-laravel-demo
```

### 2. Установка зависимостей
```bash
composer install
```

### 3. Создание файла окружения
```bash
cp .env.example .env   # Linux / Mac
copy .env.example .env # Windows (cmd)
```
Или в PowerShell:
```powershell
Copy-Item .env.example .env
```

### 4. Настройка базы данных (SQLite по умолчанию)
- Создайте пустой файл БД:
  ```bash
  touch database/database.sqlite   # Linux / Mac
  ```
  Для Windows (PowerShell):
  ```powershell
  New-Item -ItemType File -Path database\database.sqlite -Force
  ```
- Убедитесь, что в `.env` указано:
  ```
  DB_CONNECTION=sqlite
  DB_DATABASE=database/database.sqlite
  ```
  (Можно не указывать `DB_DATABASE`, тогда Laravel использует путь по умолчанию.)

### 5. Создание директорий для кэша
```bash
mkdir -p storage/framework/{cache,sessions,views}   # Linux / Mac
```
Для Windows (PowerShell):
```powershell
New-Item -ItemType Directory -Path storage\framework\cache -Force
New-Item -ItemType Directory -Path storage\framework\sessions -Force
New-Item -ItemType Directory -Path storage\framework\views -Force
```

### 6. Генерация ключа приложения
```bash
php artisan key:generate
```

### 7. Выполнение миграций (создание таблиц)
```bash
php artisan migrate
```

### 8. Настройка OIDC (Авториза)
- Зарегистрируйте приложение в Авторизе (тип **Confidential: Web Application**, Redirect URI: `http://Ваш_Redirect_URI/auth/callback`).
- Получите **Client ID** и **Client Secret**.
- Заполните `.env`:
  ```
  OIDC_ISSUER_URL=https://authoriza.ru
  OIDC_CLIENT_ID=ваш_client_id
  OIDC_CLIENT_SECRET=ваш_client_secret
  OIDC_REDIRECT_URI=http://Ваш_Redirect_URI/auth/callback
  ```

### 9. Запуск сервера
```bash
php artisan serve
```

### 10. Проверка работы
- Откройте `http://Ваш_Redirect_URI/auth/login`.
- Войдите через Авторизу.
- После успешного входа вы попадёте на страницу `/auth/tokens` с отображением токенов.

---

> **Если возникли ошибки**, обратитесь к разделу **«🐛 Возможные проблемы и решения»** в конце документа.

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

## 📂 Ключевые файлы

| Файл | Назначение |
|------|------------|
| **`AuthController.php`** | Тонкий контроллер (~100 строк). Перенаправляет на Авторизу, обрабатывает callback, вызывает сервисы для обмена токенов, обновления и выхода. |
| **`RefreshTokens.php`** | Middleware для автообновления Access Token при истечении (≤ 5 минут). |
| **`OidcService.php`** | Главный сервис, объединяет PKCE, управление токенами и сессией. |
| **`TokenManager.php`** | HTTP-запросы к Token Endpoint (обмен кода, обновление через Refresh Token). |
| **`PkceGenerator.php`** | Генерация `code_verifier` и `code_challenge` для PKCE. |

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
   OIDC_REDIRECT_URI=http://Ваш_Redirect_URI/auth/callback
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

- [Основной сайт Авторизы](https://authoriza.ru/)
- [Laravel Socialite Documentation](https://laravel.com/docs/socialite)
- [Laravel OIDC Socialite Provider](https://github.com/kovah/laravel-socialite-oidc)
- [Laragon Official Site](https://laragon.org)

---

## 👤 Автор

**Кристина**  
Проект выполнен в рамках практики по интеграции Авторизы для стека Laravel (PHP).  
[GitHub: kristenyn](https://github.com/kristenyn)
