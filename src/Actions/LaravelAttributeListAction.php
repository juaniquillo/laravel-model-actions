<?php

declare(strict_types=1);

namespace Juaniquillo\LaravelModelActions\Actions;

use Closure;
use InvalidArgumentException;
use Juaniquillo\CrudAssistant\Action;
use Juaniquillo\CrudAssistant\Contracts\ActionInterface;
use Juaniquillo\CrudAssistant\Contracts\InputCollectionInterface;
use Juaniquillo\CrudAssistant\Contracts\InputInterface;
use Juaniquillo\CrudAssistant\Contracts\RecipeInterface;
use Juaniquillo\LaravelModelActions\Recipes\LaravelAttributeListRecipe;

/**
 * Resolves a list of attribute names for every input.
 *
 * @internal
 */
abstract class LaravelAttributeListAction extends Action implements ActionInterface
{
    /**
     * Execute action on input.
     */
    public function execute(InputCollectionInterface|InputInterface $input): void
    {
        $name = $input->getName();

        if ($name === null || $name === '') {
            throw new InvalidArgumentException('The input name cannot be empty.');
        }

        $recipe = $input->getRecipe($this->getIdentifier());

        if ($recipe instanceof LaravelAttributeListRecipe && $recipe->isIgnored()) {
            return;
        }

        $this->getOutput()->push($this->resolveName($input, $name, $recipe));
    }

    private function resolveName(InputInterface $input, string $name, ?RecipeInterface $recipe): string
    {
        if (! $recipe instanceof LaravelAttributeListRecipe) {
            return $name;
        }

        if ($recipe->name !== null && $recipe->name !== '') {
            return $recipe->name;
        }

        $callback = $recipe->callback;

        if ($callback instanceof Closure) {
            return $callback($input);
        }

        return $name;
    }
}
