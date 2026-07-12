<?php

// Объявление пространства имен для провайдеров приложения
namespace App\Providers;

// Импорт базового класса ServiceProvider
use Illuminate\Support\ServiceProvider;
// Импорт фасада Event для работы с событиями
use Illuminate\Support\Facades\Event;
// Импорт события, которое вызывается при регистрации Socialite-провайдеров
use SocialiteProviders\Manager\SocialiteWasCalled;
// Импорт нашего сервиса для OIDC Discovery
use App\Services\OidcDiscoveryService;

// Класс основного провайдера приложения
class AppServiceProvider extends ServiceProvider
{
    // Метод register вызывается при регистрации провайдера
    public function register(): void
    {
        // Регистрируем Discovery сервис как синглтон в контейнере
        $this->app->singleton(OidcDiscoveryService::class, function ($app) {
            // Возвращаем новый экземпляр сервиса
            return new OidcDiscoveryService();
        });

        // Загружаем конфигурацию через Discovery (закомментировано)
        //$this->loadDiscoveryConfig();
    }

    // Метод boot выполняется после регистрации всех сервисов
    public function boot(): void
    {
        // Слушаем событие SocialiteWasCalled
        Event::listen(function (SocialiteWasCalled $event) {
            // Расширяем Socialite драйвером 'oidc' с указанием класса провайдера
            $event->extendSocialite('oidc', \SocialiteProviders\OIDC\Provider::class);
        });
    }

    // Приватный метод для загрузки конфигурации через Discovery
    private function loadDiscoveryConfig(): void
    {
        try {
            // Получаем URL издателя (Issuer) из конфигурации services.oidc.base_url
            $issuerUrl = config('services.oidc.base_url');

            // Если URL не задан, логируем предупреждение и выходим
            if (!$issuerUrl) {
                \Log::warning('OIDC_ISSUER_URL не задан в .env');
                return;
            }

            // Извлекаем экземпляр OidcDiscoveryService из контейнера
            $discovery = app(OidcDiscoveryService::class);
            // Получаем конфигурацию через discovery-эндпоинт
            $config = $discovery->getConfiguration($issuerUrl);

            // Переопределяем параметры конфигурации для OIDC
            config([
                'services.oidc.authorization_endpoint'   => $config['authorization_endpoint'], // эндпоинт авторизации
                'services.oidc.token_endpoint'             => $config['token_endpoint'],          // эндпоинт для получения токенов
                'services.oidc.userinfo_endpoint'          => $config['userinfo_endpoint'],       // эндпоинт для информации о пользователе
                'services.oidc.jwks_uri'                   => $config['jwks_uri'] ?? null,        // URI для JWKS (если есть)
                'services.oidc.end_session_endpoint'       => $config['end_session_endpoint'] ?? null, // эндпоинт для выхода (если есть)
            ]);

            // Логируем успешную загрузку
            \Log::info('OIDC Discovery успешно загружен');

        } catch (\Exception $e) {
            // В случае ошибки логируем предупреждение с сообщением исключения
            \Log::warning('OIDC Discovery не удался: ' . $e->getMessage());
        }
    }
}