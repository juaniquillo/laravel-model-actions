<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Builder;
use Juaniquillo\CrudAssistant\Contracts\InputInterface;
use Juaniquillo\CrudAssistant\Inputs\TextInput;
use Juaniquillo\LaravelModelActions\Actions\LaravelQueryAction;
use Juaniquillo\LaravelModelActions\Recipes\LaravelQueryRecipe;
use Workbench\App\Models\User;

it('exposes the builder it mutates', function (): void {
    $query = User::query();
    $action = new LaravelQueryAction($query);

    expect($action->getQuery())->toBe($query);
});

it('applies an equality condition from the input value when there is no recipe', function (): void {
    $action = new LaravelQueryAction(User::query(), ['email' => 'john@example.com']);

    (new TextInput('email'))->execute($action);

    expect($action->getQuery()->toSql())->toBe('select * from "users" where "email" = ?')
        ->and($action->getQuery()->getBindings())->toBe(['john@example.com']);
});

it('skips inputs without a recipe when the value is empty', function (): void {
    $action = new LaravelQueryAction(User::query(), ['email' => '']);

    (new TextInput('email'))->execute($action);

    expect($action->getQuery()->toSql())->toBe('select * from "users"')
        ->and($action->getOutput()->toArray())->toBe([]);
});

it('uses the recipe column name', function (): void {
    $action = new LaravelQueryAction(User::query(), ['name' => 'john']);

    (new TextInput('name'))
        ->setRecipe(new LaravelQueryRecipe(column: 'users.name', operator: 'like'))
        ->execute($action);

    expect($action->getQuery()->toSql())->toBe('select * from "users" where "users"."name" like ?')
        ->and($action->getQuery()->getBindings())->toBe(['%john%']);
});

it('wraps like values with wildcards', function (): void {
    $action = new LaravelQueryAction(User::query(), ['name' => 'john']);

    (new TextInput('name'))->setRecipe(new LaravelQueryRecipe(operator: 'like'))->execute($action);

    expect($action->getQuery()->getBindings())->toBe(['%john%']);
});

it('applies an in condition', function (): void {
    $action = new LaravelQueryAction(User::query(), ['status' => ['active', 'pending']]);

    (new TextInput('status'))->setRecipe(new LaravelQueryRecipe(operator: 'in'))->execute($action);

    expect($action->getQuery()->toSql())->toBe('select * from "users" where "status" in (?, ?)')
        ->and($action->getQuery()->getBindings())->toBe(['active', 'pending']);
});

it('applies a between condition', function (): void {
    $action = new LaravelQueryAction(User::query(), [
        'created_at' => ['2026-01-01', '2026-12-31'],
    ]);

    (new TextInput('created_at'))->setRecipe(new LaravelQueryRecipe(operator: 'between'))->execute($action);

    expect($action->getQuery()->toSql())->toBe('select * from "users" where "created_at" between ? and ?')
        ->and($action->getQuery()->getBindings())->toBe(['2026-01-01', '2026-12-31']);
});

it('applies a null condition even when the value is empty', function (): void {
    $action = new LaravelQueryAction(User::query(), ['deleted_at' => null]);

    (new TextInput('deleted_at'))->setRecipe(new LaravelQueryRecipe(operator: 'null'))->execute($action);

    expect($action->getQuery()->toSql())->toBe('select * from "users" where "deleted_at" is null')
        ->and($action->getQuery()->getBindings())->toBe([]);
});

it('applies a not null condition', function (): void {
    $action = new LaravelQueryAction(User::query(), ['deleted_at' => null]);

    (new TextInput('deleted_at'))->setRecipe(new LaravelQueryRecipe(operator: 'not null'))->execute($action);

    expect($action->getQuery()->toSql())->toBe('select * from "users" where "deleted_at" is not null')
        ->and($action->getQuery()->getBindings())->toBe([]);
});

it('applies a comparison condition', function (): void {
    $action = new LaravelQueryAction(User::query(), ['status' => 'active']);

    (new TextInput('status'))->setRecipe(new LaravelQueryRecipe(operator: '!='))->execute($action);

    expect($action->getQuery()->toSql())->toBe('select * from "users" where "status" != ?')
        ->and($action->getQuery()->getBindings())->toBe(['active']);
});

