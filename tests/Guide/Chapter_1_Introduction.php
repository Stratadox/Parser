<?php declare(strict_types=1);

namespace Stratadox\Parser\Test\Guide;

use PHPUnit\Framework\TestCase;
use function Stratadox\Parser\any;
use function Stratadox\Parser\pattern;
use function Stratadox\Parser\text;

final class Chapter_1_Introduction extends TestCase
{
    /** @test */
    function Chapter_1_1_example_1()
    {
        $greeting = text("Hello, ")->or("Hi, ")->or("Hey there, ");

        $result = $greeting->parse("Hello, my name is Alice");

        self::assertEquals("Hello, ", $result->data());
        self::assertEquals("my name is Alice", $result->unparsed());
    }

    /** @test */
    function Chapter_1_1_example_2()
    {
        $greeting = text("Hello, ")->or("Hi, ")->or("Hey there, ");

        $result = $greeting->parse("Hi, I'm Bob. :)");

        self::assertEquals("Hi, ", $result->data());
        self::assertEquals("I'm Bob. :)", $result->unparsed());
    }

    /** @test */
    function Chapter_1_1_example_3()
    {
        $greeting = text("Hello, ")->or("Hi, ")->or("Hey there, ");

        $result = $greeting->parse("Hey there, I'm called Charlie!");

        self::assertEquals("Hey there, ", $result->data());
        self::assertEquals("I'm called Charlie!", $result->unparsed());
    }

    /** @test */
    function Chapter_1_2_example_1()
    {
        $greeting = text("Hello, ")->or("Hi, ")->or("Hey there, ");
        $verb = text("my name is ")->or("I'm called ")->or("I'm ");
        $introduction = $greeting->andThen($verb);

        $result = $introduction->parse("Hello, my name is Alice");

        self::assertEquals(["Hello, ", "my name is "], $result->data());
        self::assertEquals("Alice", $result->unparsed());
    }

    /** @test */
    function Chapter_1_2_example_2()
    {
        $greeting = text("Hello, ")->or("Hi, ")->or("Hey there, ");
        $verb = text("my name is ")->or("I'm called ")->or("I'm ");
        $introduction = $greeting->andThen($verb);

        $result = $introduction->parse("Hi, I'm Bob. :)");

        self::assertEquals(["Hi, ", "I'm "], $result->data());
        self::assertEquals("Bob. :)", $result->unparsed());
    }

    /** @test */
    function Chapter_1_2_example_3()
    {
        $greeting = text("Hello, ")->or("Hi, ")->or("Hey there, ");
        $verb = text("my name is ")->or("I'm called ")->or("I'm ");
        $introduction = $greeting->andThen($verb);

        $result = $introduction->parse("Hey there, I'm called Charlie!");

        self::assertEquals(["Hey there, ", "I'm called "], $result->data());
        self::assertEquals("Charlie!", $result->unparsed());
    }

    /** @test */
    function Chapter_1_3_example_1()
    {
        $greeting = text("Hello, ")->or("Hi, ")->or("Hey there, ");
        $verb = text("my name is ")->or("I'm called ")->or("I'm ");
        $name = pattern("[A-Za-z]+");
        $introduction = $greeting->andThen($verb)->andThen($name);

        $result = $introduction->parse("Hello, my name is Alice");

        self::assertEquals(["Hello, ", "my name is ", "Alice"], $result->data());
        self::assertEquals("", $result->unparsed());
    }

    /** @test */
    function Chapter_1_3_example_2()
    {
        $greeting = text("Hello, ")->or("Hi, ")->or("Hey there, ");
        $verb = text("my name is ")->or("I'm called ")->or("I'm ");
        $name = pattern("[A-Za-z]+");
        $introduction = $greeting->andThen($verb)->andThen($name);

        $result = $introduction->parse("Hi, I'm Bob. :)");

        self::assertEquals(["Hi, ", "I'm ", "Bob"], $result->data());
        self::assertEquals(". :)", $result->unparsed());
    }

    /** @test */
    function Chapter_1_3_example_3()
    {
        $greeting = text("Hello, ")->or("Hi, ")->or("Hey there, ");
        $verb = text("my name is ")->or("I'm called ")->or("I'm ");
        $name = pattern("[A-Za-z]+");
        $introduction = $greeting->andThen($verb)->andThen($name);

        $result = $introduction->parse("Hey there, I'm called Charlie!");

        self::assertEquals(["Hey there, ", "I'm called ", "Charlie"], $result->data());
        self::assertEquals("!", $result->unparsed());
    }

