<?php

declare(strict_types=1);

namespace Juaniquillo\LaravelModelActions\Actions;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Juaniquillo\CrudAssistant\Action;
use Juaniquillo\CrudAssistant\Contracts\ActionInterface;
use Juaniquillo\CrudAssistant\Contracts\InputCollectionInterface;
use Juaniquillo\CrudAssistant\Contracts\InputInterface;
use Juaniquillo\LaravelModelActions\Recipes\LaravelQueryRecipe;

/**
 * Applies query conditions for every input to an Eloquent builder.
 *
 * @template TModel of Model
 */
final class LaravelQueryAction extends Action implements ActionInterface
{
    /**
     * The conditions applied so far, in execution order.
     *
     * @var list<array{column: string, operator: string, value: mixed}>
     */
    private array $applied = [];

    /**
     * @param  Builder<TModel>  $query
     * @param  array<int|string, mixed>  $data
     */
    public function __construct(
        private readonly Builder $query,
        private array $data = [],
    ) {}

    /**
     * The builder the conditions are applied to.
     *
     * @return Builder<TModel>
     */
    public function getQuery(): Builder
    {
        return $this->query;
    }

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

        if ($recipe instanceof LaravelQueryRecipe) {
            if ($recipe->isIgnored()) {
                return;
            }

            $this->executeRecipe($input, $name, $recipe);

            return;
        }

        $value = $this->data[$name] ?? null;

        if ($this->isEmptyValue($value)) {
            return;
        }

        $this->apply($name, '=', $value);
    }

    private function executeRecipe(InputInterface $input, string $name, LaravelQueryRecipe $recipe): void
    {
        $column = $recipe->column ?? $name;

        $value = $this->data[$name] ?? null;

        $callback = $recipe->callback;

        if ($callback instanceof Closure) {
            $callback($input, $value, $this->query);

            $this->record($column, 'callback', $value);

            return;
        }

        $operator = $recipe->operator;

        if ($recipe->ignoreIfEmpty && ! $this->isNullOperator($operator) && $this->isEmptyValue($value)) {
            return;
        }

        $this->apply($column, $operator, $value);
    }

    private function apply(string $column, string $operator, mixed $value): void
    {
        match ($operator) {
            '=', '!=', '>', '>=', '<', '<=' => $this->query->where($column, $operator, $value),
            'like' => $this->query->where($column, 'like', '%'.$this->stringValue($value, $operator).'%'),
            'in' => $this->query->whereIn($column, $this->arrayValue($value, $operator)),
            'between' => $this->query->whereBetween($column, $this->rangeValue($value, $operator)),
            'null' => $this->query->whereNull($column),
            'not null' => $this->query->whereNotNull($column),
            default => throw new InvalidArgumentException("Unsupported query operator [{$operator}]."),
        };

        $this->record($column, $operator, $value);
    }

    private function record(string $column, string $operator, mixed $value): void
    {
        $this->applied[] = [
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
        ];

        $this->getOutput()
            ->set('query', $this->query)
            ->set('applied', $this->applied);
    }

    private function isNullOperator(string $operator): bool
    {
        return $operator === 'null' || $operator === 'not null';
    }

    private function isEmptyValue(mixed $value): bool
    {
        return in_array($value, [null, '', []], true);
    }

    private function stringValue(mixed $value, string $operator): string
    {
        if (! \is_scalar($value)) {
            throw new InvalidArgumentException("The [{$operator}] operator expects a scalar value.");
        }

        return (string) $value;
    }

    /**
     * @return array<int|string, mixed>
     */
    private function arrayValue(mixed $value, string $operator): array
    {
        if (! \is_array($value)) {
            throw new InvalidArgumentException("The [{$operator}] operator expects an array value.");
        }

        return $value;
    }

    /**
     * @return array{0: mixed, 1: mixed}
     */
    private function rangeValue(mixed $value, string $operator): array
    {
        $values = \array_values($this->arrayValue($value, $operator));

        if (\count($values) !== 2) {
            throw new InvalidArgumentException("The [{$operator}] operator expects exactly two values.");
        }

        return [$values[0], $values[1]];
    }
}
