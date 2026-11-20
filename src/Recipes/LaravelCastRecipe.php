<?php

declare(strict_types=1);

namespace Juaniquillo\LaravelModelActions\Recipes;

use Closure;
use Juaniquillo\CrudAssistant\Concerns\IsRecipe;
use Juaniquillo\CrudAssistant\Contracts\DataContainerInterface;
use Juaniquillo\CrudAssistant\Contracts\InputInterface;
use Juaniquillo\CrudAssistant\Contracts\RecipeInterface;
use Juaniquillo\LaravelModelActions\Actions\LaravelCastAction;

/**
 * Cast Action Recipe.
 */
final class LaravelCastRecipe implements RecipeInterface
{
    use IsRecipe;

    /**
     * @param  (Closure(InputInterface $input, DataContainerInterface<mixed> $output): void)|null  $callback
     */
    public function __construct(
        public readonly ?string $cast = null,
        public readonly ?Closure $callback = null,
    ) {
        $this->action = LaravelCastAction::class;
    }
}
