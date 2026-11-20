---
name: laravel-model-actions-development
description: Work on the laravel-model-actions Laravel package — add action/recipe pairs and service provider bindings, write Pest feature, unit and architecture tests, extend the Testbench workbench app, and run the analyse/rector/lint/type-coverage/Pest QA gate.
---

# Laravel Model Actions Development

## When to use this skill

Use this skill when the task touches this repository:

- Add or change an action or recipe in `src/Actions` and `src/Recipes`
- Add or change any other class, contract, enum or trait in `src/`
- Register bindings, publishes, commands, migrations or views in the service provider
- Write or update Pest tests (feature, unit or architecture)
- Add routes, models, migrations, factories or seeders to the `workbench/` app
- Fix PHPStan, Rector, Pint, type-coverage or test failures

## Guardrail commands

Run from the repository root, in this order:

```bash
composer qa         # analyse → rector:check → lint:check → test:types → test:unit
composer lint       # rewrite code style
composer rector     # apply automated refactors
composer test:unit  # Pest only
```

`composer qa` must pass before a change is considered done. Never weaken the gate to make it pass.

## Layout

| Path | Purpose |
|---|---|
| `src/` | Package source, `Juaniquillo\LaravelModelActions` |
| `src/Actions/`, `src/Recipes/` | Action/recipe pairs, plus the internal attribute-list base classes |
| `tests/Feature/` | Pest feature tests (Testbench boots a Laravel app) |
| `tests/Unit/` | Pest unit tests — Testbench still boots, because `tests/Pest.php` applies `Tests\TestCase` here too |
| `tests/ArchTest.php` | Architecture rules: presets, strict types, banned helpers |
| `workbench/` | Disposable Laravel app for manual checks and fixtures |
| `workbench/database/migrations/` | Schema loaded into `RefreshDatabase` tests by `defineDatabaseMigrations()` in `tests/TestCase.php` |
| `testbench.yaml` | Workbench wiring (providers, migrations, seeders, build steps) |
| `bin/pest.php` | Pest runner: parallel outside Windows, `--ci` in GitHub Actions |

## Conventions

- `declare(strict_types=1)` at the top of every PHP file.
- Namespace the package as `Juaniquillo\LaravelModelActions`; tests as
  `Juaniquillo\LaravelModelActions\Tests`.
- One class per file. Methods and properties are typed — type coverage is 100%, so an untyped
  parameter, return value or property fails `composer test:types`.
- Prefer constructor injection and Laravel contracts; only reach for facades in the workbench.
- Do not use `dd()`, `ddd()`, `env()` or `exit()` anywhere — `ArchTest` fails.
- New public behaviour needs unit tests, a case in `tests/Feature/CollectionExecutionTest.php`, and
  a `CHANGELOG.md` entry under `## [Unreleased]`; add a `README.md` usage section and a
  `composer.json` keyword when the feature is user-facing.
- This skill is duplicated at `.agents/skills/` and `resources/boost/skills/`. When you change it,
  update both copies identically.

## Actions and recipes

Treat `LaravelFillableAction` and `LaravelFillableRecipe` as the reference pair.

- An action is `final class XAction extends Action implements ActionInterface` and implements
  `execute(InputCollectionInterface|InputInterface $input): void`.
- A recipe is `final class XRecipe implements RecipeInterface`, uses `IsRecipe`, and sets
  `$this->action = XAction::class;` in its constructor. That is the identifier
  `$input->getRecipe($this->getIdentifier())` looks up, so a recipe only applies to its action.
- Per-input configuration belongs in the recipe (`name`, `cast`, `operator`, ...). Cross-action
  data belongs in the input attributes, read with `$input->getAttribute(...)`.
- Honour `ignore()`: the collection skips ignored recipes, but the action must also return early,
  because a single input can execute an action directly.
- A null or empty input name throws
  `InvalidArgumentException('The input name cannot be empty.')` before writing any output.
- Output conventions: `getOutput()->push(...)` for list actions, `getOutput()->set($key, $value)`
  for maps, and `['query' => $builder, 'applied' => [...]]` for the query action.
- Attribute lists (`$fillable`, `$hidden`, `$visible`, `$appends`) extend the internal
  `LaravelAttributeListAction` and `LaravelAttributeListRecipe`. A new list target is a four-line
  action plus a four-line recipe; see `LaravelHiddenAction`.
- Eloquent actions are generic: annotate the class with `@template TModel of Model`, accept and
  return `Builder<TModel>`, and type recipe callbacks with `Builder<covariant Model>`. A bare
  `Builder` fails `missingType.generics`, while `Builder<Model>` rejects callers holding
  `Builder<User>` because Eloquent's builder template is invariant.

## Adding a class

```php
<?php

declare(strict_types=1);

namespace Juaniquillo\LaravelModelActions;

final class Example
{
    public function __construct(
        private readonly string $name,
    ) {}
}
```

## Registering it

Expose the class through `LaravelModelActionsServiceProvider` only when the package needs
container bindings, commands, migrations, views or config publishing:

```php
public function register(): void
{
    $this->app->singleton(Example::class);
}
```

## Testing

Feature and unit tests both extend `Tests\TestCase`; the split is organisational, not a boot
boundary. Test observable behaviour through public APIs.

```php
it('resolves the example', function (): void {
    expect($this->app->make(Example::class))->toBeInstanceOf(Example::class);
});
```

Actions that touch Eloquent need a database-backed feature test. The schema comes from
`workbench/database/migrations`, loaded by `defineDatabaseMigrations()` in `tests/TestCase.php`:

```php
uses(RefreshDatabase::class);

it('filters rows', function (): void {
    User::query()->forceCreate(['name' => 'John Doe', 'password' => 'secret']);

    // execute the action, then assert on the builder results
});
```
