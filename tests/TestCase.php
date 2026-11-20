<?php

declare(strict_types=1);

namespace Juaniquillo\LaravelModelActions\Tests;

use Illuminate\Foundation\Application;
use Juaniquillo\LaravelModelActions\LaravelModelActionsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * @param  Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            LaravelModelActionsServiceProvider::class,
        ];
    }
}
