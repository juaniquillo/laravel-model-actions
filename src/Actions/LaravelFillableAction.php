<?php

declare(strict_types=1);

namespace Juaniquillo\LaravelModelActions\Actions;

use Closure;
use InvalidArgumentException;
use Juaniquillo\CrudAssistant\Action;
use Juaniquillo\CrudAssistant\Contracts\ActionInterface;
use Juaniquillo\CrudAssistant\Contracts\InputCollectionInterface;
use Juaniquillo\CrudAssistant\Contracts\InputInterface;
use Juaniquillo\LaravelModelActions\Recipes\LaravelFillableRecipe;

/**
 * Resolves the fillable attribute name for every input.
 */
final class LaravelFillableAction extends Action implements ActionInterface
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

        $output = $this->getOutput();

        $recipe = $input->getRecipe($this->getIdentifier());

        if ($recipe instanceof LaravelFillableRecipe) {
            if ($recipe->name !== null && $recipe->name !== '') {
                $output->push($recipe->name);

                return;
            }

            $callback = $recipe->callback;

            if ($callback instanceof Closure) {
                $output->push($callback($input));

                return;
            }
        }

        $output->push($name);
    }
}
