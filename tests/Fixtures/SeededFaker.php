<?php

declare(strict_types=1);

namespace Juaniquillo\LaravelModelActions\Tests\Fixtures;

use Faker\Factory as FakerFactory;
use Faker\Generator;

/**
 * Deterministic Faker generator for assertions on generated values.
 */
final class SeededFaker
{
    public static function create(int $seed = 1234): Generator
    {
        $generator = FakerFactory::create();
        $generator->seed($seed);

        return $generator;
    }
}
