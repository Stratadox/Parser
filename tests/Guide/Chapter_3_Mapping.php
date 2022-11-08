<?php declare(strict_types=1);

namespace Stratadox\Parser\Test\Guide;

use PHPUnit\Framework\TestCase;
use Stratadox\Parser\Parsers\Ignore;
use function Stratadox\Parser\any;
use function Stratadox\Parser\text;
use function strtolower;
use function strtoupper;
use function trim;

final class Chapter_3_Mapping extends TestCase
{
    /** @test */
    function Chapter_3_baseline_1()
    {
        $greeting = text("Hello")->or("Hi", "Hey there")->andThen(Ignore::the(', '));
        $verb = text("my name is ")->or("I'm called ", "I'm ");
        $suffix = text(". :)")->or("!")->maybe()->end();
        $name = any()->except($suffix)->repeatableString();
        $introduction = $greeting->andThen($verb, $name, $suffix);

        $result = $introduction->parse("Hello, my name is Alice");

        self::assertEquals(["Hello", "my name is ", "Alice"], $result->data());
    }

    /** @test */
    function Chapter_3_baseline_2()
    {
        $greeting = text("Hello")->or("Hi", "Hey there")->andThen(Ignore::the(', '));
        $verb = text("my name is ")->or("I'm called ", "I'm ");
        $suffix = text(". :)")->or("!")->maybe()->end();
        $name = any()->except($suffix)->repeatableString();
        $introduction = $greeting->andThen($verb, $name, $suffix);

        $result = $introduction->parse("Hey there, my name is Tom Bombadil. :)");

        self::assertEquals(
            ["Hey there", "my name is ", "Tom Bombadil", ". :)"],
            $result->data()
        );
    }

    /** @test */
    function Chapter_3_1_example_1()
    {
        $greeting = text("Hello")->or("Hi", "Hey there")
            ->map(fn($x) => strtoupper($x))
            ->andThen(Ignore::the(', '));
        $verb = text("my name is ")->or("I'm called ", "I'm ");
        $suffix = text(". :)")->or("!")->maybe()->end();
        $name = any()->except($suffix)->repeatableString();
        $introduction = $greeting->andThen($verb, $name, $suffix);

        $result = $introduction->parse("Hey there, my name is Tom Bombadil. :)");

        self::assertEquals(
            ["HEY THERE", "my name is ", "Tom Bombadil", ". :)"],
            $result->data()
        );
    }

    /** @test */
    function Chapter_3_1_example_2()
    {
        $greeting = text("Hello")->or("Hi", "Hey there")
            ->map(fn($x) => strtolower($x))
            ->andThen(Ignore::the(', '));
        $verb = text("my name is ")->or("I'm called ", "I'm ")
            ->map(fn($x) => trim($x));
        $suffix = text(". :)")->or("!")->maybe()->end();
        $name = any()->except($suffix)->repeatableString();
        $introduction = $greeting->andThen($verb, $name, $suffix)
            ->map(fn($x) => [
                'greeting' => $x[0],
                'verb' => $x[1],
                'name' => $x[2],
                'suffix' => $x[3] ?? null,
            ]);

        $result = $introduction->parse("Hey there, my name is Tom Bombadil. :)");

        self::assertEquals(
            [
                'greeting' => "hey there",
                'verb' => "my name is",
                'name' => "Tom Bombadil",
                'suffix' => ". :)",
            ],
            $result->data()
        );
    }

    /** @test */
    function Chapter_3_2_example_1()
    {
        $greeting = text("Hello")->or("Hi", "Hey there")
            ->map(fn($x) => strtolower($x))
            ->andThen(Ignore::the(', '));
        $verb = text("my name is ")->or("I'm called ", "I'm ")
            ->map(fn($x) => trim($x));
        $suffix = text(". :)")->or("!")->maybe()->end();
        $name = any()->except($suffix)->repeatableString();
        $introduction = $greeting->andThen($verb)->join(' / ')
            ->andThen($name, $suffix)
            ->map(fn($x) => [
                'greeting' => $x[0],
                'name' => $x[1],
                'suffix' => $x[2] ?? null,
            ]);

        $result = $introduction->parse("Hey there, my name is Tom Bombadil. :)");

        self::assertEquals(
            [
                'greeting' => "hey there / my name is",
                'name' => "Tom Bombadil",
                'suffix' => ". :)",
            ],
            $result->data()
        );
    }

    /** @test */
    function Chapter_3_2_example_2()
    {
        $greeting = text("Hello")->or("Hi", "Hey there")
            ->map(fn($x) => strtolower($x))
            ->andThen(Ignore::the(', '));
        $verb = text("my name is ")->or("I'm called ", "I'm ")
            ->map(fn($x) => trim($x));
        $suffix = text(". :)")->or("!")->maybe()->end();
        $name = any()->except($suffix)->repeatableString();
        $introduction = $greeting->andThen($verb)->join(' / ')
            ->andThen($name, $suffix)
            ->map(fn($x) => [
                'greeting' => $x[0],
                'name' => $x[1],
                'suffix' => $x[2] ?? null,
            ]);

        $result = $introduction->parse("Hi, I'm Bob");

        self::assertEquals(
            [
                'greeting' => "hi / I'm",
                'name' => "Bob",
                'suffix' => null,
            ],
            $result->data()
        );
    }

    /** @test */
    function Chapter_3_3_example_1()
    {
        $greeting = text("Hello")->or("Hi", "Hey there")->andThen(Ignore::the(', '));
        $verb = text("my name is ")->or("I'm called ", "I'm ");
        $suffix = text(". :)")->or("!")->maybe()->end();
        $name = any()->except($suffix)->repeatable()->atLeast(2)->join();
        $introduction = $greeting->andThen($verb, $name, $suffix);

        $result = $introduction->parse("Hey there, my name is A. :)");

        self::assertFalse($result->ok());
        self::assertEquals("unexpected .", $result->data());
    }

    /** @test */
    function Chapter_3_3_example_2()
    {
        $parser = text('a')->repeatable()->atLeast(3)->atMost(5)
            ->or(
                text('a')->repeatable()->ignore()
                    ->andThen(text('b')->repeatable()->atLeast(5))
                    ->first()
            );

        $result = $parser->parse('aaaabbbbbbbbb');

        self::assertEquals(['a', 'a', 'a', 'a'], $result->data());
    }

    /** @test */
    function Chapter_3_3_example_3()
    {
        $parser = text('a')->repeatable()->atLeast(3)->atMost(5)
            ->or(
                text('a')->repeatable()->ignore()
                    ->andThen(text('b')->repeatable()->atLeast(5))
                    ->first()
            );

        $result = $parser->parse('aaaaaaaabbbbb');

        self::assertEquals(['b', 'b', 'b', 'b', 'b'], $result->data());
    }
}
