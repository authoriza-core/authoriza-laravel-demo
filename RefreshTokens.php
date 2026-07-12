<?php

// Пространство имен для middleware
namespace App\Http\Middleware;

// Импорт необходимых классов
use Closure;                      // Для определения замыкания middleware
use Illuminate\Http\Request;       // Объект HTTP-запроса
use Illuminate\Support\Facades\Http; // HTTP-клиент для запросов
use Illuminate\Support\Facades\Log;  // Логирование
use Carbon\Carbon;                 // Работа с датами и временем

// Класс middleware для автоматического обновления токенов
class RefreshTokens
{
    public function handle(Request $request, Closure $next) // Основной метод обработки запроса
    {
        // Если в сессии нет access_token, пропускаем запрос без обновления
        if (!session('access_token')) {
            return $next($request);
        }
        // Получаем время истечения токена и парсим его в Carbon, либо null
        $expiresAt = session('expires_at') ? Carbon::parse(session('expires_at')) : null;
        // Текущее время
        $now = Carbon::now();
        // Вычисляем количество секунд до истечения
        $secondsLeft = $expiresAt ? $now->diffInSeconds($expiresAt) : null;

        // Логируем информацию о состоянии токена для отладки
        Log::info('RefreshTokens check', [
            'expires_at' => $expiresAt,
            'seconds_left' => $secondsLeft,
            'now' => $now->toDateTimeString(),
        ]);
        // Если токен существует и до истечения осталось 300 секунд (5 минут) или меньше
        if ($expiresAt && $secondsLeft !== null && $secondsLeft <= 300) {
            Log::info('Обновление токена запущено');

            // Получаем refresh_token из сессии
            $refreshToken = session('refresh_token');
            // Если его нет, очищаем сессию и перенаправляем на логин с ошибкой
            if (!$refreshToken) {
                Log::warning('Нет Refresh Token');
                session()->forget(['access_token', 'refresh_token', 'expires_at']);
                return redirect('/auth/login')->with('error', 'Нет Refresh Token');
            }
            try {
                // Определяем URL для запроса токенов
                $baseUrl = config('services.oidc.base_url');
                $tokenUrl = $baseUrl . '/token';
                // Если в конфигурации задан token_endpoint, используем его
                if (config('services.oidc.token_endpoint')) {
                    $tokenUrl = config('services.oidc.token_endpoint');
                }

                Log::info('Отправка refresh-запроса', ['url' => $tokenUrl]);

                // Отправляем POST-запрос с данными в формате application/x-www-form-urlencoded
                $response = Http::asForm()->post($tokenUrl, [
                    'grant_type' => 'refresh_token',   // Тип гранта
                    'refresh_token' => $refreshToken,   // Сам refresh-токен
                    'client_id' => config('services.oidc.client_id'),      // ID клиента
                    'client_secret' => config('services.oidc.client_secret'), // Секрет клиента
                    'scope' => 'openid profile email offline_access',       // Запрашиваемые scope
                ]);
                $data = $response->json(); // Декодируем JSON-ответ в массив
                Log::info('Ответ сервера при обновлении', ['data' => $data]);
                // Проверяем, выдал ли сервер новый refresh_token (логируем для информации)
                if (isset($data['refresh_token'])) {
                    Log::info('Сервер выдал новый Refresh Token');
                } else {
                    Log::info('Сервер не выдал новый Refresh Token');
                }
                // Если в ответе отсутствует access_token — ошибка
                if (!isset($data['access_token'])) {
                    Log::error('В ответе нет access_token');
                    session()->forget(['access_token', 'refresh_token', 'expires_at']);
                    return redirect('/auth/login')->with('error', 'Ошибка обновления токена');
                }
                // Обновляем сессию новыми данными
                session([
                    'access_token' => $data['access_token'],                  // Новый access_token
                    'refresh_token' => $data['refresh_token'] ?? $refreshToken, // Если сервер не выдал новый, оставляем старый
                    'expires_in' => $data['expires_in'] ?? 420,               // Время жизни в секундах (по умолчанию 420)
                    'expires_at' => now()->addSeconds($data['expires_in'] ?? 420), // Метка времени истечения
                    'refresh_expires_in' => 86400,                           // Задаём фиксированное время жизни refresh (сутки)
                    'refresh_expires_at' => now()->addSeconds(86400),        // Время истечения refresh
                    'last_refresh' => now()->toDateTimeString(),             // Метка последнего обновления
                ]);
                Log::info('Токены обновлены успешно', ['last_refresh' => session('last_refresh')]);
            } catch (\Exception $e) {
                // В случае ошибки логируем, очищаем сессию и перенаправляем на логин
                Log::error('Ошибка обновления токена: ' . $e->getMessage());
                session()->forget(['access_token', 'refresh_token', 'expires_at']);
                return redirect('/auth/login')->with('error', 'Сессия истекла. Войдите заново.');
            }
        }
        return $next($request); // Пропускаем запрос дальше по цепочке middleware
    }
}