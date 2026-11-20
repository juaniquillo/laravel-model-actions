<?php

declare(strict_types=1);

namespace Juaniquillo\LaravelModelActions\Recipes;

use Closure;
use Juaniquillo\CrudAssistant\Concerns\IsRecipe;
use Juaniquillo\CrudAssistant\Contracts\InputInterface;
use Juaniquillo\CrudAssistant\Contracts\RecipeInterface;
use Juaniquillo\LaravelModelActions\Actions\LaravelFillableAction;

/**
 * Fillable Action Recipe.
 */
final class LaravelFillableRecipe implements RecipeInterface
{
    use IsRecipe;

    /**
     * @param  (Closure(InputInterface $input): string)|null  $callback
     */
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?Closure $callback = null,
    ) {
        $this->action = LaravelFillableAction::class;
    }
}
