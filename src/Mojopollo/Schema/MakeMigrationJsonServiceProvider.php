<?php
namespace Mojopollo\Schema;

use Illuminate\Support\ServiceProvider;

class MakeMigrationJsonServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerMigrationJsonGenerator();
    }

    /**
     * Register the make:migration:json generator.
     *
     * @return void
     */
    private function registerMigrationJsonGenerator()
    {
        $this->app->singleton('command.mojopollo.migrate.json', function ($app) {
            return $app['Mojopollo\Schema\Commands\MakeMigrationJsonCommand'];
        });

        $this->commands('command.mojopollo.migrate.json');
    }
}
