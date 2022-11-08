<?php declare(strict_types=1);

namespace Stratadox\Parser\Test\Guide;

use PHPUnit\Framework\TestCase;
use Stratadox\Parser\Parsers\Ignore;
use function Stratadox\Parser\any;
use function Stratadox\Parser\text;

final class Chapter_2_Optional_Maybe extends TestCase
{
    /** @test */
    function Chapter_2_1_example_1()
    {
        $greeting = text("Hello, ")->or("Hi, ")->or("Hey there, ");
        $verb = text("my name is ")->or("I'm called ")->or("I'm ")->ignore();
        $suffix = text(". :)")->or("!")->or("")->end();
        $name = any()->except($suffix)->repeatableString();
        $introduction = $greeting->andThen($verb, $name, $suffix);

        $result = $introduction->parse("Hey there, my name is Tom Bombadil. :)");

        self::assertEquals(["Hey there, ", "Tom Bombadil", ". :)"], $result->data());
    }

    /** @test */
    function Chapter_2_1_example_2()
    {
        $greeting = text("Hello")->or("Hi")->or("Hey there")->andThen(Ignore::the(', '));
        $verb = text("my name is ")->or("I'm called ")->or("I'm ");
        $suffix = text(". :)")->or("!")->or("")->end();
        $name = any()->except($suffix)->repeatableString();
        $introduction = $greeting->andThen($verb, $name, $suffix);

        $result = $introduction->parse("Hey there, my name is Tom Bombadil. :)");

        self::assertEquals(["Hey there", "my name is ", "Tom Bombadil", ". :)"], $result->data());
    }

    /** @test */
    function Chapter_2_2_example_1()
    {
        $greeting = text("Hello, ")->or("Hi, ")->or("Hey there, ");
        $verb = text("my name is ")->or("I'm called ")->or("I'm ")->ignore();
        $suffix = text(". :)")->or("!")->maybe()->end();
        $name = any()->except($suffix)->repeatableString();
        $introduction = $greeting->andThen($verb, $name, $suffix);

        $result = $introduction->parse("Hello, my name is Alice");

        self::assertEquals(["Hello, ", "Alice"], $result->data());
    }

    /** @test */
    function Chapter_2_2_example_2()
    {
        $greeting = text("Hello, ")->or("Hi, ")->or("Hey there, ");
        $verb = text("my name is ")->or("I'm called ")->or("I'm ")->ignore();
        $suffix = text(". :)")->or("!")->maybe()->end();
        $name = any()->except($suffix)->repeatableString();
        $introduction = $greeting->andThen($verb, $name, $suffix);

        $result = $introduction->parse("Hi, I'm Bob. :)");

        self::assertEquals(["Hi, ", "Bob", ". :)"], $result->data());
    }

    /** @test */
    function Chapter_2_3_example_1()
    {
        $greeting = text("Hello, ")->or("Hi, ")->or("Hey there, ")->optional();
        $verb = text("my name is ")->or("I'm called ")->or("I'm ")->ignore();
        $suffix = text(". :)")->or("!")->maybe()->end();
        $name = any()->except($suffix)->repeatableString();
        $introduction = $greeting->andThen($verb, $name, $suffix);

        $result = $introduction->parse("Hello, my name is Alice");

        self::assertEquals(["Alice"], $result->data());
    }

    /** @test */
    function Chapter_2_3_example_2()
    {
        $greeting = text("Hello, ")->or("Hi, ")->or("Hey there, ")->optional();
        $verb = text("my name is ")->or("I'm called ")->or("I'm ")->ignore();
        $suffix = text(". :)")->or("!")->maybe()->end();
        $name = any()->except($suffix)->repeatableString();
        $introduction = $greeting->andThen($verb, $name, $suffix);

        $result = $introduction->parse("Hi, I'm Bob. :)");

        self::assertEquals(["Bob", ". :)"], $result->data());
    }
}
