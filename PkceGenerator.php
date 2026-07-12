<?php

namespace App\Services\Oidc;

// Генерация PKCE-параметров для Authorization Code Flow
class PkceGenerator
{
    // Генерация code_verifier (случайная строка 64 символа)
    public function generateCodeVerifier(): string
    {
        // Допустимые символы согласно спецификации PKCE
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-._~';
        $length = 64;
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $result;
    }
    // Генерация code_challenge (SHA256 + Base64URL)
    public function generateCodeChallenge(string $codeVerifier): string
    {
        $hash = hash('sha256', $codeVerifier, true);            // SHA256
        return rtrim(strtr(base64_encode($hash), '+/', '-_'), '='); // Base64URL
    }
}