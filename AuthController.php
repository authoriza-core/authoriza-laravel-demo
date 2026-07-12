<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;                              // HTTP-запросы
use Laravel\Socialite\Facades\Socialite;                 // OIDC-клиент
use App\Services\Oidc\OidcService;                       // Главный OIDC-сервис
use App\Services\Oidc\JwtDecoder;                        // Декодирование JWT
use Illuminate\Support\Facades\Log;                       // Логирование

class AuthController extends Controller
{
    public function __construct(private OidcService $oidc) {} // Внедрение OIDC-сервиса
    public function redirectToProvider()                    // Редирект на Авторизу
    {
        $pkce = $this->oidc->generatePkce();               // Генерация PKCE
        session(['code_verifier' => $pkce['verifier']]);   // Сохранение verifier
        return Socialite::driver('oidc')                   // Редирект через Socialite
            ->scopes(['openid', 'profile', 'email', 'offline_access'])
            ->with(['prompt'=>'login consent','code_challenge'=>$pkce['challenge'],'code_challenge_method'=>'S256'])
            ->redirect();
    }
    public function handleCallback(Request $request)        // Обработка callback
    {
        $code = $request->input('code');                   // Получение кода
        $verifier = session('code_verifier');              // Получение verifier
        if (!$code) return $this->error('Код не получен'); // Ошибка: нет кода
        if (!$verifier) return $this->error('Ошибка PKCE');// Ошибка: нет verifier
        try {
            $this->oidc->handleCallback($code, $verifier); // Обмен кода на токены
            return redirect('/auth/tokens');               // Переход на страницу токенов
        } catch (\Exception $e) {
            Log::error('Ошибка обмена кода: ' . $e->getMessage());
            return $this->error('Ошибка авторизации');     // Общая ошибка
        }
    }
    public function showTokens()                            // Отображение токенов
    {
        if (!$this->oidc->isAuthenticated())               // Проверка аутентификации
            return redirect('/auth/login')->with('error', 'Вы не авторизованы');
        $decoder = new JwtDecoder();                       // Декодер JWT
        return view('tokens', [                            // Передача данных в шаблон
            'access_token' => session('access_token'),
            'refresh_token' => session('refresh_token'),
            'id_token' => session('id_token'),
            'user' => session('user'),
            'expires_in' => session('expires_in'),
            'expires_at' => session('expires_at'),
            'last_refresh' => session('last_refresh'),
            'access_payload' => $decoder->decode(session('access_token')), // Payload Access
            'id_payload' => $decoder->decode(session('id_token')),         // Payload ID
            'token_endpoint_response' => session('token_endpoint_response'),
        ]);
    }
    private function error($msg)                           // Перенаправление с ошибкой
    {
        return redirect('/auth/login')->with('error', $msg);
    }

    private function refreshLogic($token)                  // Логика обновления
    {
        if (!$token) return false;                         // Нет токена
        try {
            $this->oidc->refreshTokens($token);            // Обновление через сервис
            return true;                                   // Успех
        } catch (\Exception $e) {
            Log::error('Refresh error: ' . $e->getMessage());
            return false;                                  // Ошибка
        }
    }
    public function refresh()                               // Ручное обновление
    {
        $rt = session('refresh_token');                    // Получение RT
        if (!$rt) return $this->error('Нет Refresh Token');
        return $this->refreshLogic($rt) ? redirect('/auth/tokens') : $this->error('Ошибка обновления');
    }
    public function autoRefresh()                           // Автообновление (AJAX)
    {
        $rt = session('refresh_token');                    // Получение RT
        if (!$rt) return response()->json(['error'=>'Нет Refresh Token'],401);
        $expiresAt = session('expires_at');                // Время истечения
        $secondsLeft = $expiresAt ? now()->diffInSeconds($expiresAt) : null;
        if ($secondsLeft !== null && $secondsLeft > 300)   // >5 мин → не обновлять
            return response()->json(['success'=>true,'message'=>'Обновление не требуется','seconds_left'=>$secondsLeft,'expires_at'=>$expiresAt,'last_refresh'=>session('last_refresh')]);
        if ($this->refreshLogic($rt))                      // Обновление
            return response()->json(['success'=>true,'expires_at'=>session('expires_at'),'last_refresh'=>session('last_refresh')]);
        session()->flush();                                // Очистка сессии при ошибке
        return response()->json(['error'=>'Сессия истекла'],401);
    }
    public function logout()                                // Выход
    {
        $this->oidc->logout();                             // Очистка сессии
        return redirect('/')->with('status', 'Вы вышли из системы');
    }
}