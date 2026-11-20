# Laravel Model Actions

[![CI](https://github.com/juaniquillo/laravel-model-actions/actions/workflows/ci.yml/badge.svg)](https://github.com/juaniquillo/laravel-model-actions/actions/workflows/ci.yml)
[![Latest Version](https://img.shields.io/github/release/juaniquillo/laravel-model-actions.svg)](https://github.com/juaniquillo/laravel-model-actions/releases)
[![License](https://img.shields.io/github/license/juaniquillo/laravel-model-actions.svg)](LICENSE.md)
[![PHP](https://img.shields.io/badge/php-%5E8.3-777bb4.svg)](https://www.php.net)
[![Laravel](https://img.shields.io/badge/laravel-12%7C13-ff2d20.svg)](https://laravel.com)

## Requirements

- PHP `^8.3`
- Laravel `12` or `13` (used by Testbench; the package itself only requires `illuminate/contracts`, `illuminate/support` and `fakerphp/faker`)

## Installation

```bash
composer require juaniquillo/laravel-model-actions
```

The service provider is auto-discovered:

```php
Juaniquillo\LaravelModelActions\LaravelModelActionsServiceProvider::class
```

## Usage

Both actions read the input name, so every input must have one: an unnamed input throws an
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
| `.agents/skills/`, `resources/boost/skills/` | AI skills for Laravel code and for working on this package |

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## Contributing

Run `composer qa` before opening a pull request.

## License

MIT. See [LICENSE.md](LICENSE.md).
