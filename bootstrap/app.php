<?php 

// Импорт класса Application для настройки Laravel-приложения
use Illuminate\Foundation\Application;
// Импорт класса Exceptions для настройки обработки исключений
use Illuminate\Foundation\Configuration\Exceptions;
// Импорт класса Middleware для настройки промежуточного ПО
use Illuminate\Foundation\Configuration\Middleware;
// Импорт собственного middleware для обновления токенов
use App\Http\Middleware\RefreshTokens;

// Создание и настройка экземпляра приложения, передача базового пути (корневая директория)
return Application::configure(basePath: dirname(__DIR__))
    // Настройка маршрутов: веб-маршруты из файла web.php
    ->withRouting(
        web: __DIR__.'/../routes/web.php', // Путь к файлу веб-маршрутов
        commands: __DIR__.'/../routes/console.php', // Путь к консольным маршрутам
        health: '/up', // URL для проверки работоспособности приложения
    )
    // Настройка middleware через замыкание
    ->withMiddleware(function (Middleware $middleware) {
        // Добавление алиаса (псевдонима) для middleware, чтобы использовать 'refresh.tokens' в маршрутах
        $middleware->alias([
            'refresh.tokens' => RefreshTokens::class,
        ]);
    })
    // Настройка обработки исключений (пока пусто)
    ->withExceptions(function (Exceptions $exceptions) {
        // Никаких дополнительных настроек
        //
    })
    // Создание и возврат готового экземпляра приложения
    ->create();