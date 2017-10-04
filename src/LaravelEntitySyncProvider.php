<?php

namespace Ventrec\LaravelEntitySync;

use Illuminate\Support\ServiceProvider;

class LaravelEntitySyncProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../publishes/config/laravelEntitySync.php' => config_path('laravelEntitySync.php'),
        ], 'config');
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        /**
         * Register event listener for entities defined in the config file
         * We want to listen to created, updated and deleted events.
         */
        $entities = config('laravelEntitySync.entities');

        foreach ($entities as $entity) {
            // Do we need a static call here instead?
            app($entity)->observe();
        }
    }
}
