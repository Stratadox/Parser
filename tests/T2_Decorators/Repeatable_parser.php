<?php declare(strict_types=1);

namespace Stratadox\Parser\Test\T2_Decorators;

use PHPUnit\Framework\TestCase;
use Stratadox\Parser\Parsers\Ignore;
use Stratadox\Parser\Parsers\Repeatable;
use Stratadox\Parser\Parsers\Text;

final class Repeatable_parser extends TestCase
{
    /** @test */
    function any_number_of_the_letter_a_in_a()
    {
        $parser = Repeatable::parser(Text::is('a'));

        $result = $parser->parse('a');

        self::assertTrue($result->ok());
        self::assertEquals(['a'], $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function any_number_of_the_letter_a_in_aaa()
    {
        $parser = Repeatable::parser(Text::is('a'));

        $result = $parser->parse('aaa');

        self::assertTrue($result->ok());
        self::assertEquals(['a', 'a', 'a'], $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function any_number_of_the_letter_a_in_aaabc()
    {
        $parser = Repeatable::parser(Text::is('a'));

        $result = $parser->parse('aaabc');

        self::assertTrue($result->ok());
        self::assertEquals(['a', 'a', 'a'], $result->data());
        self::assertEquals('bc', $result->unparsed());
    }

    /** @test */
    function any_number_of_the_letter_a_in_b()
    {
        $parser = Repeatable::parser(Text::is('a'));

        $result = $parser->parse('b');

        self::assertTrue($result->ok());
        self::assertEquals([], $result->data());
        self::assertEquals('b', $result->unparsed());
    }

    /** @test */
    function any_number_of_the_ignored_letter_a_in_aaabc()
    {
        $parser = Repeatable::parser(Ignore::the('a'));

        $result = $parser->parse('aaabc');

        self::assertTrue($result->ok());
        self::assertEquals([], $result->data());
        self::assertEquals('bc', $result->unparsed());
    }

    /** @test */
    function any_number_of_the_letter_a_in_aaa_as_string()
    {
        $parser = Repeatable::parser('a');

        $result = $parser->parse('aaa');

        self::assertTrue($result->ok());
        self::assertEquals(['a', 'a', 'a'], $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function any_number_of_the_letter_a_in_aaa_through_method()
    {
        $parser = Text::is('a')->repeatable();

        $result = $parser->parse('aaa');

        self::assertTrue($result->ok());
        self::assertEquals(['a', 'a', 'a'], $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function any_number_of_the_letter_a_in_aaa_as_string_through_method()
    {
        $parser = Text::is('a')->repeatableString();

        $result = $parser->parse('aaa');

        self::assertTrue($result->ok());
        self::assertEquals('aaa', $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function any_number_of_the_sequence_with_space_in_a_text()
    {
        $parser = Text::is(' x')->repeatable();

        $result = $parser->parse(' x x y x');

        self::assertTrue($result->ok());
        self::assertEquals([' x', ' x'], $result->data());
        self::assertEquals(' y x', $result->unparsed());
    }
}
