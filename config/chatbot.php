<?php

return [
    'provider' => env('CHATBOT_AI_PROVIDER', 'gemini'),
    'api_key' => env('GEMINI_API_KEY'),
    'model' => env('GEMINI_MODEL', 'gemini-3.6-flash'),
];
