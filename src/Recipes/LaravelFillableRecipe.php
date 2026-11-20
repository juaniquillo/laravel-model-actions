<?php

declare(strict_types=1);

namespace Juaniquillo\LaravelModelActions\Recipes;

use Juaniquillo\LaravelModelActions\Actions\LaravelFillableAction;

/**
 * Fillable Action Recipe.
 */
final class LaravelFillableRecipe extends LaravelAttributeListRecipe
{
    /**
     * @return class-string<LaravelFillableAction>
     */
    protected function actionClass(): string
    {
        return LaravelFillableAction::class;
    }
}
