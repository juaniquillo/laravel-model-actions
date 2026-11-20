<?php

declare(strict_types=1);

use Juaniquillo\CrudAssistant\Inputs\TextInput;
use Juaniquillo\LaravelModelActions\Actions\LaravelVisibleAction;
use Juaniquillo\LaravelModelActions\Recipes\LaravelVisibleRecipe;

it('uses the input name when there is no recipe', function (): void {
    $action = new LaravelVisibleAction;

    (new TextInput('name'))->execute($action);

    expect($action->getOutput()->toArray())->toBe(['name']);
});

it('uses the recipe name instead of the input name', function (): void {
    $action = new LaravelVisibleAction;

    (new TextInput('name'))->setRecipe(new LaravelVisibleRecipe('display_name'))->execute($action);

    expect($action->getOutput()->toArray())->toBe(['display_name']);
});

it('writes nothing when the recipe is ignored', function (): void {
    $action = new LaravelVisibleAction;

    (new TextInput('name'))
        ->setRecipe((new LaravelVisibleRecipe)->ignore())
        ->execute($action);

    expect($action->getOutput()->toArray())->toBe([]);
});

it('throws when the input has no name', function (): void {
    $action = new LaravelVisibleAction;

    expect(fn () => $action->execute(new TextInput))
        ->toThrow(InvalidArgumentException::class, 'The input name cannot be empty.');
});

it('throws when the input name is an empty string', function (): void {
    $action = new LaravelVisibleAction;

    expect(fn () => $action->execute((new TextInput)->setName('')))
        ->toThrow(InvalidArgumentException::class, 'The input name cannot be empty.');
});
