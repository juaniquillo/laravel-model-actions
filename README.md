# Laravel Model Actions

[![CI](https://github.com/juaniquillo/laravel-model-actions/actions/workflows/ci.yml/badge.svg)](https://github.com/juaniquillo/laravel-model-actions/actions/workflows/ci.yml)
[![Latest Version](https://img.shields.io/github/release/juaniquillo/laravel-model-actions.svg)](https://github.com/juaniquillo/laravel-model-actions/releases)
[![License](https://img.shields.io/github/license/juaniquillo/laravel-model-actions.svg)](LICENSE.md)
[![PHP](https://img.shields.io/badge/php-%5E8.3-777bb4.svg)](https://www.php.net)
[![Laravel](https://img.shields.io/badge/laravel-12%7C13-ff2d20.svg)](https://laravel.com)

## Requirements

- PHP `^8.3`
- Laravel `12` or `13` (used by Testbench; the package itself requires `illuminate/contracts`,
  `illuminate/database`, `illuminate/support` and `fakerphp/faker`)

## Installation

```bash
composer require juaniquillo/laravel-model-actions
```

The service provider is auto-discovered:

```php
Juaniquillo\LaravelModelActions\LaravelModelActionsServiceProvider::class
```

## Usage

Every action reads the input name, so every input must have one: an unnamed input throws an
`InvalidArgumentException`. Inputs are ordinary
[Crud Assistant](https://github.com/juaniquillo/crud-assistant) inputs.

### Fillable names

Resolves the attribute names that should be fillable. The recipe can rename the attribute or
compute it with a callback:

```php
use Juaniquillo\CrudAssistant\CrudAssistant;
use Juaniquillo\CrudAssistant\Inputs\TextInput;
use Juaniquillo\LaravelModelActions\Actions\LaravelFillableAction;
use Juaniquillo\LaravelModelActions\Recipes\LaravelFillableRecipe;

$collection = CrudAssistant::make([
    new TextInput('name'),
    (new TextInput('email'))->setRecipe(new LaravelFillableRecipe('contact_email')),
]);

$collection->execute(new LaravelFillableAction)->toArray();
// ['name', 'contact_email']
```

```php
(new LaravelFillableRecipe(
    callback: fn ($input) => 'user_'.$input->getName(),
));
```

### Factory data

Generates fake values for model factories. The recipe accepts a Faker formatter, a callback, or
nothing (a sentence is generated):

```php
use Juaniquillo\LaravelModelActions\Actions\LaravelFactoryAction;
use Juaniquillo\LaravelModelActions\Recipes\LaravelFactoryRecipe;

$collection = CrudAssistant::make([
    new TextInput('name'),
    (new TextInput('email'))->setRecipe(new LaravelFactoryRecipe('safeEmail')),
    new TextInput('bio'),
]);

$collection->execute(new LaravelFactoryAction)->toArray();
// ['name' => '...', 'email' => '...', 'bio' => '...']
```

```php
(new LaravelFactoryRecipe(
    callback: fn ($input, $output, $faker) => $output->set($input->getName(), 'custom'),
));
```

The action builds its generator with `Faker\Factory::create()`; inject a configured
`Faker\Generator` to control the locale or seed:

```php
new LaravelFactoryAction($faker);
```

A recipe can be ignored, in which case the collection skips that input:

```php
(new LaravelFactoryRecipe('safeEmail'))->ignore();
```

### Casts

Resolves the cast of every input. Inputs without a recipe are skipped: there is no sensible
default cast.

```php
use Juaniquillo\LaravelModelActions\Actions\LaravelCastAction;
use Juaniquillo\LaravelModelActions\Recipes\LaravelCastRecipe;

$collection = CrudAssistant::make([
    (new TextInput('settings'))->setRecipe(new LaravelCastRecipe('array')),
    (new TextInput('published_at'))->setRecipe(new LaravelCastRecipe('datetime')),
    new TextInput('name'),
]);

$collection->execute(new LaravelCastAction)->toArray();
// ['settings' => 'array', 'published_at' => 'datetime']
```

A callback can resolve the cast itself, including a custom cast class:

```php
use Illuminate\Database\Eloquent\Casts\AsStringable;

(new LaravelCastRecipe(
    callback: fn ($input, $output) => $output->set($input->getName(), AsStringable::class),
));
```

### Attribute lists

`$hidden`, `$visible` and `$appends` are plain attribute names, so they work like fillable names.
Each one has its own action and recipe; without a recipe the input name is used.

```php
use Juaniquillo\LaravelModelActions\Actions\LaravelAppendsAction;
use Juaniquillo\LaravelModelActions\Actions\LaravelHiddenAction;
use Juaniquillo\LaravelModelActions\Actions\LaravelVisibleAction;
use Juaniquillo\LaravelModelActions\Recipes\LaravelAppendsRecipe;
use Juaniquillo\LaravelModelActions\Recipes\LaravelHiddenRecipe;
use Juaniquillo\LaravelModelActions\Recipes\LaravelVisibleRecipe;

$collection = CrudAssistant::make([
    new TextInput('name'),
    (new TextInput('password'))->setRecipe(new LaravelHiddenRecipe('password')),
    (new TextInput('email'))->setRecipe(new LaravelVisibleRecipe('contact_email')),
    (new TextInput('avatar'))->setRecipe(new LaravelAppendsRecipe('avatar_url')),
]);

$collection->execute(new LaravelHiddenAction)->toArray();   // ['name', 'password']
$collection->execute(new LaravelVisibleAction)->toArray();  // ['name', 'contact_email']
$collection->execute(new LaravelAppendsAction)->toArray();  // ['name', 'avatar_url']
```

The recipes accept the same `name` and `callback` arguments as `LaravelFillableRecipe`.

### Resource keys

Resolves the key every input maps to in an API resource. The output is a key map
(`resource_key => attribute_name`); the action never touches a model, so conditions such as
`whenLoaded` stay in your `JsonResource`.

```php
use Juaniquillo\LaravelModelActions\Actions\LaravelResourceAction;
use Juaniquillo\LaravelModelActions\Recipes\LaravelResourceRecipe;

$collection = CrudAssistant::make([
    new TextInput('name'),
    (new TextInput('email'))->setRecipe(new LaravelResourceRecipe('contact_email')),
]);

$collection->execute(new LaravelResourceAction)->toArray();
// ['name' => 'name', 'contact_email' => 'email']
```

### Query filters

Applies a condition for every input to an Eloquent builder. Values come from the array passed to
the action, not from the inputs. Empty values are skipped unless `ignoreIfEmpty: false` is set.
An input without a recipe uses its own name as the column and `=` as the operator.

```php
use Juaniquillo\LaravelModelActions\Actions\LaravelQueryAction;
use Juaniquillo\LaravelModelActions\Recipes\LaravelQueryRecipe;

$collection = CrudAssistant::make([
    (new TextInput('name'))->setRecipe(new LaravelQueryRecipe(operator: 'like')),
    (new TextInput('status'))->setRecipe(new LaravelQueryRecipe(operator: 'in')),
    (new TextInput('created_at'))->setRecipe(new LaravelQueryRecipe(operator: 'between')),
    new TextInput('email'),
]);

$action = new LaravelQueryAction(User::query(), $request->all());

$collection->execute($action);

$action->getQuery()->paginate();
```

Supported operators: `=`, `!=`, `>`, `>=`, `<`, `<=`, `like`, `in`, `between`, `null` and
`not null`. `like` wraps the value in wildcards, `in` and `between` expect arrays (`between`
expects exactly two values), and `null`/`not null` ignore the value. An unsupported operator or a
malformed value throws an `InvalidArgumentException`.

Anything else goes through the recipe callback, which receives the input, the value and the
builder:

```php
(new LaravelQueryRecipe(
    callback: fn ($input, $value, $query) => $query->whereHas('posts', fn ($query) => $query->where('title', 'like', '%'.$value.'%')),
));
```

The action also records what it applied, which is handy for debugging:

```php
$action->getOutput()->toArray();
// ['query' => ..., 'applied' => [['column' => 'name', 'operator' => 'like', 'value' => 'jo']]]
```

## Testing

```bash
composer qa         # analyse → rector:check → lint:check → test:types → test:unit
composer test:unit  # Pest only (no --parallel on Windows)
composer test:types # 100% type coverage gate
```

## Workbench

A disposable Laravel app lives in `workbench/`:

```bash
composer build
composer serve
```

## AI skills

```bash
php artisan boost:add-skill https://github.com/juaniquillo/laravel-model-actions
```

## Structure

| Path | Purpose |
|---|---|
| `src/` | Package source (`Juaniquillo\LaravelModelActions`) |
| `tests/` | Pest feature, unit and architecture tests |
| `workbench/` | Disposable Laravel app powered by Orchestra Testbench |
| `.agents/skills/` | AI skills: `laravel-best-practices` and `laravel-model-actions-development` |
| `resources/boost/skills/` | Shipped copy of `laravel-model-actions-development` |

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## Contributing

Run `composer qa` before opening a pull request.

## License

MIT. See [LICENSE.md](LICENSE.md).
