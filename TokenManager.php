<?php

namespace App\Services\Oidc;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TokenManager
{
    // Обмен кода авторизации на токены
    public function exchangeCode(string $code, string $codeVerifier): array
    {
        $baseUrl = config('services.oidc.base_url');          // Базовый URL OIDC-сервера
        $tokenUrl = $baseUrl . '/token';                      // Эндпоинт для получения токенов

        $response = Http::asForm()->post($tokenUrl, [
            'grant_type' => 'authorization_code',             // Тип гранта
            'code' => $code,                                  // Код авторизации
            'redirect_uri' => config('services.oidc.redirect'), // Redirect URI
            'client_id' => config('services.oidc.client_id'), // Client ID
            'client_secret' => config('services.oidc.client_secret'), // Client Secret
            'code_verifier' => $codeVerifier,                 // PKCE code_verifier
        ]);

        $data = $response->json();                            // Парсинг JSON-ответа
        Log::info('Токены получены', ['data' => $data]);

        if (!isset($data['access_token'])) {
            throw new \Exception('Не удалось получить access_token');
        }

        return $data;
    }

    // Обновление токенов через Refresh Token
    public function refresh(string $refreshToken): array
    {
        $baseUrl = config('services.oidc.base_url');
        $tokenUrl = $baseUrl . '/token';

        $response = Http::asForm()->post($tokenUrl, [
            'grant_type' => 'refresh_token',                  // Тип гранта для обновления
            'refresh_token' => $refreshToken,                 // Refresh Token
            'client_id' => config('services.oidc.client_id'),
            'client_secret' => config('services.oidc.client_secret'),
            'scope' => 'openid profile email offline_access', // Запрашиваемые scope
        ]);

        $data = $response->json();
        Log::info('Токены обновлены', ['data' => $data]);

        if (!isset($data['access_token'])) {
            throw new \Exception('Не удалось обновить токен');
        }

        return $data;
    }
}