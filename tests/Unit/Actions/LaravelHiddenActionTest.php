<?php

declare(strict_types=1);

use Juaniquillo\CrudAssistant\Contracts\InputInterface;
use Juaniquillo\CrudAssistant\Inputs\TextInput;
use Juaniquillo\LaravelModelActions\Actions\LaravelHiddenAction;
use Juaniquillo\LaravelModelActions\Recipes\LaravelHiddenRecipe;

it('uses the input name when there is no recipe', function (): void {
    $action = new LaravelHiddenAction;

    (new TextInput('password'))->execute($action);

    expect($action->getOutput()->toArray())->toBe(['password']);
});

it('uses the recipe name instead of the input name', function (): void {
    $action = new LaravelHiddenAction;

    (new TextInput('password'))->setRecipe(new LaravelHiddenRecipe('secret'))->execute($action);

    expect($action->getOutput()->toArray())->toBe(['secret']);
});

it('uses the recipe callback result', function (): void {
    $action = new LaravelHiddenAction;

    (new TextInput('password'))->setRecipe(new LaravelHiddenRecipe(
        callback: fn (InputInterface $input): string => 'hidden_'.$input->getName(),
    ))->execute($action);

    expect($action->getOutput()->toArray())->toBe(['hidden_password']);
});

it('writes nothing when the recipe is ignored', function (): void {
    $action = new LaravelHiddenAction;

    (new TextInput('password'))
        ->setRecipe((new LaravelHiddenRecipe)->ignore())
        ->execute($action);

    expect($action->getOutput()->toArray())->toBe([]);
});

it('throws when the input has no name', function (): void {
    $action = new LaravelHiddenAction;

    expect(fn () => $action->execute(new TextInput))
        ->toThrow(InvalidArgumentException::class, 'The input name cannot be empty.');
});

it('throws when the input name is an empty string', function (): void {
    $action = new LaravelHiddenAction;

    expect(fn () => $action->execute((new TextInput)->setName('')))
        ->toThrow(InvalidArgumentException::class, 'The input name cannot be empty.');
});
