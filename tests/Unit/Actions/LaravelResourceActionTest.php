<?php

declare(strict_types=1);

use Juaniquillo\CrudAssistant\Contracts\InputInterface;
use Juaniquillo\CrudAssistant\Inputs\TextInput;
use Juaniquillo\LaravelModelActions\Actions\LaravelResourceAction;
use Juaniquillo\LaravelModelActions\Recipes\LaravelResourceRecipe;

it('maps the input name to itself when there is no recipe', function (): void {
    $action = new LaravelResourceAction;

    (new TextInput('email'))->execute($action);

    expect($action->getOutput()->toArray())->toBe(['email' => 'email']);
});

it('maps the recipe key to the input name', function (): void {
    $action = new LaravelResourceAction;

    (new TextInput('email'))->setRecipe(new LaravelResourceRecipe('contact_email'))->execute($action);

    expect($action->getOutput()->toArray())->toBe(['contact_email' => 'email']);
});

it('uses the recipe callback as the key', function (): void {
    $action = new LaravelResourceAction;

    (new TextInput('email'))->setRecipe(new LaravelResourceRecipe(
        callback: fn (InputInterface $input): string => 'user_'.$input->getName(),
    ))->execute($action);

    expect($action->getOutput()->toArray())->toBe(['user_email' => 'email']);
});

it('prefers the recipe key over the recipe callback', function (): void {
    $action = new LaravelResourceAction;

    (new TextInput('email'))->setRecipe(new LaravelResourceRecipe(
        key: 'contact_email',
        callback: fn (InputInterface $input): string => 'user_'.$input->getName(),
    ))->execute($action);

    expect($action->getOutput()->toArray())->toBe(['contact_email' => 'email']);
});

it('falls back to the input name when the recipe key is empty', function (): void {
    $action = new LaravelResourceAction;

    (new TextInput('email'))->setRecipe(new LaravelResourceRecipe(''))->execute($action);

    expect($action->getOutput()->toArray())->toBe(['email' => 'email']);
});

it('writes nothing when the recipe is ignored', function (): void {
    $action = new LaravelResourceAction;

    (new TextInput('email'))
        ->setRecipe((new LaravelResourceRecipe)->ignore())
        ->execute($action);

    expect($action->getOutput()->toArray())->toBe([]);
});

it('throws when the input has no name', function (): void {
    $action = new LaravelResourceAction;

    expect(fn () => $action->execute(new TextInput))
        ->toThrow(InvalidArgumentException::class, 'The input name cannot be empty.');
});

it('throws when the input name is an empty string', function (): void {
    $action = new LaravelResourceAction;

    expect(fn () => $action->execute((new TextInput)->setName('')))
        ->toThrow(InvalidArgumentException::class, 'The input name cannot be empty.');
});
