<?php

namespace Stratadox\Parser\Test\T1_Base;

use PHPUnit\Framework\TestCase;
use Stratadox\Parser\Parsers\Text;
use function Stratadox\Parser\text;

final class Characters_and_strings extends TestCase
{
    /** @test */
    function parsing_the_letter_a()
    {
        $parser = Text::is('a');

        $result = $parser->parse('a');

        self::assertTrue($result->ok());
        self::assertEquals('a', $result->data());
    }

    /** @test */
    function parsing_the_string_abc()
    {
        $parser = Text::is('abc');

        $result = $parser->parse('abc');

        self::assertTrue($result->ok());
        self::assertEquals('abc', $result->data());
    }

    /** @test */
    function parsing_the_letter_a_as_part_of_the_string_abc()
    {
        $parser = Text::is('a');

        $result = $parser->parse('abc');

        self::assertTrue($result->ok());
        self::assertEquals('a', $result->data());
        self::assertEquals('bc', $result->unparsed());
    }

    /** @test */
    function parsing_an_emoji()
    {
        $parser = Text::is('😀');

        $result = $parser->parse('😀!');

        self::assertTrue($result->ok());
        self::assertEquals('😀', $result->data());
        self::assertEquals('!', $result->unparsed());
    }

    /** @test */
    function parsing_korean()
    {
        $parser = Text::is('반갑다');

        $result = $parser->parse('반갑다 세상아');

        self::assertTrue($result->ok());
        self::assertEquals('반갑다', $result->data());
        self::assertEquals(' 세상아', $result->unparsed());
    }

    /** @test */
    function parsing_a_substring()
    {
        $parser = Text::is('abc');

        $result = $parser->parse('abcdef');

        self::assertTrue($result->ok());
        self::assertEquals('abc', $result->data());
        self::assertEquals('def', $result->unparsed());
    }

    /** @test */
    function refusing_a_letter_not_part_of_a_string()
    {
        $parser = Text::is('z');

        $result = $parser->parse('abc');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected a', $result->data());
        self::assertEquals('abc', $result->unparsed());
    }

    /** @test */
    function refusing_a_letter_not_first_in_a_string()
    {
        $parser = Text::is('c');

        $result = $parser->parse('abc');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected a', $result->data());
        self::assertEquals('abc', $result->unparsed());
    }

    /** @test */
    function refusing_a_letter_in_an_empty_string()
    {
        $parser = Text::is('abc');

        $result = $parser->parse('');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected end', $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function indicating_where_the_input_diverges()
    {
        $parser = Text::is('aaa');

        $result = $parser->parse('aab');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected b', $result->data());
        self::assertEquals('b', $result->unparsed());
    }

    /** @test */
    function indicating_where_the_input_diverges_in_korean()
    {
        $parser = Text::is('반갑다 세상아');

        $result = $parser->parse('반갑다 세!');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected !', $result->data());
        self::assertEquals('!', $result->unparsed());
    }

    /** @test */
    function parsing_the_letter_a_through_function()
    {
        $parser = text('a');

        $result = $parser->parse('a');

        self::assertTrue($result->ok());
        self::assertEquals('a', $result->data());
    }

    /** @test */
    function refusing_the_letter_z_when_parsing_an_a()
    {
        $parser = text('a');

        $result = $parser->parse('z');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected z', $result->data());
        self::assertEquals('z', $result->unparsed());
    }
}
