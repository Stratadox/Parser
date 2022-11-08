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

        $result = $parser->parse('😀!');

        self::assertTrue($result->ok());
        self::assertEquals('😀', $result->data());
        self::assertEquals('!', $result->unparsed());
    }

    /** @test */
    function parsing_a_korean_character()
    {
        $parser = Any::symbol();

        $result = $parser->parse('반갑다 세상아');

        self::assertTrue($result->ok());
        self::assertEquals('반', $result->data());
        self::assertEquals('갑다 세상아', $result->unparsed());
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
