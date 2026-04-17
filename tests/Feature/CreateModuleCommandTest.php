<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class CreateModuleCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        File::deleteDirectory(app_path('Modules/TestProjects'));

        $providersPath = base_path('bootstrap/providers.php');
        $providers = File::get($providersPath);

        $providers = str_replace(
            "    App\\Modules\\TestProjects\\ServiceProvider::class,\n",
            '',
            $providers,
        );

        File::put($providersPath, $providers);

        parent::tearDown();
    }

    public function test_it_creates_a_module_with_api_first_files(): void
    {
        $this->artisan('make:module', [
            'name' => 'test-projects',
            '--with-files' => true,
        ])->assertSuccessful();

        $this->assertFileExists(app_path('Modules/TestProjects/ServiceProvider.php'));
        $this->assertFileExists(app_path('Modules/TestProjects/routes/api.php'));
        $this->assertFileExists(app_path('Modules/TestProjects/Http/Controllers/TestProjectController.php'));
        $this->assertFileExists(app_path('Modules/TestProjects/Http/Requests/StoreTestProjectRequest.php'));
        $this->assertFileExists(app_path('Modules/TestProjects/Http/Requests/UpdateTestProjectRequest.php'));
        $this->assertFileExists(app_path('Modules/TestProjects/Http/Resources/TestProjectResource.php'));
        $this->assertFileExists(app_path('Modules/TestProjects/Models/TestProject.php'));
        $this->assertNotEmpty(glob(app_path('Modules/TestProjects/database/migrations/*.php')));

        $providers = File::get(base_path('bootstrap/providers.php'));

        $this->assertStringContainsString(
            'App\\Modules\\TestProjects\\ServiceProvider::class',
            $providers,
        );

        $routes = File::get(app_path('Modules/TestProjects/routes/api.php'));

        $this->assertStringContainsString("Route::apiResource('test-projects'", $routes);
    }
}
