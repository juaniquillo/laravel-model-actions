<?php

declare(strict_types=1);

namespace Juaniquillo\LaravelModelActions\Recipes;

use Juaniquillo\LaravelModelActions\Actions\LaravelVisibleAction;

/**
 * Visible Action Recipe.
 */
final class LaravelVisibleRecipe extends LaravelAttributeListRecipe
{
    /**
     * @return class-string<LaravelVisibleAction>
     */
    protected function actionClass(): string
    {
        return LaravelVisibleAction::class;
    }
}
