<?php

declare(strict_types=1);

namespace Juaniquillo\LaravelModelActions\Recipes;

use Juaniquillo\LaravelModelActions\Actions\LaravelHiddenAction;

/**
 * Hidden Action Recipe.
 */
final class LaravelHiddenRecipe extends LaravelAttributeListRecipe
{
    /**
     * @return class-string<LaravelHiddenAction>
     */
    protected function actionClass(): string
    {
        return LaravelHiddenAction::class;
    }
}
