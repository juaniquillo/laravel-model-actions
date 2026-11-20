<?php

declare(strict_types=1);

namespace Juaniquillo\LaravelModelActions\Recipes;

use Closure;
use Faker\Generator;
use Juaniquillo\CrudAssistant\Concerns\IsRecipe;
use Juaniquillo\CrudAssistant\Contracts\DataContainerInterface;
use Juaniquillo\CrudAssistant\Contracts\InputInterface;
use Juaniquillo\CrudAssistant\Contracts\RecipeInterface;
use Juaniquillo\LaravelModelActions\Actions\LaravelFactoryAction;

/**
 * Factory Action Recipe.
 */
final class LaravelFactoryRecipe implements RecipeInterface
{
    use IsRecipe;

    /**
     * @param  (Closure(InputInterface $input, DataContainerInterface<mixed> $output, Generator $faker): void)|null  $callback
     */
    public function __construct(
        public readonly ?string $type = null,
        public readonly ?Closure $callback = null,
    ) {
        $this->action = LaravelFactoryAction::class;
    }
}
