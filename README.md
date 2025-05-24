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



Credits to:
- ["Modular Structure in Laravel 5" tutorial](http://ziyahanalbeniz.blogspot.com.tr/2015/03/modular-structure-in-laravel-5.html)
- ["Artem Schander - L5 Modular"](https://github.com/Artem-Schander/L5Modular)
