<?php declare(strict_types=1);

namespace Stratadox\Parser\Test\T2_Decorators;

use PHPUnit\Framework\TestCase;
use Stratadox\Parser\Parsers\Any;
use Stratadox\Parser\Parsers\Except;
use Stratadox\Parser\Parsers\Pattern;

final class Conditional_parsers extends TestCase
{
    /** @test */
    function refusing_4_in_any_digit_except_4()
    {
        $parser = Except::for('4', Pattern::match('\d'));

        $result = $parser->parse('4');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected 4', $result->data());
        self::assertEquals('4', $result->unparsed());
    }

    /** @test */
    function parsing_5_in_any_digit_except_4()
    {
        $parser = Except::for('4', Pattern::match('\d'));

        $result = $parser->parse('5');

        self::assertTrue($result->ok());
        self::assertEquals('5', $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function refusing_4_in_any_digit_except_4_through_method()
    {
        $parser = Pattern::match('\d')->except('4');

        $result = $parser->parse('4');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected 4', $result->data());
        self::assertEquals('4', $result->unparsed());
    }

    /** @test */
    function parsing_5_in_any_digit_except_4_through_method()
    {
        $parser = Pattern::match('\d')->except('4');

        $result = $parser->parse('5');

        self::assertTrue($result->ok());
        self::assertEquals('5', $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function any_character_except_abc()
    {
        $parser = Any::symbol()->except('abc');

        $result = $parser->parse('abc');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected a', $result->data());
        self::assertEquals('abc', $result->unparsed());
    }
}
