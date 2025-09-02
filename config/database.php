<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Directly force PostgreSQL as the default connection.
    |
    */

    'default' => 'pgsql',

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are all connections. SQLite and others are left intact.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => null,
            'database' => database_path('database.sqlite'),
            'prefix' => '',
            'foreign_key_constraints' => true,
        ],

        'mysql' => [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'nkcng',
            'username' => 'root',
            'password' => '',
            'unix_socket' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => null,
            ]) : [],
        ],

        'mariadb' => [
            'driver' => 'mariadb',
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'laravel',
            'username' => 'root',
            'password' => '',
            'unix_socket' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => null,
            ]) : [],
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'host' => 'dpg-d2qraeumcj7s73cg9gl0-a.frankfurt-postgres.render.com',
            'port' => '5432',
            'database' => 'nkcng',
            'username' => 'nkcng_user',
            'password' => 'cHxNnfBmI2gTiCLWbPPgb809n2ChJza8',
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'prefer',
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'host' => '127.0.0.1',
            'port' => '1433',
            'database' => 'nkcng',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    */

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    */

    'redis' => [

        'client' => 'phpredis',

        'options' => [
            'cluster' => 'redis',
            'prefix' => Str::slug('Nkcng', '_').'_database_',
        ],

        'default' => [
            'host' => '127.0.0.1',
            'username' => null,
            'password' => null,
            'port' => '6379',
            'database' => 0,
        ],

        'cache' => [
            'host' => '127.0.0.1',
            'username' => null,
            'password' => null,
            'port' => '6379',
            'database' => 1,
        ],

    ],

];
