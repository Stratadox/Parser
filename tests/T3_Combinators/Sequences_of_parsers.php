<?php declare(strict_types=1);

namespace Stratadox\Parser\Test\T3_Combinators;

use PHPUnit\Framework\TestCase;
use Stratadox\Parser\Parsers\Ignore;
use Stratadox\Parser\Parsers\Pattern;
use Stratadox\Parser\Parsers\Sequence;
use Stratadox\Parser\Parsers\Text;

final class Sequences_of_parsers extends TestCase
{
    /** @test */
    function parsing_the_sequence_a_b_c_in_abcdef()
    {
        $parser = Sequence::of('a', 'b', 'c');

        $result = $parser->parse('abcdef');

        self::assertTrue($result->ok());
        self::assertEquals(['a', 'b', 'c'], $result->data());
        self::assertEquals('def', $result->unparsed());
    }

    /** @test */
    function refusing_the_sequence_a_b_c_in_foo()
    {
        $parser = Sequence::of('a', 'b', 'c');

        $result = $parser->parse('foo');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected f', $result->data());
        self::assertEquals('foo', $result->unparsed());
    }

    /** @test */
    function refusing_the_sequence_a_b_c_in_abd()
    {
        $parser = Sequence::of('a', 'b', 'c');

        $result = $parser->parse('abd');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected d', $result->data());
        self::assertEquals('d', $result->unparsed());
    }

    /** @test */
    function refusing_the_sequence_foo_bar_in_foo_bank()
    {
        $parser = Sequence::of('foo', ' ', 'bar');

        $result = $parser->parse('foo bank');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected n', $result->data());
        self::assertEquals('nk', $result->unparsed());
    }

    /** @test */
    function parsing_the_sequence_of_x_digit_x_as_x3xy()
    {
        $parser = Sequence::of('x', Pattern::match('\d'), 'x');

        $result = $parser->parse('x3xy');

        self::assertTrue($result->ok());
        self::assertEquals(['x', '3', 'x'], $result->data());
        self::assertEquals('y', $result->unparsed());
    }

    /** @test */
    function parsing_the_sequence_of_characters_where_some_are_ignored()
    {
        $parser = Sequence::of(Ignore::the('abc'), 'x', Ignore::the('y'), 'z');

        $result = $parser->parse('abcxyz');

        self::assertTrue($result->ok());
        self::assertEquals(['x', 'z'], $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function parsing_the_sequence_a_b_c_in_abcdef_through_method()
    {
        $parser = Text::is('a')->andThen('b', 'c');

        $result = $parser->parse('abcdef');

        self::assertTrue($result->ok());
        self::assertEquals(['a', 'b', 'c'], $result->data());
        self::assertEquals('def', $result->unparsed());
    }

    /** @test */
    function parsing_the_sequence_a_b_c_in_abcdef_through_method_chain()
    {
        $parser = Text::is('a')->andThen('b')->andThen('c');

        $result = $parser->parse('abcdef');

        self::assertTrue($result->ok());
        self::assertEquals(['a', 'b', 'c'], $result->data());
        self::assertEquals('def', $result->unparsed());
    }
}
