<?php

declare(strict_types=1);

use Juaniquillo\CrudAssistant\Contracts\RecipeInterface;
use Juaniquillo\LaravelModelActions\Actions\LaravelFillableAction;
use Juaniquillo\LaravelModelActions\Recipes\LaravelFillableRecipe;

it('implements the recipe contract', function (): void {
    expect(new LaravelFillableRecipe)->toBeInstanceOf(RecipeInterface::class);
});

it('identifies itself as the fillable action', function (): void {
    expect((new LaravelFillableRecipe)->getIdentifier())->toBe(LaravelFillableAction::class);
});

it('keeps the given name and callback', function (): void {
    $callback = fn (): string => 'resolved';

    $recipe = new LaravelFillableRecipe(name: 'contact_email', callback: $callback);

    expect($recipe->name)->toBe('contact_email')
        ->and($recipe->callback)->toBe($callback);
});

it('is not ignored by default', function (): void {
    expect((new LaravelFillableRecipe)->isIgnored())->toBeFalse();
});

it('can be ignored', function (): void {
    expect((new LaravelFillableRecipe)->ignore()->isIgnored())->toBeTrue();
});
