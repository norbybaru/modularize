# Modularize

**Modularize** helps you organize large Laravel applications into self-contained modules — each with its own controllers, models, migrations, routes, and service providers.

Instead of scattering related code across `app/Http/Controllers`, `app/Models`, and `database/migrations`, you group everything by feature: `modules/Auth/`, `modules/Billing/`, `modules/Notifications/`. Each module is independently navigable, testable, and replaceable — without changing how Laravel loads your application.

## Requirements

- PHP 8.2+
- Laravel 10, 11, or 12

## Installation

```bash
composer require norbybaru/modularize
```

## Configuration

Publish the package configuration:

```bash
php artisan vendor:publish --provider="NorbyBaru\Modularize\ModularizeServiceProvider"
```

### Autoloading

Add the `Modules` namespace to your `composer.json` and run `composer dump-autoload`:

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Modules\\": "modules/"
        }
    }
}
```

### Config options

The published config file (`config/modularize.php`) exposes the following options:

| Option | Default | Description |
|---|---|---|
| `enable` | `true` | Autoload all modules found in `root_path` |
| `root_path` | `modules` | Root directory where modules are created |
| `autoload_routes` | `true` | Automatically register routes from each module's `Routes/` directory |
| `autoload_service_provider` | `true` | Automatically register each module's `Providers/<ModuleName>ServiceProvider.php` |

When `enable` is `true`, modules are automatically discovered and booted — no manual registration required.

## Creating Your First Module

Generate a model with all related files in one command:

```bash
php artisan module:make:model User --module=Auth --all
```

This creates the following structure:

```
modules/
└── Auth/
    ├── Controllers/
    │   └── UserController.php
    ├── Database/
    │   ├── Factories/
    │   │   └── UserFactory.php
    │   ├── Migrations/
    │   │   └── <timestamp>_create_users_table.php
    │   └── Seeders/
    │       └── UserSeeder.php
    ├── Models/
    │   └── User.php
    └── Policies/
        └── UserPolicy.php
```

## Command Reference

### Module Management

| Command | Description |
|---|---|
| `module:list` | List all existing modules |

### Generators

All generator commands accept `--module=<name>` to specify the target module and `--force` to overwrite an existing file. If `--module` is omitted, you will be prompted interactively with autocomplete.

| Command | Key Options | Description |
|---|---|---|
| `module:make:model` | `-a/--all`, `-m/--migration`, `-f/--factory`, `-s/--seed`, `-c/--controller`, `--policy`, `--api`, `-r/--resource`, `-i/--invokable`, `-p/--pivot` | Generate a model; `--all` also creates a migration, factory, seeder, policy, and resource controller |
| `module:make:controller` | `--api`, `-r/--resource`, `-i/--invokable`, `-m/--model=` | Generate a controller (`--api` omits `create` and `edit` methods) |
| `module:make:migration` | `--create=`, `--table=` | Generate a migration |
| `module:make:factory` | `--model=` | Generate a model factory |
| `module:make:seeder` | — | Generate a seeder |
| `module:make:policy` | `--model=` | Generate a policy |
| `module:make:request` | — | Generate a form request |
| `module:make:resource` | — | Generate an API resource |
| `module:make:middleware` | — | Generate middleware |
| `module:make:event` | — | Generate an event |
| `module:make:listener` | — | Generate a listener |
| `module:make:job` | — | Generate a job |
| `module:make:mail` | — | Generate a mailable |
| `module:make:notification` | — | Generate a notification |
| `module:make:provider` | — | Generate a service provider |
| `module:make:component` | — | Generate a view component |
| `module:make:console` | — | Generate an Artisan command |
| `module:make:view` | — | Generate a Blade view |
| `module:make:test` | — | Generate a test class |

## Contributing

Contributions are welcome. Fork the repository, create a branch off `master`, and open a pull request.

## License

MIT — see [LICENSE.md](LICENSE.md).
