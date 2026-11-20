<?php

declare(strict_types=1);

namespace Juaniquillo\LaravelModelActions\Actions;

use Closure;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use InvalidArgumentException;
use Juaniquillo\CrudAssistant\Action;
use Juaniquillo\CrudAssistant\Contracts\ActionInterface;
use Juaniquillo\CrudAssistant\Contracts\InputCollectionInterface;
use Juaniquillo\CrudAssistant\Contracts\InputInterface;
use Juaniquillo\LaravelModelActions\Recipes\LaravelFactoryRecipe;

/**
 * Generates fake data for every input.
 */
final class LaravelFactoryAction extends Action implements ActionInterface
{
    public function __construct(
        private ?Generator $faker = null,
    ) {}

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

        if ($recipe instanceof LaravelFactoryRecipe && $recipe->isIgnored()) {
            return;
        }

        $output = $this->getOutput();

        $faker = $this->faker();

        if ($recipe instanceof LaravelFactoryRecipe) {
            $callback = $recipe->callback;

            if ($callback instanceof Closure) {
                $callback($input, $output, $faker);

                return;
            }

            if ($recipe->type !== null && $recipe->type !== '') {
                $output->set($name, $faker->format($recipe->type));

                return;
            }
        }

        $output->set($name, $faker->sentence());
    }

    private function faker(): Generator
    {
        return $this->faker ??= FakerFactory::create();
    }
}
