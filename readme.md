# Laravel entity sync

Easily sync entities, when changes occur, to another project.

## Introduction

This package is designed to sync entities from one laravel project to another, something that can be useful if you have a master and client setup.

For this to work, you will need to install the client package in your other laravel project.

## Installation

1. `composer require ventrec/laravel-entity-sync`
2. Add `Ventrec\LaravelEntitySync\LaravelEntitySyncProvider::class` to providers in app.php
3. Publish the config file `php artisan vendor:publish --provider="Ventrec\LaravelEntitySync\LaravelEntitySyncProvider"`
4. Update the config file
    - Add the entities that you would like to monitor for changes to the config file.
    - Enter the full url to the endpoint where the requests should go.
    - Enter an api token that should be used to verify the requests
    
## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.