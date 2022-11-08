<?php declare(strict_types=1);

namespace Stratadox\Parser\Test\T1_Base;

use PHPUnit\Framework\TestCase;
use Stratadox\Parser\Parsers\Pattern;
use function Stratadox\Parser\pattern;

final class Evaluating_regular_expressions extends TestCase
{
    /** @test */
    function parsing_a_digit()
    {
        $parser = Pattern::match('\d');

        $result = $parser->parse('4');

        self::assertTrue($result->ok());
        self::assertEquals('4', $result->data());
    }

    /** @test */
    function parsing_an_integer()
    {
        $parser = Pattern::match('\d+');

        $result = $parser->parse('123');

        self::assertTrue($result->ok());
        self::assertEquals('123', $result->data());
    }

    /** @test */
    function parsing_a_digit_followed_by_text()
    {
        $parser = Pattern::match('\d');

        $result = $parser->parse('4abc');

        self::assertTrue($result->ok());
        self::assertEquals('4', $result->data());
        self::assertEquals('abc', $result->unparsed());
    }

    /** @test */
    function parsing_an_alphanumeric_sequence()
    {
        $parser = Pattern::match('[a-zA-Z0-9]+');

        $result = $parser->parse('Hello world');

        self::assertTrue($result->ok());
        self::assertEquals('Hello', $result->data());
        self::assertEquals(' world', $result->unparsed());
    }

    /** @test */
    function parsing_a_digit_followed_by_multiline_text()
    {
        $parser = Pattern::match('\d');

        $result = $parser->parse('4abc
        ');

        self::assertTrue($result->ok());
        self::assertEquals('4', $result->data());
        self::assertEquals('abc
        ', $result->unparsed());
    }

    /** @test */
    function parsing_a_specific_capturing_group()
    {
        $parser = Pattern::match('foo(\d)');

        $result = $parser->parse('foo4abc');

        self::assertTrue($result->ok());
        self::assertEquals('4', $result->data());
        self::assertEquals('abc', $result->unparsed());
    }

    /** @test */
    function refusing_a_letter_when_expecting_a_digit()
    {
        $parser = Pattern::match('\d');

        $result = $parser->parse('a');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected a', $result->data());
        self::assertEquals('a', $result->unparsed());
    }

    /** @test */
    function refusing_prepended_matches()
    {
        $parser = Pattern::match('\d');

        $result = $parser->parse('a1');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected a', $result->data());
        self::assertEquals('a1', $result->unparsed());
    }

    /** @test */
    function parsing_a_case_insensitive_string()
    {
        $parser = Pattern::match('string', 'i');

        $result = $parser->parse('sTrInG');

        self::assertTrue($result->ok());
        self::assertEquals('sTrInG', $result->data());
    }

    /** @test */
    function parsing_a_digit_through_function()
    {
        $parser = pattern('\d');

        $result = $parser->parse('4');

        self::assertTrue($result->ok());
        self::assertEquals('4', $result->data());
    }

    /** @test */
    function refusing_a_letter_when_expecting_a_digit_through_function()
    {
        $parser = pattern('\d');

        $result = $parser->parse('a');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected a', $result->data());
        self::assertEquals('a', $result->unparsed());
    }

    /** @test */
    function parsing_a_case_insensitive_string_through_function()
    {
        $parser = pattern('string', 'i');

        $result = $parser->parse('sTrInG');

        self::assertTrue($result->ok());
        self::assertEquals('sTrInG', $result->data());
    }

    /** @test */
    function parsing_multiple_capturing_groups()
    {
        $parser = Pattern::match('foo(\d)(\d)');

        $result = $parser->parse('foo42abc');

        self::assertTrue($result->ok());
        self::assertEquals(['4', '2'], $result->data());
        self::assertEquals('abc', $result->unparsed());
    }
}
