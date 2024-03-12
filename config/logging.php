<?php

use Monolog\Handler\FilterHandler;
use Monolog\Handler\TelegramBotHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog", "errorlog",
    |                    "monolog", "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['daily'],
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/lumen.log'), // Log file location
            'level' => 'debug', // Log level
            'days' => 14, // Number of days to keep log files
        ],

        'telegram' => [
            'driver'  => 'monolog',
            'handler' => FilterHandler::class,
            'level' => env('LOG_LEVEL', 'debug'),
            'with' => [
                'handler' => new TelegramBotHandler($apiKey = env('TELEGRAM_API_KEY'), $channel = env('TELEGRAM_CHANNEL'))

            ]
        ]

    ],

    // ...

];
