<?php
return [
    /*
     * Включить или выключить отправку сообщений в Telegram.
     */
    'enabled' => env('ENABLE_RAY_TELEGRAM', false),

    /*
     * API токен вашего Telegram бота.
     */
    'bot_token' => env('TELEGRAM_BOT_TOKEN'),

    /*
     * ID чата, куда будут приходить сообщения.
     */
    'chat_id' => env('TELEGRAM_CHAT_ID'),
];