<?php declare(strict_types=1);

namespace Stratadox\Parser\Test\T4_Combinator_Shortcuts;

use PHPUnit\Framework\TestCase;
use Stratadox\Parser\Helpers\Between;

final class Content_between_escaped_delimiters extends TestCase
{
    /** @test */
    function parsing_the_text_between_quotes()
    {
        $parser = Between::escaped('"', '"', '\\');

        $result = $parser->parse('"abc"');

        self::assertTrue($result->ok());
        self::assertEquals('abc', $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function parsing_the_text_with_escaped_quote_between_quotes()
    {
        $parser = Between::escaped('"', '"', '\\');

        $result = $parser->parse('"ab\\"c"');

        self::assertTrue($result->ok());
        self::assertEquals('ab"c', $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function parsing_the_text_with_escaped_quote_between_quotes_using_quote_as_escape()
    {
        $parser = Between::escaped('{', '}', '}');

        $result = $parser->parse('{ab}}c}');

        self::assertEquals('', $result->unparsed());
        self::assertEquals('ab}c', $result->data());
        self::assertTrue($result->ok());
    }

    /** @test */
    function parsing_the_text_with_escaped_escaped_quote_between_quotes()
    {
        $parser = Between::escaped('"', '"', '\\');

        $result = $parser->parse('"ab\\\\"c"');

        self::assertTrue($result->ok());
        self::assertEquals('ab\\', $result->data());
        self::assertEquals('c"', $result->unparsed());
    }

    /** @test */
    function parsing_the_text_using_different_escape_for_the_escape_character()
    {
        $parser = Between::escaped('"', '"', '|', '!!');

        $result = $parser->parse('"ab!!|!!|"');

        self::assertTrue($result->ok());
        self::assertEquals('ab||', $result->data());
        self::assertEquals('', $result->unparsed());
    }
}
