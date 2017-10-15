<?php

namespace Ventrec\LaravelEntitySync;

use Illuminate\Support\ServiceProvider;
use Ventrec\LaravelEntitySync\Observers\EntityObserver;

class LaravelEntitySyncProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../publishes/config/laravelEntitySync.php' => config_path('laravelEntitySync.php'),
        ], 'config');

        // Only run if app is not running in console and package is enabled
        if (!app()->runningInConsole() and config('laravelEntitySync.enabled')) {
            /**
             * Register event listener for entities defined in the config file
             * We want to listen to created, updated and deleted events.
             */
            $entities = config('laravelEntitySync.entities');

            foreach ($entities as $entity) {
                app($entity)->observe(new EntityObserver);
            }
        }
    }
}
