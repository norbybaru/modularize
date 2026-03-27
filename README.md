# Modularize

The package encourage implementation of modular pattern for your Laravel project.
You can easily start your modular journey with this simple package and generate only files you need.

A module is like a Laravel package, it has some views, controllers or models.

## Installation

Run the following command from your projects root
```php
composer require norbybaru/modularize
```
## Configuration
Publish the package configuration using the following command:
```php
php artisan vendor:publish --provider="NorbyBaru\Modularize\ModularizeServiceProvider"
```

### Autoloading
The default namespace is set as Modules this will apply the namespace for all classes the module will use when it's being created and later when generation additional classes.

For autoloading modules, add the following to your composer.json and execute composer dump-autoload:

```php
{
  "autoload": {
    "psr-4": {
      "App\\": "app/",
      "Modules\\": "modules/"
    }
  }
}
```

## Basic Usage

### Create module
Open your terminal and run command to list all possible commands:
```
php artisan module:make:
```

## Advance Usage

### Generate Controller

Create a controller for a module using the `module:make:controller` command.

#### Basic Controller

Generate a plain controller:

```bash
php artisan module:make:controller UserController --module=User
```

This creates a basic controller class in `modules/User/Controllers/UserController.php`.

#### Controller Options

##### API Controller (`--api`)

Generate an API controller without `create` and `edit` methods:

```bash
php artisan module:make:controller ApiUserController --module=User --api
```

##### Invokable Controller (`--invokable` or `-i`)

Generate a single-action controller with an `__invoke` method:

```bash
php artisan module:make:controller ProcessPayment --module=Payment --invokable
```

##### Resource Controller (`--resource` or `-r`)

Generate a resource controller with all CRUD methods:

```bash
php artisan module:make:controller ProductController --module=Product --resource
```

##### Model-Based Resource Controller (`--model` or `-m`)

Generate a resource controller with type-hinted model:

```bash
php artisan module:make:controller OrderController --module=Order --model=Order
```

##### Force Creation (`--force`)

Overwrite existing controller:

```bash
php artisan module:make:controller UserController --module=User --force
```

#### Combined Options

You can combine multiple options:

```bash
# API resource controller with model
php artisan module:make:controller ProductController --module=Product --api --model=Product

# Invokable controller (force overwrite)
php artisan module:make:controller SendNotification --module=Notification --invokable --force
```

Credits to:
- ["Modular Structure in Laravel 5" tutorial](http://ziyahanalbeniz.blogspot.com.tr/2015/03/modular-structure-in-laravel-5.html)
- ["Artem Schander - L5 Modular"](https://github.com/Artem-Schander/L5Modular)
