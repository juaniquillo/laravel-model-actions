<?php

declare(strict_types=1);

use Juaniquillo\CrudAssistant\Contracts\RecipeInterface;
use Juaniquillo\LaravelModelActions\Actions\LaravelVisibleAction;
use Juaniquillo\LaravelModelActions\Recipes\LaravelVisibleRecipe;

it('implements the recipe contract', function (): void {
    expect(new LaravelVisibleRecipe)->toBeInstanceOf(RecipeInterface::class);
});

it('identifies itself as the visible action', function (): void {
    expect((new LaravelVisibleRecipe)->getIdentifier())->toBe(LaravelVisibleAction::class);
});

it('keeps the given name and callback', function (): void {
    $callback = fn (): string => 'visible';

    $recipe = new LaravelVisibleRecipe(name: 'name', callback: $callback);

    expect($recipe->name)->toBe('name')
        ->and($recipe->callback)->toBe($callback);
});

it('can be ignored', function (): void {
    expect((new LaravelVisibleRecipe)->ignore()->isIgnored())->toBeTrue();
});
