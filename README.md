# Modularize

The package encourage implementation of modular app.
You can easily generate modules with this package for your Laravel app.

This package support Laravel 5.3 +

## Installation

Run the following command from your projects root
```php
composer require norbybaru/modularize
```

For Laravel versions lower than 5.5, this step is important after running above script.
- Open your `config/app.php` file and add custom service provider:
```php
NorbyBaru\Modularize\ModularizeServiceProvider::class
```

## Usage

Open your terminal and run command:
```
php artisan module:generate -h 
```

You will see output with all different options to use.
Simple example will be to generate a **`user`** module directory, run command:
```php
php artisan module:generate user   
```
This will generate files with following structures:
```php
laravel/
    app/
    └── Modules/
        └── User/
            ├── Controllers/
            │   └── UserController.php
            ├── Models/
            │   └── User.php
            ├── Requests/
            │   └── UserRequest.php
            ├── Views/
            │   └── index.blade.php
            ├── Translations/
            │   └── en/
            │       └── example.php
            ├── routes
            │   ├── api.php
            │   └── web.php
            └── Helper.php
```
The package allow you to group modules as well with command:
```php
php artisan module:generate user --group=admin
```
This will output:
```php
laravel/
    app/
    └── Modules/
        └── Admin/
            └── User/
                ├── Controllers/
                │   └── UserController.php
                ├── Models/
                │   └── User.php
                ├── Requests/
                │   └── UserRequest.php
                ├── Views/
                │   └── index.blade.php
                ├── Translations/
                │   └── en/
                │       └── example.php
                ├── routes
                │   ├── api.php
                │   └── web.php
                └── Helper.php
```

Credits to:
- ["Modular Structure in Laravel 5" tutorial](http://ziyahanalbeniz.blogspot.com.tr/2015/03/modular-structure-in-laravel-5.html)
- ["Artem Schander - L5 Modular"](https://github.com/Artem-Schander/L5Modular)
