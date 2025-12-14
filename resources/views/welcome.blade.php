<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Welcome</title>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <!-- Минимальные стили, чтобы страница не была пустой -->
        <style>
            body {
                font-family: system-ui, sans-serif;
                background-color: #f8fafc;
                color: #111827;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }
            h1 {
                font-size: 2rem;
            }
            button {
                background-color: #2563eb;
                color: #fff;
                padding: 0.5rem 1rem;
                border: none;
                border-radius: 0.375rem;
                cursor: pointer;
            }
        </style>
    @endif
</head>
<body>
    <div>
        <h1>Добро пожаловать!</h1>
        <button onclick="alert('Привет!')">Нажми меня</button>
    </div>
</body>
</html>
