<?php
return [
    'bot_token'        => env('TELEGRAM_BOT_TOKEN', ''),
    'allowed_chat_ids' => array_filter(explode(',', env('TELEGRAM_ALLOWED_CHAT_IDS', ''))),
];
