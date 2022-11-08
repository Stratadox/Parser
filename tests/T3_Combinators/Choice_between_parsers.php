<?php declare(strict_types=1);

namespace Stratadox\Parser\Test\T3_Combinators;

use PHPUnit\Framework\TestCase;
use Stratadox\Parser\Parsers\Either;
use Stratadox\Parser\Parsers\Map;
use Stratadox\Parser\Parsers\Pattern;
use Stratadox\Parser\Parsers\Text;

final class Choice_between_parsers extends TestCase
{
    /** @test */
    function parsing_either_a_or_b_as_a()
    {
        $parser = Either::of(Text::is('a'), Text::is('b'));

        $result = $parser->parse('abc');

        self::assertTrue($result->ok());
        self::assertEquals('a', $result->data());
        self::assertEquals('bc', $result->unparsed());
    }

    /** @test */
    function parsing_either_a_or_b_as_b()
    {
        $parser = Either::of(Text::is('a'), Text::is('b'));

        $result = $parser->parse('bcd');

        self::assertTrue($result->ok());
        self::assertEquals('b', $result->data());
        self::assertEquals('cd', $result->unparsed());
    }

    /** @test */
    function not_parsing_either_a_or_b_as_c()
    {
        $parser = Either::of(Text::is('a'), Text::is('b'));

        $result = $parser->parse('c');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected c', $result->data());
        self::assertEquals('c', $result->unparsed());
    }

    /** @test */
    function parsing_either_digit_or_abc_as_4()
    {
        $parser = Either::of(Pattern::match('\d'), Text::is('abc'));

        $result = $parser->parse('4a');

        self::assertTrue($result->ok());
        self::assertEquals('4', $result->data());
        self::assertEquals('a', $result->unparsed());
    }

    /** @test */
    function parsing_either_digit_or_abc_as_abc()
    {
        $parser = Either::of(Pattern::match('\d'), Text::is('abc'));

        $result = $parser->parse('abcdefghi');

        self::assertTrue($result->ok());
        self::assertEquals('abc', $result->data());
        self::assertEquals('defghi', $result->unparsed());
    }

    /** @test */
    function not_parsing_either_digit_or_abc_as_def()
    {
        $parser = Either::of(Pattern::match('\d'), Text::is('abc'));

        $result = $parser->parse('def');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected d', $result->data());
        self::assertEquals('def', $result->unparsed());
    }

    /** @test */
    function parsing_either_one_of_foo_bar_baz_as_foo()
    {
        $parser = Either::of('foo', 'bar', 'baz');

        $result = $parser->parse('foo etc');

        self::assertTrue($result->ok());
        self::assertEquals('foo', $result->data());
        self::assertEquals(' etc', $result->unparsed());
    }

    /** @test */
    function parsing_either_one_of_foo_bar_baz_as_bar()
    {
        $parser = Either::of('foo', 'bar', 'baz');

        $result = $parser->parse('barrel');

        self::assertTrue($result->ok());
        self::assertEquals('bar', $result->data());
        self::assertEquals('rel', $result->unparsed());
    }

    /** @test */
    function not_parsing_either_one_of_foo_bar_baz_as_abc()
    {
        $parser = Either::of('foo', 'bar', 'baz');

        $result = $parser->parse('abc');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected a', $result->data());
        self::assertEquals('abc', $result->unparsed());
    }

    /** @test */
    function not_parsing_either_one_of_foo_bar_baz_as_banana()
    {
        $parser = Either::of('foo', 'bar', 'baz');

        $result = $parser->parse('banana');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected n', $result->data());
        self::assertEquals('nana', $result->unparsed());
    }

    /** @test */
    function parsing_either_a_or_b_as_a_through_method()
    {
        $parser = Text::is('a')->or(Text::is('b'));

        $result = $parser->parse('abc');

        self::assertTrue($result->ok());
        self::assertEquals('a', $result->data());
        self::assertEquals('bc', $result->unparsed());
    }

    /** @test */
    function parsing_either_a_or_b_as_a_through_method_using_text()
    {
        $parser = Text::is('a')->or('b');

        $result = $parser->parse('abc');

        self::assertTrue($result->ok());
        self::assertEquals('a', $result->data());
        self::assertEquals('bc', $result->unparsed());
    }

    /** @test */
    function parsing_either_one_of_foo_bar_baz_as_foo_through_method()
    {
        $parser = Text::is('foo')->or('bar', 'baz');

        $result = $parser->parse('foo etc');

        self::assertTrue($result->ok());
        self::assertEquals('foo', $result->data());
        self::assertEquals(' etc', $result->unparsed());
    }

    /** @test */
    function parsing_either_one_of_foo_bar_baz_as_foo_through_method_chain()
    {
        $parser = Text::is('foo')->or('bar')->or('baz');

        $result = $parser->parse('foo etc');

        self::assertTrue($result->ok());
        self::assertEquals('foo', $result->data());
        self::assertEquals(' etc', $result->unparsed());
    }

    /** @test */
    function parsing_the_first_valid_choice()
    {
        $parser = Text::is('x')
            ->or(Map::the('foo', fn($x)=>'FOO!'))
            ->or(Map::the('foo', fn($x)=>'BAR!'));

        $result = $parser->parse('foo etc');

        self::assertTrue($result->ok());
        self::assertEquals('FOO!', $result->data());
        self::assertEquals(' etc', $result->unparsed());
    }

    /** @test */
    function using_a_single_choice_combinator()
    {
        self::assertEquals(
            Either::of('foo', 'bar', 'baz'),
            Text::is('foo')->or('bar')->or('baz'),
        );
    }
}
