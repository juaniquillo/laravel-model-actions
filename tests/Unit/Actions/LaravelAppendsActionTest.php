<?php

declare(strict_types=1);

use Juaniquillo\CrudAssistant\Inputs\TextInput;
use Juaniquillo\LaravelModelActions\Actions\LaravelAppendsAction;
use Juaniquillo\LaravelModelActions\Recipes\LaravelAppendsRecipe;

it('uses the input name when there is no recipe', function (): void {
    $action = new LaravelAppendsAction;

    (new TextInput('full_name'))->execute($action);

    expect($action->getOutput()->toArray())->toBe(['full_name']);
});

it('uses the recipe name instead of the input name', function (): void {
    $action = new LaravelAppendsAction;

    (new TextInput('full_name'))->setRecipe(new LaravelAppendsRecipe('display_name'))->execute($action);

    expect($action->getOutput()->toArray())->toBe(['display_name']);
});

it('writes nothing when the recipe is ignored', function (): void {
    $action = new LaravelAppendsAction;

    (new TextInput('full_name'))
        ->setRecipe((new LaravelAppendsRecipe)->ignore())
        ->execute($action);

    expect($action->getOutput()->toArray())->toBe([]);
});

it('throws when the input has no name', function (): void {
    $action = new LaravelAppendsAction;

    expect(fn () => $action->execute(new TextInput))
        ->toThrow(InvalidArgumentException::class, 'The input name cannot be empty.');
});

it('throws when the input name is an empty string', function (): void {
    $action = new LaravelAppendsAction;

    expect(fn () => $action->execute((new TextInput)->setName('')))
        ->toThrow(InvalidArgumentException::class, 'The input name cannot be empty.');
});
