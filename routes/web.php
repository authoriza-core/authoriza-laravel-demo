<?php

// Импорт фасада Route для определения маршрутов
use Illuminate\Support\Facades\Route;
// Импорт контроллера аутентификации
use App\Http\Controllers\AuthController;

// Главная страница, возвращает представление welcome
Route::get('/', function () {
    return view('welcome');
});

// Публичные маршруты (доступны без аутентификации)

// Маршрут для перенаправления на провайдера OIDC (страница входа)
Route::get('/auth/login', [AuthController::class, 'redirectToProvider'])->name('login');
// Маршрут обработки callback-ответа от провайдера после аутентификации
Route::get('/auth/callback', [AuthController::class, 'handleCallback']);
// Маршрут для выхода из системы (завершения сессии)
Route::get('/auth/logout', [AuthController::class, 'logout'])->name('logout');

// Защищённые маршруты (требуют аутентификации и автообновления токенов)

// Группа маршрутов с middleware 'refresh.tokens' для обновления токенов
Route::middleware(['refresh.tokens'])->group(function () {
    // Маршрут для отображения информации о токенах
    Route::get('/auth/tokens', [AuthController::class, 'showTokens'])->name('tokens');
    // Маршрут для ручного обновления токенов
    Route::get('/auth/refresh', [AuthController::class, 'refresh'])->name('refresh');
    // Маршрут для автоматического обновления токенов (возможно, через AJAX)
    Route::get('/auth/auto-refresh', [AuthController::class, 'autoRefresh']);
});