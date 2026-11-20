<?php

declare(strict_types=1);

use Juaniquillo\CrudAssistant\Contracts\InputInterface;
use Juaniquillo\CrudAssistant\Inputs\TextInput;
use Juaniquillo\LaravelModelActions\Actions\LaravelFillableAction;
use Juaniquillo\LaravelModelActions\Recipes\LaravelFillableRecipe;

it('uses the input name when there is no recipe', function (): void {
    $input = new TextInput('email');
    $action = new LaravelFillableAction;

    $input->execute($action);

    expect($action->getOutput()->toArray())->toBe(['email']);
});

it('uses the recipe name instead of the input name', function (): void {
    $input = (new TextInput('email'))->setRecipe(new LaravelFillableRecipe('contact_email'));
    $action = new LaravelFillableAction;

    $input->execute($action);

    expect($action->getOutput()->toArray())->toBe(['contact_email']);
});

it('uses the recipe callback result', function (): void {
    $input = (new TextInput('email'))->setRecipe(new LaravelFillableRecipe(
        callback: fn (InputInterface $input): string => 'user_'.$input->getName(),
    ));
    $action = new LaravelFillableAction;

    $input->execute($action);

    expect($action->getOutput()->toArray())->toBe(['user_email']);
});

it('prefers the recipe name over the recipe callback', function (): void {
    $input = (new TextInput('email'))->setRecipe(new LaravelFillableRecipe(
        name: 'contact_email',
        callback: fn (InputInterface $input): string => 'user_'.$input->getName(),
    ));
    $action = new LaravelFillableAction;

    $input->execute($action);

    expect($action->getOutput()->toArray())->toBe(['contact_email']);
});

it('falls back to the input name when the recipe name is empty', function (): void {
    $input = (new TextInput('email'))->setRecipe(new LaravelFillableRecipe(''));
    $action = new LaravelFillableAction;

    $input->execute($action);

    expect($action->getOutput()->toArray())->toBe(['email']);
});

it('throws when the input has no name', function (): void {
    $action = new LaravelFillableAction;

    expect(fn () => $action->execute(new TextInput))
        ->toThrow(InvalidArgumentException::class, 'The input name cannot be empty.');

    expect($action->getOutput()->toArray())->toBe([]);
});

it('throws when the input name is an empty string', function (): void {
    $action = new LaravelFillableAction;

    expect(fn () => $action->execute((new TextInput)->setName('')))
        ->toThrow(InvalidArgumentException::class, 'The input name cannot be empty.');
});
