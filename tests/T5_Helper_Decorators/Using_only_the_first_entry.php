<?php declare(strict_types=1);

namespace Stratadox\Parser\Test\T5_Helper_Decorators;

use PHPUnit\Framework\TestCase;
use Stratadox\Parser\Helpers\First;
use Stratadox\Parser\Parsers\Repeatable;
use Stratadox\Parser\Parsers\Sequence;
use Stratadox\Parser\Parsers\Text;

final class Using_only_the_first_entry extends TestCase
{
    /** @test */
    function using_the_first_of_a_sequence()
    {
        $parser = First::of(Sequence::of('a', 'b', 'c'));

        $result = $parser->parse('abcdef');

        self::assertTrue($result->ok());
        self::assertEquals('a', $result->data());
        self::assertEquals('def', $result->unparsed());
    }

    /** @test */
    function using_the_whole_result_if_not_an_array()
    {
        $parser = First::of(Text::is('abc'));

        $result = $parser->parse('abcdef');

        self::assertTrue($result->ok());
        self::assertEquals('abc', $result->data());
        self::assertEquals('def', $result->unparsed());
    }

    /** @test */
    function using_the_whole_result_for_an_empty_array()
    {
        $parser = First::of(Repeatable::parser(Text::is('a')));

        $result = $parser->parse('def');

        self::assertTrue($result->ok());
        self::assertEquals([], $result->data());
        self::assertEquals('def', $result->unparsed());
    }

    /** @test */
    function using_the_first_of_a_sequence_through_method()
    {
        $parser = Sequence::of('a', 'b', 'c')->first();

        $result = $parser->parse('abcdef');

        self::assertTrue($result->ok());
        self::assertEquals('a', $result->data());
        self::assertEquals('def', $result->unparsed());
    }
}
