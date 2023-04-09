<?php

namespace SoftHouse\CodeGenerate;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class CodeGenerateServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Blueprint::macro('codeGenerate', function () {
            $this->string('code')->nullable();
        });
    }

    public function register(): void
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'code-generate');

        // Register the main class to use with the facade
        $this->app->singleton('code-generate', function () {
            return new CodeGenerate;
        });
    }
}
