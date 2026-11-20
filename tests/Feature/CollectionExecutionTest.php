<?php

declare(strict_types=1);

use Juaniquillo\CrudAssistant\CrudAssistant;
use Juaniquillo\CrudAssistant\Inputs\TextInput;
use Juaniquillo\LaravelModelActions\Actions\LaravelAppendsAction;
use Juaniquillo\LaravelModelActions\Actions\LaravelCastAction;
use Juaniquillo\LaravelModelActions\Actions\LaravelFactoryAction;
use Juaniquillo\LaravelModelActions\Actions\LaravelFillableAction;
use Juaniquillo\LaravelModelActions\Actions\LaravelHiddenAction;
use Juaniquillo\LaravelModelActions\Actions\LaravelResourceAction;
use Juaniquillo\LaravelModelActions\Actions\LaravelVisibleAction;
use Juaniquillo\LaravelModelActions\Recipes\LaravelAppendsRecipe;
use Juaniquillo\LaravelModelActions\Recipes\LaravelCastRecipe;
use Juaniquillo\LaravelModelActions\Recipes\LaravelFactoryRecipe;
use Juaniquillo\LaravelModelActions\Recipes\LaravelFillableRecipe;
use Juaniquillo\LaravelModelActions\Recipes\LaravelHiddenRecipe;
use Juaniquillo\LaravelModelActions\Recipes\LaravelResourceRecipe;
use Juaniquillo\LaravelModelActions\Recipes\LaravelVisibleRecipe;
use Juaniquillo\LaravelModelActions\Tests\Fixtures\SeededFaker;

it('resolves fillable names across a collection', function (): void {
    $collection = CrudAssistant::make([
        new TextInput('name'),
        (new TextInput('email'))->setRecipe(new LaravelFillableRecipe('contact_email')),
    ]);

    expect($collection->execute(new LaravelFillableAction)->toArray())
        ->toBe(['name', 'contact_email']);
});

it('skips fillable inputs whose recipe is ignored', function (): void {
    $collection = CrudAssistant::make([
        new TextInput('name'),
        (new TextInput('secret'))->setRecipe((new LaravelFillableRecipe)->ignore()),
    ]);

    expect($collection->execute(new LaravelFillableAction)->toArray())
        ->toBe(['name']);
});

it('generates fake data across a collection', function (): void {
    $collection = CrudAssistant::make([
        new TextInput('name'),
        (new TextInput('email'))->setRecipe(new LaravelFactoryRecipe('safeEmail')),
    ]);

    $output = $collection->execute(new LaravelFactoryAction(SeededFaker::create()))->toArray();

    expect($output)->toHaveKeys(['name', 'email'])
        ->and($output['email'])->toBeString()->toContain('@');
});

it('fails the whole run when a collection contains an unnamed input', function (): void {
    $collection = CrudAssistant::make([
        new TextInput('email'),
        new TextInput,
    ]);

    expect(fn () => $collection->execute(new LaravelFillableAction))
        ->toThrow(InvalidArgumentException::class, 'The input name cannot be empty.');
});

it('resolves casts across a collection', function (): void {
    $collection = CrudAssistant::make([
        new TextInput('name'),
        (new TextInput('settings'))->setRecipe(new LaravelCastRecipe('array')),
        (new TextInput('published_at'))->setRecipe(new LaravelCastRecipe('datetime')),
    ]);

    expect($collection->execute(new LaravelCastAction)->toArray())
        ->toBe(['settings' => 'array', 'published_at' => 'datetime']);
});

it('resolves hidden attributes across a collection', function (): void {
    $collection = CrudAssistant::make([
        new TextInput('name'),
        (new TextInput('password'))->setRecipe(new LaravelHiddenRecipe('password')),
    ]);

    expect($collection->execute(new LaravelHiddenAction)->toArray())
        ->toBe(['name', 'password']);
});

it('resolves visible attributes across a collection', function (): void {
    $collection = CrudAssistant::make([
        new TextInput('name'),
        (new TextInput('email'))->setRecipe(new LaravelVisibleRecipe('contact_email')),
    ]);

    expect($collection->execute(new LaravelVisibleAction)->toArray())
        ->toBe(['name', 'contact_email']);
});

it('resolves appended attributes across a collection', function (): void {
    $collection = CrudAssistant::make([
        new TextInput('full_name'),
        (new TextInput('avatar'))->setRecipe(new LaravelAppendsRecipe('avatar_url')),
    ]);

    expect($collection->execute(new LaravelAppendsAction)->toArray())
        ->toBe(['full_name', 'avatar_url']);
});

it('maps resource keys across a collection', function (): void {
    $collection = CrudAssistant::make([
        new TextInput('name'),
        (new TextInput('email'))->setRecipe(new LaravelResourceRecipe('contact_email')),
    ]);

    expect($collection->execute(new LaravelResourceAction)->toArray())
        ->toBe(['name' => 'name', 'contact_email' => 'email']);
});
