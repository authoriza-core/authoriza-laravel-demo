<?php

namespace App\Services\Oidc;

// Управление сессией: сохранение, очистка, проверка статуса
class SessionManager
{
    // Сохранение токенов и данных пользователя в сессию
    public function storeTokens(array $data, ?string $idToken = null): void
    {
        $userData = $idToken ? (new JwtDecoder())->decode($idToken) : [];

        session([
            'access_token' => $data['access_token'],                 // Access Token
            'refresh_token' => $data['refresh_token'] ?? null,       // Refresh Token
            'id_token' => $idToken,                                  // ID Token
            'expires_in' => $data['expires_in'] ?? 420,              // Время жизни в секундах
            'expires_at' => now()->addSeconds($data['expires_in'] ?? 420), // Точное время истечения
            'token_type' => 'Bearer',                                // Тип токена
            'scope' => 'openid profile email offline_access',        // Запрошенные scope
            'last_refresh' => now()->toDateTimeString(),             // Время последнего обновления
            'user' => [                                              // Данные пользователя
                'name' => $userData['name'] ?? null,
                'email' => $userData['email'] ?? null,
                'sub' => $userData['sub'] ?? null,
            ],
            'token_endpoint_response' => json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        ]);
    }

    // Полная очистка сессии
    public function clear(): void
    {
        session()->flush();
    }

    // Проверка: есть ли Access Token в сессии
    public function isAuthenticated(): bool
    {
        return session()->has('access_token');
    }

    // Геттер времени истечения Access Token
    public function getExpiresAt()
    {
        return session('expires_at');
    }

    // Геттер Refresh Token
    public function getRefreshToken()
    {
        return session('refresh_token');
    }

    // Геттер времени последнего обновления
    public function getLastRefresh()
    {
        return session('last_refresh');
    }
}