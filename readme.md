# Laravel entity sync

Easily sync entities, when changes occur, to another project.

**WARNING**

This package is still under development. Breaking changes will occur. Use at your own risk.

## Introduction

This package is designed to sync entities from one laravel project to another, something that can be useful if you have a master and client setup.

For this to work, you will need to install the [client package](https://github.com/ventrec/laravel-entity-sync-endpoint) in the laravel project that you wish to sync your entities to.

## Installation

1. `composer require ventrec/laravel-entity-sync`
2. Add `Ventrec\LaravelEntitySync\LaravelEntitySyncProvider::class` to providers in app.php
3. Publish the config file `php artisan vendor:publish --provider="Ventrec\LaravelEntitySync\LaravelEntitySyncProvider"`
4. Update the config file
    - Add the entities that you would like to monitor for changes to the config file.
    - Enter the full url to the endpoint where the requests should go.
    - Enter an api token that should be used to verify the requests
    
## Usage

In some cases you might have attributes on a model that you do not want to sync. For this you can define a method named `ignoreSyncAttributes` that returns an array containing the name of attributes you do not want to sync.

**Example**

In a User model you might want to exclude the password:
```php
public function ignoreSyncAttributes()
{
    return ['password'];
}
```

### Prevent observer from running while seeding

In order to prevent the observer from running while seeding, you have to disable the package in runtime.

In your `DatabaseSeeder` class, add the following line at the top of the `run()` method:

```php
config(['laravelEntitySync.enabled' => false]);
```
    
## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.