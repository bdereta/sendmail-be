<?php

return [
    'sendmail_be_mail' => [
        'url' => env('MAIL_URL', 'https://mkt-api.gcu.edu/sendmail-be/v1/send'),
        'key' => env('MAIL_API_KEY')
    ],
];