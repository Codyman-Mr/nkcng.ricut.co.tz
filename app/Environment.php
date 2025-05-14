<?php

namespace App;

use Dotenv\Dotenv;

class Environment
{
    public static function load($path)
    {
        // Get APP_ENV from server environment, CLI, or fallback
        $env = env('APP_ENV', 'local');

        // Define possible .env files in order of priority
        $files = [
            ".env.{$env}.local",
            ".env.{$env}",
            '.env.local',
            '.env',
        ];

        // Load the first existing file
        foreach ($files as $file) {
            if (file_exists($path . '/' . $file)) {
                Dotenv::createImmutable($path, $file)->safeLoad();
                return;
            }
        }
    }
}
