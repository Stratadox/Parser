<?php declare(strict_types=1);

namespace Stratadox\Parser\Test\T2_Decorators;

use PHPUnit\Framework\TestCase;
use Stratadox\Parser\Parsers\Optional;
use Stratadox\Parser\Parsers\Text;

final class Optional_parser extends TestCase
{
    /** @test */
    function ignoring_a_successful_full_parse()
    {
        $parser = Optional::ignored(Text::is('foo'));

        $result = $parser->parse('foo');

        self::assertTrue($result->ok());
        self::assertFalse($result->use());
        self::assertNull($result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function ignoring_a_successful_partial_parse()
    {
        $parser = Optional::ignored(Text::is('foo'));

        $result = $parser->parse('foo bar');

        self::assertTrue($result->ok());
        self::assertFalse($result->use());
        self::assertNull($result->data());
        self::assertEquals(' bar', $result->unparsed());
    }

    /** @test */
    function ignoring_an_unsuccessful_partial_parse()
    {
        $parser = Optional::ignored(Text::is('foo'));

        $result = $parser->parse('bar');

        self::assertTrue($result->ok());
        self::assertFalse($result->use());
        self::assertNull($result->data());
        self::assertEquals('bar', $result->unparsed());
    }

    /** @test */
    function ignoring_a_successful_full_parse_as_string()
    {
        $parser = Optional::ignored('foo');

        $result = $parser->parse('foo');

        self::assertTrue($result->ok());
        self::assertFalse($result->use());
        self::assertNull($result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function ignoring_a_successful_partial_parse_as_string()
    {
        $parser = Optional::ignored('foo');

        $result = $parser->parse('foo bar');

        self::assertTrue($result->ok());
        self::assertFalse($result->use());
        self::assertNull($result->data());
        self::assertEquals(' bar', $result->unparsed());
    }

    /** @test */
    function ignoring_an_unsuccessful_partial_parse_as_string()
    {
        $parser = Optional::ignored('foo');

        $result = $parser->parse('bar');

        self::assertTrue($result->ok());
        self::assertFalse($result->use());
        self::assertNull($result->data());
        self::assertEquals('bar', $result->unparsed());
    }

    /** @test */
    function ignoring_a_successful_full_parse_through_method()
    {
        $parser = Text::is('foo')->optional();

        $result = $parser->parse('foo');

        self::assertTrue($result->ok());
        self::assertFalse($result->use());
        self::assertNull($result->data());
        self::assertEquals('', $result->unparsed());
    }
}
