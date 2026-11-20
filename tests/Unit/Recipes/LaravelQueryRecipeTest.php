<?php

declare(strict_types=1);

use Juaniquillo\CrudAssistant\Contracts\RecipeInterface;
use Juaniquillo\LaravelModelActions\Actions\LaravelQueryAction;
use Juaniquillo\LaravelModelActions\Recipes\LaravelQueryRecipe;

it('implements the recipe contract', function (): void {
    expect(new LaravelQueryRecipe)->toBeInstanceOf(RecipeInterface::class);
});

it('identifies itself as the query action', function (): void {
    expect((new LaravelQueryRecipe)->getIdentifier())->toBe(LaravelQueryAction::class);
});

it('defaults to an equality operator that ignores empty values', function (): void {
    $recipe = new LaravelQueryRecipe;

    expect($recipe->operator)->toBe('=')
        ->and($recipe->ignoreIfEmpty)->toBeTrue()
        ->and($recipe->column)->toBeNull()
        ->and($recipe->callback)->toBeNull();
});

it('keeps the given configuration', function (): void {
    $callback = fn (): null => null;

    $recipe = new LaravelQueryRecipe(
        column: 'created_at',
        operator: 'between',
        callback: $callback,
        ignoreIfEmpty: false,
    );

    expect($recipe->column)->toBe('created_at')
        ->and($recipe->operator)->toBe('between')
        ->and($recipe->callback)->toBe($callback)
        ->and($recipe->ignoreIfEmpty)->toBeFalse();
});

it('can be ignored', function (): void {
    expect((new LaravelQueryRecipe)->ignore()->isIgnored())->toBeTrue();
});
