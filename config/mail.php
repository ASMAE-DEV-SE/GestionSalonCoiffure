<?php

return [

    'default' => env('MAIL_MAILER', 'smtp'),

    'mailers' => [

        'smtp' => [
            'transport'  => 'smtp',
            'host'       => env('MAIL_HOST', 'sandbox.smtp.mailtrap.io'),
            'port'       => env('MAIL_PORT', 2525),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'username'   => env('MAIL_USERNAME'),
            'password'   => env('MAIL_PASSWORD'),
            'timeout'    => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN'),
            // Nécessaire sur Windows (XAMPP/Laragon) pour éviter
            // les erreurs de vérification du certificat SSL de Mailtrap
            'stream' => [
                'ssl' => [
                    'allow_self_signed' => true,
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                ],
            ],
        ],

        'ses' => [
            'transport' => 'ses',
        ],

        'mailgun' => [
            'transport' => 'mailgun',
        ],

        'log' => [
            'transport' => 'log',
            'channel'   => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],

    ],

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'noreply@salonify.ma'),
        'name'    => env('MAIL_FROM_NAME', 'Salonify'),
    ],

];
