<?php declare(strict_types=1);

namespace Stratadox\Parser\Test\T2_Decorators;

use PHPUnit\Framework\TestCase;
use Stratadox\Parser\Parsers\Maybe;
use Stratadox\Parser\Parsers\Text;

final class Maybe_a_parser extends TestCase
{
    /** @test */
    function parsing_a_text_that_occurs()
    {
        $parser = Maybe::that(Text::is('foo'));

        $result = $parser->parse('foo bar');

        self::assertTrue($result->ok());
        self::assertEquals('foo', $result->data());
        self::assertEquals(' bar', $result->unparsed());
    }

    /** @test */
    function ignoring_a_text_that_does_not_occur()
    {
        $parser = Maybe::that(Text::is('foo'));

        $result = $parser->parse('bar bar');

        self::assertTrue($result->ok());
        self::assertFalse($result->use());
        self::assertNull($result->data());
        self::assertEquals('bar bar', $result->unparsed());
    }

    /** @test */
    function parsing_a_text_that_occurs_as_string()
    {
        $parser = Maybe::that('foo');

        $result = $parser->parse('foo bar');

        self::assertTrue($result->ok());
        self::assertEquals('foo', $result->data());
        self::assertEquals(' bar', $result->unparsed());
    }

    /** @test */
    function ignoring_a_text_that_does_not_occur_as_string()
    {
        $parser = Maybe::that('foo');

        $result = $parser->parse('bar bar');

        self::assertTrue($result->ok());
        self::assertFalse($result->use());
        self::assertNull($result->data());
        self::assertEquals('bar bar', $result->unparsed());
    }

    /** @test */
    function parsing_a_text_that_occurs_through_method()
    {
        $parser = Text::is('foo')->maybe();

        $result = $parser->parse('foo bar');

        self::assertTrue($result->ok());
        self::assertEquals('foo', $result->data());
        self::assertEquals(' bar', $result->unparsed());
    }

    /** @test */
    function ignoring_a_text_that_does_not_occur_through_method()
    {
        $parser = Text::is('foo')->maybe();

        $result = $parser->parse('bar bar');

        self::assertTrue($result->ok());
        self::assertFalse($result->use());
        self::assertNull($result->data());
        self::assertEquals('bar bar', $result->unparsed());
    }
}
