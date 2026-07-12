<?php

namespace App\Services\Oidc;

// Декодирование JWT-токенов (парсинг Payload)
class JwtDecoder
{
    // Декодирование токена в массив данных
    public function decode(?string $token): ?array
    {
        if (!$token) return null;                     // Пустой токен
        $parts = explode('.', $token);               // Разделение на части (header.payload.signature)
        if (count($parts) < 2) return null;          // Невалидный JWT
        return json_decode(base64_decode($parts[1]), true); // Декодирование payload в массив
    }
}