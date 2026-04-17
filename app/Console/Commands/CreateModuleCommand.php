<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CreateModuleCommand extends Command
{
    protected $signature = 'make:module
        {name : The module name}
        {--F|with-files : Create initial API files, model, and migration}';

    protected $description = 'Create a new module with the standard folder structure';

    public function handle(): int
    {
        $moduleName = Str::studly((string) $this->argument('name'));
        $resourceName = Str::singular($moduleName);
        $basePath = app_path("Modules/{$moduleName}");

        if (File::exists($basePath)) {
            $this->error("Module '{$moduleName}' already exists.");

            return self::FAILURE;
        }

        try {
            $this->createDirectories($basePath);
            $this->createServiceProvider($moduleName, $basePath);
            $this->createApiRoutes($moduleName, $resourceName, $basePath);
            $this->registerProvider($moduleName);

            if ($this->option('with-files')) {
                $this->createFiles($moduleName, $resourceName, $basePath);
            }
        } catch (\Throwable $throwable) {
            if (File::exists($basePath)) {
                File::deleteDirectory($basePath);
            }

            $this->error("Failed to create module: {$throwable->getMessage()}");

            return self::FAILURE;
        }

        $this->components->info("Module '{$moduleName}' created successfully.");

        return self::SUCCESS;
    }

    private function createDirectories(string $basePath): void
    {
        foreach ([
            $basePath,
            "{$basePath}/Http",
            "{$basePath}/Http/Controllers",
            "{$basePath}/Http/Requests",
            "{$basePath}/Http/Resources",
            "{$basePath}/Models",
            "{$basePath}/database",
            "{$basePath}/database/migrations",
            "{$basePath}/routes",
        ] as $directory) {
            File::makeDirectory($directory, 0755, true);
        }
    }

    private function createServiceProvider(string $moduleName, string $basePath): void
    {
        $content = <<<PHP
        <?php

        namespace App\Modules\\{$moduleName};

        use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

        class ServiceProvider extends IlluminateServiceProvider
        {
            public function register(): void
            {
                //
            }

            public function boot(): void
            {
                \$this->loadRoutesFrom(__DIR__ . '/routes/api.php');
                \$this->loadMigrationsFrom(__DIR__ . '/database/migrations');
            }
        }
        PHP;

        File::put("{$basePath}/ServiceProvider.php", $content . PHP_EOL);
    }

    private function createApiRoutes(string $moduleName, string $resourceName, string $basePath): void
    {
        $controllerClass = "{$resourceName}Controller";
        $routeSegment = Str::kebab($moduleName);
        $routeName = Str::of($moduleName)->kebab()->replace('-', '.');

        $content = <<<PHP
        <?php

        use App\Modules\\{$moduleName}\Http\Controllers\\{$controllerClass};
        use Illuminate\Support\Facades\Route;

        Route::middleware('api')
            ->prefix('api')
            ->group(function (): void {
                Route::apiResource('{$routeSegment}', {$controllerClass}::class)
                    ->names('{$routeName}');
            });
        PHP;

        File::put("{$basePath}/routes/api.php", $content . PHP_EOL);
    }

    private function registerProvider(string $moduleName): void
    {
        $providersPath = base_path('bootstrap/providers.php');
        $providerClass = "    App\\Modules\\{$moduleName}\\ServiceProvider::class,";
        $providers = File::get($providersPath);

        if (str_contains($providers, $providerClass)) {
            return;
        }

        $updatedProviders = str_replace(
            '];',
            $providerClass . PHP_EOL . '];',
            $providers,
        );

        File::put($providersPath, $updatedProviders);
    }

    private function createFiles(string $moduleName, string $resourceName, string $basePath): void
    {
        $this->call('make:model', [
            'name' => "App\\Modules\\{$moduleName}\\Models\\{$resourceName}",
            '--no-interaction' => true,
        ]);

        $this->call('make:controller', [
            'name' => "App\\Modules\\{$moduleName}\\Http\\Controllers\\{$resourceName}Controller",
            '--api' => true,
            '--model' => "App\\Modules\\{$moduleName}\\Models\\{$resourceName}",
            '--no-interaction' => true,
        ]);

        $this->call('make:request', [
            'name' => "App\\Modules\\{$moduleName}\\Http\\Requests\\Store{$resourceName}Request",
            '--no-interaction' => true,
        ]);

        $this->call('make:request', [
            'name' => "App\\Modules\\{$moduleName}\\Http\\Requests\\Update{$resourceName}Request",
            '--no-interaction' => true,
        ]);

        $this->call('make:resource', [
            'name' => "App\\Modules\\{$moduleName}\\Http\\Resources\\{$resourceName}Resource",
            '--no-interaction' => true,
        ]);

        $migrationName = 'create_' . Str::snake(Str::pluralStudly($resourceName)) . '_table';

        $this->call('make:migration', [
            'name' => $migrationName,
            '--path' => "app/Modules/{$moduleName}/database/migrations",
            '--no-interaction' => true,
        ]);

        $this->removePhpDocBlocks([
            "{$basePath}/Http/Controllers/{$resourceName}Controller.php",
            "{$basePath}/Http/Requests/Store{$resourceName}Request.php",
            "{$basePath}/Http/Requests/Update{$resourceName}Request.php",
            "{$basePath}/Http/Resources/{$resourceName}Resource.php",
            "{$basePath}/Models/{$resourceName}.php",
        ]);
    }

    /**
     * @param  array<int, string>  $files
     */
    private function removePhpDocBlocks(array $files): void
    {
        foreach ($files as $file) {
            if (! File::exists($file)) {
                continue;
            }

            $content = File::get($file);

            $content = (string) preg_replace('/\n\s*\/\*\*[\s\S]*?\*\/\n/', "\n", $content);
            $content = (string) preg_replace('/\n{3,}/', "\n\n", $content);

            File::put($file, $content);
        }
    }
}
