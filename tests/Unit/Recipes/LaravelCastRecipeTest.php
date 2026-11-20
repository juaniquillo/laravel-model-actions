<?php

declare(strict_types=1);

use Juaniquillo\CrudAssistant\Contracts\RecipeInterface;
use Juaniquillo\LaravelModelActions\Actions\LaravelCastAction;
use Juaniquillo\LaravelModelActions\Recipes\LaravelCastRecipe;

it('implements the recipe contract', function (): void {
    expect(new LaravelCastRecipe)->toBeInstanceOf(RecipeInterface::class);
});

it('identifies itself as the cast action', function (): void {
    expect((new LaravelCastRecipe)->getIdentifier())->toBe(LaravelCastAction::class);
});

it('keeps the given cast and callback', function (): void {
    $callback = fn (): null => null;

    $recipe = new LaravelCastRecipe(cast: 'array', callback: $callback);

    expect($recipe->cast)->toBe('array')
        ->and($recipe->callback)->toBe($callback);
});

it('is not ignored by default', function (): void {
    expect((new LaravelCastRecipe)->isIgnored())->toBeFalse();
});

it('can be ignored', function (): void {
    expect((new LaravelCastRecipe('array'))->ignore()->isIgnored())->toBeTrue();
});
