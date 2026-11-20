<?php

declare(strict_types=1);

use Juaniquillo\CrudAssistant\CrudAssistant;
use Juaniquillo\CrudAssistant\Inputs\TextInput;
use Juaniquillo\LaravelModelActions\Actions\LaravelFactoryAction;
use Juaniquillo\LaravelModelActions\Actions\LaravelFillableAction;
use Juaniquillo\LaravelModelActions\Recipes\LaravelFactoryRecipe;
use Juaniquillo\LaravelModelActions\Recipes\LaravelFillableRecipe;
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
