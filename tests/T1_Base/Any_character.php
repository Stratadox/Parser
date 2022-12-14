<?php declare(strict_types=1);

namespace Stratadox\Parser\Test\T1_Base;

use PHPUnit\Framework\TestCase;
use Stratadox\Parser\Parsers\Any;
use function Stratadox\Parser\any;

final class Any_character extends TestCase
{
    /** @test */
    function parsing_the_letter_a()
    {
        $parser = Any::symbol();

        $result = $parser->parse('a');

        self::assertTrue($result->ok());
        self::assertEquals('a', $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function parsing_only_one_letter_a()
    {
        $parser = Any::symbol();

        $result = $parser->parse('aaa');

        self::assertTrue($result->ok());
        self::assertEquals('a', $result->data());
        self::assertEquals('aa', $result->unparsed());
    }

    /** @test */
    function parsing_an_emoji()
    {
        $parser = Any::symbol();

        $result = $parser->parse('π!');

        self::assertTrue($result->ok());
        self::assertEquals('π', $result->data());
        self::assertEquals('!', $result->unparsed());
    }

    /** @test */
    function parsing_a_korean_character()
    {
        $parser = Any::symbol();

        $result = $parser->parse('λ°κ°λ€ μΈμμ');

        self::assertTrue($result->ok());
        self::assertEquals('λ°', $result->data());
        self::assertEquals('κ°λ€ μΈμμ', $result->unparsed());
    }

    /** @test */
    function refusing_empty_input()
    {
        $parser = Any::symbol();

        $result = $parser->parse('');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected end', $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function parsing_the_letter_a_through_function()
    {
        $parser = any();

        $result = $parser->parse('a');

        self::assertTrue($result->ok());
        self::assertEquals('a', $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function refusing_empty_input_through_function()
    {
        $parser = any();

        $result = $parser->parse('');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected end', $result->data());
        self::assertEquals('', $result->unparsed());
    }
}
