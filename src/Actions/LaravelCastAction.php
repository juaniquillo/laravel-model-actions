<?php

declare(strict_types=1);

namespace Juaniquillo\LaravelModelActions\Actions;

use Closure;
use InvalidArgumentException;
use Juaniquillo\CrudAssistant\Action;
use Juaniquillo\CrudAssistant\Contracts\ActionInterface;
use Juaniquillo\CrudAssistant\Contracts\InputCollectionInterface;
use Juaniquillo\CrudAssistant\Contracts\InputInterface;
use Juaniquillo\LaravelModelActions\Recipes\LaravelCastRecipe;

/**
 * Resolves the cast of every input.
 *
 * Inputs without a recipe are skipped: there is no sensible default cast.
 */
final class LaravelCastAction extends Action implements ActionInterface
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

        if (! $recipe instanceof LaravelCastRecipe || $recipe->isIgnored()) {
            return;
        }

        if ($recipe->cast !== null && $recipe->cast !== '') {
            $this->getOutput()->set($name, $recipe->cast);

            return;
        }

        $callback = $recipe->callback;

        if ($callback instanceof Closure) {
            $callback($input, $this->getOutput());
        }
    }
}
