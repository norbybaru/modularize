<?php

return [

    /**
     * By enabling modular config, the package will autoload all modules found in the root_path directory
     */
    'enable' => true,

    /**
     * Define application root directory folder to create modules files
     */
    'root_path' => 'modules',

    /**
     * Routes created under the Routes/ directory of a module would be autoload to be discovered by the application.
     * Setting 'autoload_routes => false' will require manually registering module routes through a service provider.
     */
    'autoload_routes' => true,

    /**
     * Setting value to false will prevent autloading of module service provider. Eg. ModuleNameServiceProvider
     */
    'autoload_service_provider' => true,

    /**
     * Enable caching of module discovery results to improve boot performance.
     * When enabled, module discovery results are cached to avoid repeated filesystem scans on every request.
     * Use 'php artisan modularize:cache' to generate cache and 'php artisan modularize:clear' to clear it.
     */
    'cache_enabled' => true,

    /**
     * Define the path where the module discovery cache file will be stored.
     * Relative to application base path. Defaults to Laravel's bootstrap/cache directory.
     */
    'cache_path' => 'bootstrap/cache/modularize.php',
];
