<?php

declare(strict_types=1);

use Juaniquillo\LaravelModelActions\LaravelModelActionsServiceProvider;

it('registers the service provider', function () {
    $provider = $this->app->getProvider(LaravelModelActionsServiceProvider::class);

    expect($provider)->toBeInstanceOf(LaravelModelActionsServiceProvider::class);
});

it('boots the service provider', function () {
    $loadedProviders = $this->app->getLoadedProviders();

    expect($loadedProviders[LaravelModelActionsServiceProvider::class] ?? false)->toBeTrue();
});
