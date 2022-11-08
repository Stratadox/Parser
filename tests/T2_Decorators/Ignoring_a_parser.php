<?php declare(strict_types=1);

namespace Stratadox\Parser\Test\T2_Decorators;

use PHPUnit\Framework\TestCase;
use Stratadox\Parser\Parsers\Ignore;
use Stratadox\Parser\Parsers\Text;

final class Ignoring_a_parser extends TestCase
{
    /** @test */
    function ignoring_a_successful_full_parse()
    {
        $parser = Ignore::the(Text::is('foo'));

        $result = $parser->parse('foo');

        self::assertTrue($result->ok());
        self::assertFalse($result->use());
        self::assertNull($result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function ignoring_a_successful_partial_parse()
    {
        $parser = Ignore::the(Text::is('foo'));

        $result = $parser->parse('foo bar');

        self::assertTrue($result->ok());
        self::assertFalse($result->use());
        self::assertNull($result->data());
        self::assertEquals(' bar', $result->unparsed());
    }

    /** @test */
    function not_ignoring_an_unsuccessful_partial_parse()
    {
        $parser = Ignore::the(Text::is('foo'));

        $result = $parser->parse('bar');

        self::assertFalse($result->ok());
        self::assertFalse($result->use());
        self::assertEquals('unexpected b', $result->data());
        self::assertEquals('bar', $result->unparsed());
    }

    /** @test */
    function ignoring_a_successful_full_parse_as_string()
    {
        $parser = Ignore::the('foo');

        $result = $parser->parse('foo');

        self::assertTrue($result->ok());
        self::assertFalse($result->use());
        self::assertNull($result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function ignoring_a_successful_full_parse_through_method()
    {
        $parser = Text::is('foo')->ignore();

        $result = $parser->parse('foo');

        self::assertTrue($result->ok());
        self::assertFalse($result->use());
        self::assertNull($result->data());
        self::assertEquals('', $result->unparsed());
    }
}
