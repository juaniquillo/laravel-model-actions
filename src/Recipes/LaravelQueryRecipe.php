<?php

declare(strict_types=1);

namespace Juaniquillo\LaravelModelActions\Recipes;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Juaniquillo\CrudAssistant\Concerns\IsRecipe;
use Juaniquillo\CrudAssistant\Contracts\InputInterface;
use Juaniquillo\CrudAssistant\Contracts\RecipeInterface;
use Juaniquillo\LaravelModelActions\Actions\LaravelQueryAction;

/**
 * Query Action Recipe.
 */
final class LaravelQueryRecipe implements RecipeInterface
{
    use IsRecipe;

    /**
     * @param  (Closure(InputInterface $input, mixed $value, Builder<covariant Model> $query): void)|null  $callback
     */
    public function __construct(
        public readonly ?string $column = null,
        public readonly string $operator = '=',
        public readonly ?Closure $callback = null,
        public readonly bool $ignoreIfEmpty = true,
    ) {
        $this->action = LaravelQueryAction::class;
    }
}
