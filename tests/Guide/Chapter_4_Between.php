<?php declare(strict_types=1);

namespace Stratadox\Parser\Test\Guide;

use PHPUnit\Framework\TestCase;
use Stratadox\Parser\Helpers\Between;
use Stratadox\Parser\Parsers\Ignore;
use function Stratadox\Parser\any;
use function Stratadox\Parser\pattern;
use function Stratadox\Parser\text;

final class Chapter_4_Between extends TestCase
{
    /** @test */
    function Chapter_4_1_example_1()
    {
        $greeting = text("Hello")->or("Hi", "Hey there")->andThen(Ignore::the(','));
        $verb = text("my name is")->or("I'm called", "I'm")->between(' ');
        $suffix = text(". :)")->or("!")->maybe()->end();
        $name = any()->except($suffix)->repeatableString();
        $introduction = $greeting->andThen($verb, $name, $suffix);

        $result = $introduction->parse("Hello, my name is Alice");

        self::assertEquals(["Hello", "my name is", "Alice"], $result->data());
    }

    /** @test */
    function Chapter_4_1_example_2()
    {
        $parser = text('content')->between(pattern('[({]'), text(')')->or('}'));

        $result = $parser->parse('{content}');

        self::assertEquals('content', $result->data());
    }

    /** @test */
    function Chapter_4_1_example_3()
    {
        $parser = text('content')->between(pattern('[({]'), text(')')->or('}'));

        $result = $parser->parse('(content)');

        self::assertEquals('content', $result->data());
    }

    /** @test */
    function Chapter_4_2_example_1()
    {
        $parser = Between::escaped('"', '"', '\\');

        $result = $parser->parse('"Regular text between quotes"');

        self::assertEquals('Regular text between quotes', $result->data());
    }

    /** @test */
    function Chapter_4_2_example_2()
    {
        $parser = Between::escaped('"', '"', '\\');

        $result = $parser->parse('"Text with \\"escaped\\" quotes"');

        self::assertEquals('Text with "escaped" quotes', $result->data());
    }
}
