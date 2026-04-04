<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramNotificationService
{
    protected $botToken;
    protected $channels;

    public function __construct()
    {
        $this->botToken = env('TELEGRAM_BOT_TOKEN');
        $this->channels = [
            'new_orders' => env('TELEGRAM_CHANNEL_NEW_ORDERS'),
            'updates' => env('TELEGRAM_CHANNEL_UPDATES'),
            'reports' => env('TELEGRAM_CHANNEL_REPORTS'),
        ];
    }

    public function send(string $message, string $channelType = 'updates')
    {
        if (!$this->botToken || !isset($this->channels[$channelType]) || !$this->channels[$channelType]) {
            Log::warning("Telegram not configured for channel type: {$channelType}");
            return false;
        }

        $chatId = $this->channels[$channelType];
        $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";

        try {
            $response = Http::post($url, [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML'
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Telegram notification failed: " . $e->getMessage());
            return false;
        }
    }
}
