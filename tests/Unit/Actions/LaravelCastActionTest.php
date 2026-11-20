<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Casts\AsStringable;
use Juaniquillo\CrudAssistant\Contracts\DataContainerInterface;
use Juaniquillo\CrudAssistant\Contracts\InputInterface;
use Juaniquillo\CrudAssistant\Inputs\TextInput;
use Juaniquillo\LaravelModelActions\Actions\LaravelCastAction;
use Juaniquillo\LaravelModelActions\Recipes\LaravelCastRecipe;

it('skips inputs without a recipe', function (): void {
    $action = new LaravelCastAction;

    (new TextInput('name'))->execute($action);

    expect($action->getOutput()->toArray())->toBe([]);
});

it('uses the recipe cast', function (): void {
    $action = new LaravelCastAction;

    (new TextInput('settings'))->setRecipe(new LaravelCastRecipe('array'))->execute($action);

    expect($action->getOutput()->toArray())->toBe(['settings' => 'array']);
});

it('delegates to the recipe callback', function (): void {
    $action = new LaravelCastAction;

    $recipe = new LaravelCastRecipe(
        callback: function (InputInterface $input, DataContainerInterface $output): void {
            $output->set($input->getName(), AsStringable::class);
        },
    );

    (new TextInput('published_at'))->setRecipe($recipe)->execute($action);

    expect($action->getOutput()->toArray())->toBe(['published_at' => AsStringable::class]);
});

it('falls back to the callback when the recipe cast is empty', function (): void {
    $action = new LaravelCastAction;

    $recipe = new LaravelCastRecipe(
        cast: '',
        callback: function (InputInterface $input, DataContainerInterface $output): void {
            $output->set($input->getName(), 'datetime');
        },
    );

    (new TextInput('published_at'))->setRecipe($recipe)->execute($action);

    expect($action->getOutput()->toArray())->toBe(['published_at' => 'datetime']);
});

it('writes nothing when the recipe is ignored', function (): void {
    $action = new LaravelCastAction;

    (new TextInput('settings'))
        ->setRecipe((new LaravelCastRecipe('array'))->ignore())
        ->execute($action);

    expect($action->getOutput()->toArray())->toBe([]);
});

it('throws when the input has no name', function (): void {
    $action = new LaravelCastAction;

    expect(fn () => $action->execute(new TextInput))
        ->toThrow(InvalidArgumentException::class, 'The input name cannot be empty.');
});

it('throws when the input name is an empty string', function (): void {
    $action = new LaravelCastAction;

    expect(fn () => $action->execute((new TextInput)->setName('')))
        ->toThrow(InvalidArgumentException::class, 'The input name cannot be empty.');
});
