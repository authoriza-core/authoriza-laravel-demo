<?php

// Объявление пространства имен для сервисов приложения
namespace App\Services;

// Импорт фасада Http для выполнения HTTP-запросов
use Illuminate\Support\Facades\Http;

// Класс для получения OIDC конфигурации через Discovery Endpoint
class OidcDiscoveryService
{
    
    // Получить конфигурацию OIDC через Discovery Endpoint (без кэширования)
    public function getConfiguration(string $issuerUrl): array
    {
        // Формирование URL для .well-known/openid-configuration, удаляя завершающий слеш
        $discoveryUrl = rtrim($issuerUrl, '/') . '/.well-known/openid-configuration';

        // Выполнение GET-запроса с таймаутом 10 секунд
        $response = Http::timeout(10)->get($discoveryUrl);

        // Проверка, что ответ успешный (статус 2xx)
        if (!$response->successful()) {
            // Выброс исключения, если discovery endpoint недоступен
            throw new \Exception('Discovery endpoint недоступен: ' . $discoveryUrl);
        }

        // Преобразование JSON-ответа в ассоциативный массив
        $data = $response->json();

        // Список обязательных полей, которые должны присутствовать в конфигурации
        $required = ['authorization_endpoint', 'token_endpoint', 'userinfo_endpoint'];
        // Перебор обязательных полей
        foreach ($required as $field) {
            // Проверка наличия каждого поля в данных
            if (!isset($data[$field])) {
                // Выброс исключения, если поле отсутствует
                throw new \Exception("Discovery endpoint не вернул поле: {$field}");
            }
        }

        // Возврат полученной конфигурации
        return $data;
    }

    //Получить конкретный эндпоинт из конфигурации
    public function getEndpoint(string $issuerUrl, string $endpointKey): string
    {
        // Получение полной конфигурации через основной метод
        $config = $this->getConfiguration($issuerUrl);
        // Возврат значения по ключу, либо выбрасывание исключения, если ключ отсутствует
        return $config[$endpointKey] ?? throw new \Exception("Endpoint {$endpointKey} не найден");
    }

    // Получить все эндпоинты для использования в конфигурации
    public function getEndpointsForConfig(string $issuerUrl): array
    {
        // Получение полной конфигурации
        $config = $this->getConfiguration($issuerUrl);

        // Возврат ассоциативного массива с извлеченными основными и опциональными полями
        return [
            'authorization_endpoint' => $config['authorization_endpoint'],   // Обязательный эндпоинт авторизации
            'token_endpoint'         => $config['token_endpoint'],           // Обязательный эндпоинт для токенов
            'userinfo_endpoint'      => $config['userinfo_endpoint'],        // Обязательный эндпоинт для информации о пользователе
            'jwks_uri'               => $config['jwks_uri'] ?? null,         // Опциональный URI для JWKS
            'end_session_endpoint'   => $config['end_session_endpoint'] ?? null, // Опциональный эндпоинт для выхода из сессии
            'scopes_supported'       => $config['scopes_supported'] ?? [],   // Поддерживаемые scope (по умолчанию пустой массив)
            'response_types_supported' => $config['response_types_supported'] ?? [], // Поддерживаемые типы ответов
            'subject_types_supported'  => $config['subject_types_supported'] ?? [],  // Поддерживаемые типы субъектов
        ];
    }
}
