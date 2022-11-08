<?php declare(strict_types=1);

namespace Stratadox\Parser\Test\T4_Combinator_Shortcuts;

use PHPUnit\Framework\TestCase;
use Stratadox\Parser\Helpers\Between;
use Stratadox\Parser\Parsers\Maybe;
use Stratadox\Parser\Parsers\Pattern;

final class Content_between_delimiters extends TestCase
{
    /** @test */
    function parsing_the_z_between_parentheses()
    {
        $parser = Between::these('(', ')', 'z');

        $result = $parser->parse('(z)');

        self::assertTrue($result->ok());
        self::assertEquals('z', $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function parsing_the_abc_between_quotes()
    {
        $parser = Between::these('"', '"', 'abc');

        $result = $parser->parse('"abc"');

        self::assertTrue($result->ok());
        self::assertEquals('abc', $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function refusing_the_z_without_parentheses()
    {
        $parser = Between::these('(', ')', 'z');

        $result = $parser->parse('z');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected z', $result->data());
        self::assertEquals('z', $result->unparsed());
    }

    /** @test */
    function parsing_the_z_without_optional_parentheses()
    {
        $parser = Between::these(Maybe::that('('), Maybe::that(')'), 'z');

        $result = $parser->parse('z');

        self::assertTrue($result->ok());
        self::assertEquals('z', $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function parsing_a_digit_between_parentheses_through_method()
    {
        $parser = Pattern::match('\d')->between('(', ')');

        $result = $parser->parse('(4)');

        self::assertTrue($result->ok());
        self::assertEquals('4', $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function refusing_the_digit_without_parentheses_through_method()
    {
        $parser = Pattern::match('\d')->between('(', ')');

        $result = $parser->parse('4');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected 4', $result->data());
        self::assertEquals('4', $result->unparsed());
    }

    /** @test */
    function parsing_the_digit_without_quotes_through_method_with_single_parameter()
    {
        $parser = Pattern::match('\d')->between('"');

        $result = $parser->parse('"4"');

        self::assertTrue($result->ok());
        self::assertEquals('4', $result->data());
        self::assertEquals('', $result->unparsed());
    }
}