it('applies an empty value when the recipe allows it', function (): void {
    $action = new LaravelQueryAction(User::query(), ['name' => '']);

    (new TextInput('name'))
        ->setRecipe(new LaravelQueryRecipe(operator: 'like', ignoreIfEmpty: false))
        ->execute($action);

    expect($action->getQuery()->getBindings())->toBe(['%%']);
});

it('delegates to the recipe callback', function (): void {
    $action = new LaravelQueryAction(User::query(), ['email' => 'john']);

    $recipe = new LaravelQueryRecipe(
        column: 'email',
        callback: function (InputInterface $input, mixed $value, Builder $query): void {
            expect($input->getName())->toBe('email')
                ->and($value)->toBe('john');

            $query->where('email', 'like', $value.'%');
        },
    );

    (new TextInput('email'))->setRecipe($recipe)->execute($action);

    expect($action->getQuery()->toSql())->toBe('select * from "users" where "email" like ?')
        ->and($action->getQuery()->getBindings())->toBe(['john%']);
});

it('records the applied conditions in the output', function (): void {
    $action = new LaravelQueryAction(User::query(), ['name' => 'john']);

    (new TextInput('name'))->setRecipe(new LaravelQueryRecipe(operator: 'like'))->execute($action);

    $output = $action->getOutput()->toArray();

    expect($output['applied'])->toBe([
        ['column' => 'name', 'operator' => 'like', 'value' => 'john'],
    ])->and($output['query'])->toBe($action->getQuery());
});

it('writes nothing when the recipe is ignored', function (): void {
    $action = new LaravelQueryAction(User::query(), ['name' => 'john']);

    (new TextInput('name'))
        ->setRecipe((new LaravelQueryRecipe(operator: 'like'))->ignore())
        ->execute($action);

    expect($action->getQuery()->toSql())->toBe('select * from "users"')
        ->and($action->getOutput()->toArray())->toBe([]);
});

it('rejects an unsupported operator', function (): void {
    $action = new LaravelQueryAction(User::query(), ['name' => 'john']);

    expect(fn () => (new TextInput('name'))
        ->setRecipe(new LaravelQueryRecipe(operator: '~'))
        ->execute($action))
        ->toThrow(InvalidArgumentException::class, 'Unsupported query operator [~].');
});

it('requires an array for the in operator', function (): void {
    $action = new LaravelQueryAction(User::query(), ['status' => 'active']);

    expect(fn () => (new TextInput('status'))
        ->setRecipe(new LaravelQueryRecipe(operator: 'in'))
        ->execute($action))
        ->toThrow(InvalidArgumentException::class, 'The [in] operator expects an array value.');
});

it('requires an array for the between operator', function (): void {
    $action = new LaravelQueryAction(User::query(), ['created_at' => '2026-01-01']);

    expect(fn () => (new TextInput('created_at'))
        ->setRecipe(new LaravelQueryRecipe(operator: 'between'))
        ->execute($action))
        ->toThrow(InvalidArgumentException::class, 'The [between] operator expects an array value.');
});

it('requires exactly two values for the between operator', function (): void {
    $action = new LaravelQueryAction(User::query(), ['created_at' => ['2026-01-01']]);

    expect(fn () => (new TextInput('created_at'))
        ->setRecipe(new LaravelQueryRecipe(operator: 'between'))
        ->execute($action))
        ->toThrow(InvalidArgumentException::class, 'The [between] operator expects exactly two values.');
});

it('requires a scalar value for the like operator', function (): void {
    $action = new LaravelQueryAction(User::query(), ['name' => ['john']]);

    expect(fn () => (new TextInput('name'))
        ->setRecipe(new LaravelQueryRecipe(operator: 'like'))
        ->execute($action))
        ->toThrow(InvalidArgumentException::class, 'The [like] operator expects a scalar value.');
});

it('throws when the input has no name', function (): void {
    $action = new LaravelQueryAction(User::query());

    expect(fn () => $action->execute(new TextInput))
        ->toThrow(InvalidArgumentException::class, 'The input name cannot be empty.');
});

it('throws when the input name is an empty string', function (): void {
    $action = new LaravelQueryAction(User::query());

    expect(fn () => $action->execute((new TextInput)->setName('')))
        ->toThrow(InvalidArgumentException::class, 'The input name cannot be empty.');
});
