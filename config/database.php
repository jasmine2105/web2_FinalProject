<?php

declare(strict_types=1);

return [
    'default' => 'sqlite',

    'connections' => [

        'sqlite' => [
            'driver'   => 'sqlite',
            'database' => __DIR__ . '/../database/database.sqlite',
        ],

        'mysql' => [
            'driver'   => 'mysql',
            'host'     => '127.0.0.1',
            'port'     => '3306',
            'database' => 'mvc_framework',
            'username' => 'root',
            'password' => '',
            'charset'  => 'utf8mb4',
        ],

    ],
];
