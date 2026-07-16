<?php

namespace App\Services\Oidc;

// Главный сервис OIDC, объединяет все подсервисы
class OidcService
{
    public function __construct(
        private PkceGenerator $pkce,        // Генератор PKCE
        private TokenManager $tokenManager, // Управление токенами
        private SessionManager $sessionManager, // Работа с сессией
        private JwtDecoder $jwtDecoder      // Декодер JWT
    ) {}

    // Генерация PKCE-пары (verifier + challenge)
    public function generatePkce(): array
    {
        $verifier = $this->pkce->generateCodeVerifier();
        $challenge = $this->pkce->generateCodeChallenge($verifier);
        return ['verifier' => $verifier, 'challenge' => $challenge];
    }

    // Обработка callback: обмен кода на токены и сохранение в сессию
    public function handleCallback(string $code, string $codeVerifier): void
    {
        $data = $this->tokenManager->exchangeCode($code, $codeVerifier);
        $idToken = $data['id_token'] ?? null;
        $this->sessionManager->storeTokens($data, $idToken);
    }

    // Обновление токенов через Refresh Token
    public function refreshTokens(string $refreshToken): array
    {
        $data = $this->tokenManager->refresh($refreshToken);
        $idToken = $data['id_token'] ?? session('id_token');
        $this->sessionManager->storeTokens($data, $idToken);
        return $data;
    }

    // Выход: очистка сессии
    public function logout(): void
    {
        $this->sessionManager->clear();
    }

    // Проверка: авторизован ли пользователь
    public function isAuthenticated(): bool
    {
        return $this->sessionManager->isAuthenticated();
    }

    // Геттер времени истечения Access Token
    public function getExpiresAt()
    {
        return $this->sessionManager->getExpiresAt();
    }

    // Геттер Refresh Token
    public function getRefreshToken()
    {
        return $this->sessionManager->getRefreshToken();
    }

    // Геттер времени последнего обновления
    public function getLastRefresh()
    {
        return $this->sessionManager->getLastRefresh();
    }
}