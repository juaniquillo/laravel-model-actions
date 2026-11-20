# Changelog

All notable changes to `laravel-model-actions` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [v0.1.0] - 2026-11-19

### Added

- `LaravelFactoryAction` and `LaravelFactoryRecipe` to generate fake data for model factories.
- `LaravelFillableAction` and `LaravelFillableRecipe` to resolve fillable attribute names.
- `LaravelFactoryAction` accepts an injectable `Faker\Generator` and defaults to
  `Faker\Factory::create()`, so no Laravel helper is required.
- Recipes support a Faker formatter, a callback and being ignored, in which case the collection
  skips that input.
- Both actions throw an `InvalidArgumentException` when an input has no name.

