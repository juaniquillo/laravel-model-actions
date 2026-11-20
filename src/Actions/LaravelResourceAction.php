<?php

declare(strict_types=1);

namespace Juaniquillo\LaravelModelActions\Actions;

use Closure;
use InvalidArgumentException;
use Juaniquillo\CrudAssistant\Action;
use Juaniquillo\CrudAssistant\Contracts\ActionInterface;
use Juaniquillo\CrudAssistant\Contracts\InputCollectionInterface;
use Juaniquillo\CrudAssistant\Contracts\InputInterface;
use Juaniquillo\LaravelModelActions\Recipes\LaravelResourceRecipe;

/**
 * Resolves the resource key that every input maps to.
 *
 * The output is a key map (`resource_key => attribute_name`), not a value map:
 * the action never touches a model.
 */
final class LaravelResourceAction extends Action implements ActionInterface
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

        $key = $name;

        if ($recipe instanceof LaravelResourceRecipe) {
            if ($recipe->isIgnored()) {
                return;
            }

            if ($recipe->key !== null && $recipe->key !== '') {
                $key = $recipe->key;
            } else {
                $callback = $recipe->callback;

                if ($callback instanceof Closure) {
                    $key = $callback($input);
                }
            }
        }

        $this->getOutput()->set($key, $name);
    }
}