    /** @test */
    function Chapter_1_4_example_1()
    {
        $greeting = text("Hello, ")->or("Hi, ")->or("Hey there, ");
        $verb = text("my name is ")->or("I'm called ")->or("I'm ");
        $name = pattern("[A-Za-z]+");
        $introduction = $greeting->andThen($verb)->andThen($name);

        $result = $introduction->parse("Hey there, my name is Tom Bombadil! :)");

        self::assertEquals(["Hey there, ", "my name is ", "Tom"], $result->data());
        self::assertEquals(" Bombadil! :)", $result->unparsed());
    }

    /** @test */
    function Chapter_1_4_example_2()
    {
        $greeting = text("Hello, ")->or("Hi, ")->or("Hey there, ");
        $verb = text("my name is ")->or("I'm called ")->or("I'm ");
        $name = pattern("[A-Za-z]+");
        $introduction = $greeting->andThen($verb)->andThen($name);

        $result = $introduction->parse("Hi, I'm called จอห์น ฟรัม");

        self::assertEquals("unexpected จ", $result->data());
        self::assertEquals("จอห์น ฟรัม", $result->unparsed());
    }

    /** @test */
    function Chapter_1_4_example_1b()
    {
        $greeting = text("Hello, ")->or("Hi, ")->or("Hey there, ");
        $verb = text("my name is ")->or("I'm called ")->or("I'm ");
        $suffix = text(". :)")->or("!")->or("")->end();
        $name = any()->except($suffix)->repeatableString();
        $introduction = $greeting->andThen($verb, $name, $suffix);

        $result = $introduction->parse("Hey there, my name is Tom Bombadil. :)");

        self::assertEquals(["Hey there, ", "my name is ", "Tom Bombadil", ". :)"], $result->data());
        self::assertEquals("", $result->unparsed());
    }

    /** @test */
    function Chapter_1_4_example_2b()
    {
        $greeting = text("Hello, ")->or("Hi, ")->or("Hey there, ");
        $verb = text("my name is ")->or("I'm called ")->or("I'm ");
        $suffix = text(". :)")->or("!")->or("")->end();
        $name = any()->except($suffix)->repeatableString();
        $introduction = $greeting->andThen($verb, $name, $suffix);

        $result = $introduction->parse("Hi, I'm called จอห์น ฟรัม");

        self::assertEquals(["Hi, ", "I'm called ", "จอห์น ฟรัม", ""], $result->data());
        self::assertEquals("", $result->unparsed());
    }

    /** @test */
    function Chapter_1_4_example_3()
    {
        $greeting = text("Hello, ")->or("Hi, ")->or("Hey there, ");
        $verb = text("my name is ")->or("I'm called ")->or("I'm ");
        $suffix = text(". :)")->or("!")->or("")->end();
        $name = any()->except($suffix)->repeatableString();
        $introduction = $greeting->andThen($verb, $name, $suffix);

        $result = $introduction->parse("Hello, my name is Alice");

        self::assertEquals(["Hello, ", "my name is ", "Alice", ""], $result->data());
        self::assertEquals("", $result->unparsed());
    }

    /** @test */
    function Chapter_1_4_example_4()
    {
        $greeting = text("Hello, ")->or("Hi, ")->or("Hey there, ");
        $verb = text("my name is ")->or("I'm called ")->or("I'm ");
        $suffix = text(". :)")->or("!")->or("")->end();
        $name = any()->except($suffix)->repeatableString();
        $introduction = $greeting->andThen($verb, $name, $suffix);

        $result = $introduction->parse("Hi, I'm Bob. :)");

        self::assertEquals(["Hi, ", "I'm ", "Bob", ". :)"], $result->data());
        self::assertEquals("", $result->unparsed());
    }

    /** @test */
    function Chapter_1_4_example_5()
    {
        $greeting = text("Hello, ")->or("Hi, ")->or("Hey there, ");
        $verb = text("my name is ")->or("I'm called ")->or("I'm ");
        $suffix = text(". :)")->or("!")->or("")->end();
        $name = any()->except($suffix)->repeatableString();
        $introduction = $greeting->andThen($verb, $name, $suffix);

        $result = $introduction->parse("Hey there, I'm called Charlie!");

        self::assertEquals(["Hey there, ", "I'm called ", "Charlie", "!"], $result->data());
        self::assertEquals("", $result->unparsed());
    }
}
