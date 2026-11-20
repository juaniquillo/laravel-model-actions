<?php

declare(strict_types=1);

use Juaniquillo\CrudAssistant\Contracts\RecipeInterface;
use Juaniquillo\LaravelModelActions\Actions\LaravelFactoryAction;
use Juaniquillo\LaravelModelActions\Recipes\LaravelFactoryRecipe;

it('implements the recipe contract', function (): void {
    expect(new LaravelFactoryRecipe)->toBeInstanceOf(RecipeInterface::class);
});

it('identifies itself as the factory action', function (): void {
    expect((new LaravelFactoryRecipe)->getIdentifier())->toBe(LaravelFactoryAction::class);
});

it('keeps the given type and callback', function (): void {
    $callback = fn (): null => null;

    $recipe = new LaravelFactoryRecipe(type: 'safeEmail', callback: $callback);

    expect($recipe->type)->toBe('safeEmail')
        ->and($recipe->callback)->toBe($callback);
});

it('is not ignored by default', function (): void {
    expect((new LaravelFactoryRecipe)->isIgnored())->toBeFalse();
});

it('can be ignored', function (): void {
    expect((new LaravelFactoryRecipe)->ignore()->isIgnored())->toBeTrue();
});
