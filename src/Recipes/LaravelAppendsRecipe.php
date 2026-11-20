<?php

declare(strict_types=1);

namespace Juaniquillo\LaravelModelActions\Recipes;

use Juaniquillo\LaravelModelActions\Actions\LaravelAppendsAction;

/**
 * Appends Action Recipe.
 */
final class LaravelAppendsRecipe extends LaravelAttributeListRecipe
{
    /**
     * @return class-string<LaravelAppendsAction>
     */
    protected function actionClass(): string
    {
        return LaravelAppendsAction::class;
    }
}
