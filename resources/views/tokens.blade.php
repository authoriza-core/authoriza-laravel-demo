<!DOCTYPE html>
<html>
<head>
    <title>Токены Авторизы</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f4f4f4; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h1 { color: #333; }
        h2 { margin-top: 20px; color: #555; }
        .token-box { background: #f0f0f0; padding: 10px; border-radius: 4px; word-break: break-all; font-size: 12px; }
        pre { background: #f0f0f0; padding: 10px; border-radius: 4px; overflow-x: auto; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        td:first-child { font-weight: bold; width: 180px; }
        .success { color: green; }
        .error { color: red; }
        .warning { background: #fff3cd; color: #856404; }
        .status { margin: 20px 0; padding: 10px; border-radius: 4px; }
        .status.success { background: #d4edda; color: #155724; }
        .status.error { background: #f8d7da; color: #721c24; }
        .status.warning { background: #fff3cd; color: #856404; }
        .buttons { margin: 20px 0; }
        .buttons a { display: inline-block; padding: 10px 20px; margin-right: 10px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; }
        .buttons a:hover { background: #0056b3; }
        .buttons a.danger { background: #dc3545; }
        .buttons a.danger:hover { background: #c82333; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Данные авторизации</h1>

        {{-- Убрано уведомление об успешном обновлении --}}
        @if(session('error'))
            <div class="status error">{{ session('error') }}</div>
        @endif

        <div class="buttons">
            <a href="/auth/refresh">🔄 Обновить токены</a>
            <a href="/auth/logout" class="danger">🚪 Выйти</a>
        </div>

        <table>
            <tr><td>Статус</td><td class="success">✅ Авторизован</td></tr>
            {{-- Убрана строка "Время истечения: {{ $expires_in }} сек" --}}
            <tr><td>Access истекает</td><td id="expires_at">{{ \Carbon\Carbon::parse($expires_at)->setTimezone('Asia/Novosibirsk')->format('d.m.Y H:i:s') }}</td></tr>
            <tr><td>Последнее обновление</td><td id="last_refresh">{{ \Carbon\Carbon::parse($last_refresh)->setTimezone('Asia/Novosibirsk')->format('d.m.Y H:i:s') }}</td></tr>
        </table>

        <h2>Access Token</h2>
        <div class="token-box">{{ $access_token }}</div>

        @if($access_payload)
            <h2>Access Token Payload</h2>
            <pre>{{ json_encode($access_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        @endif

        <h2>Refresh Token</h2>
        <div class="token-box">{{ $refresh_token }}</div>

        @if($id_token)
            <h2>ID Token</h2>
            <div class="token-box">{{ $id_token }}</div>
        @endif

        @if($id_payload)
            <h2>ID Token Payload</h2>
            <pre>{{ json_encode($id_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        @endif

        @if($token_endpoint_response)
            <h2>Token Endpoint Response</h2>
            <div class="token-box">
                <pre>{{ $token_endpoint_response }}</pre>
            </div>
        @endif
    </div>
    <!-- Автоматическое обновление токенов по таймеру -->
    <script>
        function autoRefreshTokens() {
            fetch('/auth/auto-refresh')
                .then(response => {
                    if (response.status === 401) {
                        window.location.href = '/auth/login';
                        throw new Error('Session expired');
                    }
                    if (!response.ok) {
                        throw new Error('Ошибка обновления токенов');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const expiresAt = new Date(data.expires_at);
                        const lastRefresh = new Date(data.last_refresh);

                        const expiresAtEl = document.getElementById('expires_at');
                        const lastRefreshEl = document.getElementById('last_refresh');

                        if (expiresAtEl) {
                            expiresAtEl.textContent = expiresAt.toLocaleString('ru-RU', {
                                day: '2-digit', month: '2-digit', year: 'numeric',
                                hour: '2-digit', minute: '2-digit', second: '2-digit'
                            });
                        }
                        if (lastRefreshEl) {
                            lastRefreshEl.textContent = lastRefresh.toLocaleString('ru-RU', {
                                day: '2-digit', month: '2-digit', year: 'numeric',
                                hour: '2-digit', minute: '2-digit', second: '2-digit'
                            });
                        }
                    }
                })
                .catch(error => console.error('Auto-refresh error:', error));
        }
        setInterval(autoRefreshTokens, 60000);
        autoRefreshTokens();
    </script>
</body>
</html>