<?php

return [
    'default' => env('MAIL_MAILER', 'smtp'),
    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST', 'smtp.gmail.com'),
            'port' => env('MAIL_PORT', 587),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),  // Chỉnh sửa tại đây, không cần 'scheme'
            'timeout' => null,
        ],
        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],
        // Các mailer khác ...
    ],
    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hoangkhai5504@gmail.com'),
        'name' => env('MAIL_FROM_NAME', 'Admin'),
    ],
];


