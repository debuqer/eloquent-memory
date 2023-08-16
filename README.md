# Let the eloquent remember its current state of data

[![Latest Version on Packagist](https://img.shields.io/packagist/v/debuqer/eloquent-memory.svg?style=flat-square)](https://packagist.org/packages/debuqer/eloquent-memory)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/debuqer/eloquent-memory/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/debuqer/eloquent-memory/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/debuqer/eloquent-memory/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/debuqer/eloquent-memory/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/debuqer/eloquent-memory.svg?style=flat-square)](https://packagist.org/packages/debuqer/eloquent-memory)

Eloquent memory give you a Laravel model based time machine to perform time traveling through your models state. 
```php 
$article = Article::find(5);

// 5 minutes later

$article->update([
    // update fields
]);

$article->delete(); // delete the article

// lets go back to the old time
$oldArticle = Article::find(5)->getStateOf(Carbon::now()->subMinutes(5)); // How was the model looked 5 minutes ago   
$oldArticle->save(); // Boom! This is like time-machine 
```

## Installation

You can install the package via composer:

```bash
composer require debuqer/eloquent-memory
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="eloquent-memory-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="eloquent-memory-config"
```

This is the contents of the published config file:

```php
return [
    'changes' => [
        'model-updated' => ModelUpdated::class,
        'model-created' => ModelCreated::class,
        'model-deleted' => ModelDeleted::class,
    ],
    'drivers' => [
        'default' => 'eloquent',

        'eloquent' => [
            'class_name' => \Debuqer\EloquentMemory\Repositories\Eloquent\EloquentTransitionPersistDriver::class,
            'connection' => 'default',
        ],
    ],
];
```

## Usage
In order to force models to keep track of their state, CanRememberStates trait must be used in the model class 

```php

use Debuqer\EloquentMemory\CanRememberStates;

class Post extends Model 
{
    use CanRememberStates;
}

```

The model records their states in a database and the states can be retrieved by method getStateOf

```php
$oldArticle = Article::find(5)->getStateOf(Carbon::now()->subMinutes(5));
```

## Testing

```bash
composer test
```

## Changelog

This package is in dev mode and not recommend to use in production environment

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [debuqer](https://github.com/debuqer)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
