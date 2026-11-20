<?php

declare(strict_types=1);

use Faker\Generator;
use Juaniquillo\CrudAssistant\Contracts\DataContainerInterface;
use Juaniquillo\CrudAssistant\Contracts\InputInterface;
use Juaniquillo\CrudAssistant\Inputs\TextInput;
use Juaniquillo\LaravelModelActions\Actions\LaravelFactoryAction;
use Juaniquillo\LaravelModelActions\Recipes\LaravelFactoryRecipe;
use Juaniquillo\LaravelModelActions\Tests\Fixtures\SeededFaker;

it('generates a sentence when there is no recipe', function (): void {
    $action = new LaravelFactoryAction(SeededFaker::create());

    (new TextInput('bio'))->execute($action);

    $output = $action->getOutput()->toArray();

    expect($output)->toHaveKey('bio')
        ->and($output['bio'])->toBeString()->not->toBeEmpty();
});

it('generates a value from the recipe formatter', function (): void {
    $action = new LaravelFactoryAction(SeededFaker::create());

    (new TextInput('email'))
        ->setRecipe(new LaravelFactoryRecipe('safeEmail'))
        ->execute($action);

    $output = $action->getOutput()->toArray();

    expect($output['email'])->toBeString()->toContain('@');
});

it('falls back to a sentence when the recipe formatter is empty', function (): void {
    $action = new LaravelFactoryAction(SeededFaker::create());

    (new TextInput('bio'))
        ->setRecipe(new LaravelFactoryRecipe(''))
        ->execute($action);

    $output = $action->getOutput()->toArray();

    expect($output)->toHaveKey('bio')
        ->and($output['bio'])->toBeString()->not->toBeEmpty();
});

it('delegates to the recipe callback', function (): void {
    $faker = SeededFaker::create();
    $action = new LaravelFactoryAction($faker);

    $recipe = new LaravelFactoryRecipe(
        callback: function (InputInterface $input, DataContainerInterface $output, Generator $generator) use ($faker): void {
            expect($input->getName())->toBe('email')
                ->and($generator)->toBe($faker);

            $output->set('email', 'custom@example.com');
        },
    );

    (new TextInput('email'))->setRecipe($recipe)->execute($action);

    expect($action->getOutput()->toArray())->toBe(['email' => 'custom@example.com']);
});

it('writes nothing when the recipe is ignored', function (): void {
    $action = new LaravelFactoryAction(SeededFaker::create());

    (new TextInput('email'))
        ->setRecipe((new LaravelFactoryRecipe('safeEmail'))->ignore())
        ->execute($action);

    expect($action->getOutput()->toArray())->toBe([]);
});

it('throws when the recipe formatter does not exist', function (): void {
    $action = new LaravelFactoryAction(SeededFaker::create());

    expect(fn () => (new TextInput('email'))
        ->setRecipe(new LaravelFactoryRecipe('not_a_formatter'))
        ->execute($action))
        ->toThrow(InvalidArgumentException::class, 'Unknown format "not_a_formatter"');
});

it('uses a working faker generator when none is injected', function (): void {
    $action = new LaravelFactoryAction;

    (new TextInput('bio'))->execute($action);

    $output = $action->getOutput()->toArray();

    expect($output)->toHaveKey('bio')
        ->and($output['bio'])->toBeString()->not->toBeEmpty();
});

it('throws when the input has no name', function (): void {
    $action = new LaravelFactoryAction(SeededFaker::create());

    expect(fn () => $action->execute(new TextInput))
        ->toThrow(InvalidArgumentException::class, 'The input name cannot be empty.');

    expect($action->getOutput()->toArray())->toBe([]);
});

it('throws when the input name is an empty string', function (): void {
    $action = new LaravelFactoryAction(SeededFaker::create());

    expect(fn () => $action->execute((new TextInput)->setName('')))
        ->toThrow(InvalidArgumentException::class, 'The input name cannot be empty.');
});
