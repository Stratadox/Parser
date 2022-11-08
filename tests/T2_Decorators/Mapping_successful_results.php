<?php declare(strict_types=1);

namespace Stratadox\Parser\Test\T2_Decorators;

use PHPUnit\Framework\TestCase;
use Stratadox\Parser\Parsers\Map;
use Stratadox\Parser\Parsers\Text;
use function strtoupper;

final class Mapping_successful_results extends TestCase
{
    /** @test */
    function mapping_a_successful_full_parse()
    {
        $parser = Map::the(Text::is('foo'), fn($result) => strtoupper($result));

        $result = $parser->parse('foo');

        self::assertTrue($result->ok());
        self::assertEquals('FOO', $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function mapping_a_successful_partial_parse()
    {
        $parser = Map::the(Text::is('foo'), fn($result) => strtoupper($result));

        $result = $parser->parse('foo bar');

        self::assertTrue($result->ok());
        self::assertEquals('FOO', $result->data());
        self::assertEquals(' bar', $result->unparsed());
    }

    /** @test */
    function not_mapping_a_failed_parse()
    {
        $parser = Map::the(Text::is('foo'), fn($result) => strtoupper($result));

        $result = $parser->parse('bar');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected b', $result->data());
        self::assertEquals('bar', $result->unparsed());
    }

    /** @test */
    function mapping_a_successful_full_parse_as_string()
    {
        $parser = Map::the('foo', fn($result) => strtoupper($result));

        $result = $parser->parse('foo');

        self::assertTrue($result->ok());
        self::assertEquals('FOO', $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function mapping_a_successful_full_parse_through_method()
    {
        $parser = Text::is('foo')->map(fn($result) => strtoupper($result));

        $result = $parser->parse('foo');

        self::assertTrue($result->ok());
        self::assertEquals('FOO', $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function mapping_a_successful_partial_parse_through_method()
    {
        $parser = Text::is('foo')->map(fn($result) => strtoupper($result));

        $result = $parser->parse('foo bar');

        self::assertTrue($result->ok());
        self::assertEquals('FOO', $result->data());
        self::assertEquals(' bar', $result->unparsed());
    }

    /** @test */
    function not_mapping_a_failed_parse_through_method()
    {
        $parser = Text::is('foo')->map(fn($result) => strtoupper($result));

        $result = $parser->parse('bar');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected b', $result->data());
        self::assertEquals('bar', $result->unparsed());
    }
}
