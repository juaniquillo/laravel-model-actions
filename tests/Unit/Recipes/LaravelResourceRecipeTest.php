<?php

declare(strict_types=1);

use Juaniquillo\CrudAssistant\Contracts\RecipeInterface;
use Juaniquillo\LaravelModelActions\Actions\LaravelResourceAction;
use Juaniquillo\LaravelModelActions\Recipes\LaravelResourceRecipe;

it('implements the recipe contract', function (): void {
    expect(new LaravelResourceRecipe)->toBeInstanceOf(RecipeInterface::class);
});

it('identifies itself as the resource action', function (): void {
    expect((new LaravelResourceRecipe)->getIdentifier())->toBe(LaravelResourceAction::class);
});

it('keeps the given key and callback', function (): void {
    $callback = fn (): string => 'key';

    $recipe = new LaravelResourceRecipe(key: 'contact_email', callback: $callback);

    expect($recipe->key)->toBe('contact_email')
        ->and($recipe->callback)->toBe($callback);
});

it('can be ignored', function (): void {
    expect((new LaravelResourceRecipe)->ignore()->isIgnored())->toBeTrue();
});
