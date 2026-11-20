<?php

declare(strict_types=1);

use Juaniquillo\CrudAssistant\Contracts\RecipeInterface;
use Juaniquillo\LaravelModelActions\Actions\LaravelAppendsAction;
use Juaniquillo\LaravelModelActions\Recipes\LaravelAppendsRecipe;

it('implements the recipe contract', function (): void {
    expect(new LaravelAppendsRecipe)->toBeInstanceOf(RecipeInterface::class);
});

it('identifies itself as the appends action', function (): void {
    expect((new LaravelAppendsRecipe)->getIdentifier())->toBe(LaravelAppendsAction::class);
});

it('keeps the given name and callback', function (): void {
    $callback = fn (): string => 'appended';

    $recipe = new LaravelAppendsRecipe(name: 'full_name', callback: $callback);

    expect($recipe->name)->toBe('full_name')
        ->and($recipe->callback)->toBe($callback);
});

it('can be ignored', function (): void {
    expect((new LaravelAppendsRecipe)->ignore()->isIgnored())->toBeTrue();
});
