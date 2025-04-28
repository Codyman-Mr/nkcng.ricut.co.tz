<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class BroadcastServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Log::info('Initializing broadcast routes');
        Broadcast::routes();

        require base_path('routes/channels.php');
    }
}