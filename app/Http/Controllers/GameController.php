<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GameController extends Controller
{
    public function index()
    {
        return view('game');
    }

    public function sendTelegramMessage(Request $request)
    {
        $message = $request->input('message');
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        // Детальная проверка конфигурации
        if (empty($botToken)) {
            Log::error('Telegram bot token is empty');
            return response()->json([
                'success' => false,
                'error' => 'TELEGRAM_BOT_TOKEN не настроен в .env файле'
            ], 400);
        }

        if (empty($chatId)) {
            Log::error('Telegram chat ID is empty');
            return response()->json([
                'success' => false,
                'error' => 'TELEGRAM_CHAT_ID не настроен в .env файле'
            ], 400);
        }

        try {
            $telegramUrl = "https://api.telegram.org/bot{$botToken}/sendMessage";
            
            Log::info('Отправка сообщения в Telegram', [
                'url' => $telegramUrl,
                'chat_id' => $chatId,
                'message' => $message
            ]);

            // Отключаем проверку SSL для локальной разработки
            $response = Http::timeout(10)
                ->withoutVerifying()
                ->post($telegramUrl, [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'HTML'
                ]);

            if ($response->successful()) {
                $responseData = $response->json();
                Log::info('Сообщение успешно отправлено в Telegram', [
                    'response' => $responseData
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Сообщение отправлено'
                ]);
            } else {
                $errorBody = $response->body();
                $statusCode = $response->status();
                
                Log::error('Telegram API error', [
                    'status' => $statusCode,
                    'response' => $errorBody,
                    'chat_id' => $chatId
                ]);

                // Парсим ошибку от Telegram API
                $errorData = $response->json();
                $errorMessage = $errorData['description'] ?? 'Failed to send message';
                
                // Полезные сообщения для пользователя
                if (str_contains($errorMessage, 'chat not found')) {
                    $errorMessage = 'Чат не найден. Убедитесь, что вы написали боту первым сообщение (/start)';
                } elseif (str_contains($errorMessage, 'Unauthorized')) {
                    $errorMessage = 'Неверный токен бота. Проверьте TELEGRAM_BOT_TOKEN в .env';
                } elseif (str_contains($errorMessage, 'Forbidden')) {
                    $errorMessage = 'Бот заблокирован или не имеет доступа к чату';
                }

                return response()->json([
                    'success' => false,
                    'error' => $errorMessage,
                    'details' => $errorBody
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Telegram send error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Ошибка соединения: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generatePromoCode()
    {
        // Генерируем 5-значный промокод
        $promoCode = strtoupper(substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 5));
        
        return response()->json([
            'promo_code' => $promoCode
        ]);
    }
}

