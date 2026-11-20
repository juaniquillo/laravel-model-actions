<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Juaniquillo\CrudAssistant\CrudAssistant;
use Juaniquillo\CrudAssistant\Inputs\TextInput;
use Juaniquillo\LaravelModelActions\Actions\LaravelQueryAction;
use Juaniquillo\LaravelModelActions\Recipes\LaravelQueryRecipe;
use Workbench\App\Models\User;

uses(RefreshDatabase::class);

it('filters model rows through the builder', function (): void {
    User::query()->forceCreate([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'secret',
    ]);

    User::query()->forceCreate([
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'secret',
    ]);

    $action = new LaravelQueryAction(User::query(), [
        'name' => 'john',
        'email' => 'example.com',
    ]);

    $collection = CrudAssistant::make([
        (new TextInput('name'))->setRecipe(new LaravelQueryRecipe(operator: 'like')),
        (new TextInput('email'))->setRecipe(new LaravelQueryRecipe(operator: 'like')),
    ]);

    $collection->execute($action);

    expect($action->getQuery()->pluck('email')->all())->toBe(['john@example.com']);
});

it('ignores empty filter values when querying model rows', function (): void {
    User::query()->forceCreate([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'secret',
    ]);

    $action = new LaravelQueryAction(User::query(), ['name' => '']);

    $collection = CrudAssistant::make([
        (new TextInput('name'))->setRecipe(new LaravelQueryRecipe(operator: 'like')),
    ]);

    $collection->execute($action);

    expect($action->getQuery()->count())->toBe(1);
});
