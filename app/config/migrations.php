<?php

return
[
    'paths' => [
        'migrations' => 'database/migrations',
        'seeds' => 'database/seeders'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'default_database' => 'slim_export',
        'slim_export' => [
            'adapter' => 'mysql',
            'host' => 'slim_mysql',
            'name' => 'slim_export',
            'user' => 'root',
            'pass' => 'secret',
            'port' => '3306',
            'charset' => 'utf8',
        ],
        'development' => [
            'adapter' => 'mysql',
            'host' => 'slim_mysql',
            'name' => 'slim_export',
            'user' => 'root',
            'pass' => 'secret',
            'port' => '3306',
            'charset' => 'utf8',
        ],
        'testing' => [
            'adapter' => 'mysql',
            'host' => 'slim_sql',
            'name' => 'slim_export',
            'user' => 'root',
            'pass' => 'secret',
            'port' => '3306',
            'charset' => 'utf8',
        ]
    ],
    'version_order' => 'creation'
];
