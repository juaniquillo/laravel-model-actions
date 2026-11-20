# Changelog

All notable changes to `laravel-model-actions` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [v0.2.0] - 2026-11-20

### Added

- `LaravelCastAction` and `LaravelCastRecipe` to resolve the cast of every input.
- `LaravelHiddenAction`, `LaravelVisibleAction` and `LaravelAppendsAction` (with their recipes) to
  resolve `$hidden`, `$visible` and `$appends` attribute names.
- `LaravelResourceAction` and `LaravelResourceRecipe` to map inputs to API resource keys.
- `LaravelQueryAction` and `LaravelQueryRecipe` to apply Eloquent query conditions from input
  values, supporting `=`, `!=`, `>`, `>=`, `<`, `<=`, `like`, `in`, `between`, `null`, `not null`
  and callbacks.

### Changed

- The attribute list actions (fillable, hidden, visible, appends) share an internal base action and
  recipe; `LaravelFillableAction` and `LaravelFillableRecipe` keep their public API.
- `illuminate/database` is now a required dependency.

## [v0.1.0] - 2026-11-19

### Added

- `LaravelFactoryAction` and `LaravelFactoryRecipe` to generate fake data for model factories.
- `LaravelFillableAction` and `LaravelFillableRecipe` to resolve fillable attribute names.
- `LaravelFactoryAction` accepts an injectable `Faker\Generator` and defaults to
  `Faker\Factory::create()`, so no Laravel helper is required.
- Recipes support a Faker formatter, a callback and being ignored, in which case the collection
  skips that input.
- Both actions throw an `InvalidArgumentException` when an input has no name.

