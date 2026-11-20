<?php

declare(strict_types=1);

use Juaniquillo\CrudAssistant\Contracts\RecipeInterface;
use Juaniquillo\LaravelModelActions\Actions\LaravelHiddenAction;
use Juaniquillo\LaravelModelActions\Recipes\LaravelHiddenRecipe;

it('implements the recipe contract', function (): void {
    expect(new LaravelHiddenRecipe)->toBeInstanceOf(RecipeInterface::class);
});

it('identifies itself as the hidden action', function (): void {
    expect((new LaravelHiddenRecipe)->getIdentifier())->toBe(LaravelHiddenAction::class);
});

it('keeps the given name and callback', function (): void {
    $callback = fn (): string => 'hidden';

    $recipe = new LaravelHiddenRecipe(name: 'password', callback: $callback);

    expect($recipe->name)->toBe('password')
        ->and($recipe->callback)->toBe($callback);
});

it('can be ignored', function (): void {
    expect((new LaravelHiddenRecipe)->ignore()->isIgnored())->toBeTrue();
});
