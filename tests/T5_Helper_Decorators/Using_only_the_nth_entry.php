<?php declare(strict_types=1);

namespace Stratadox\Parser\Test\T5_Helper_Decorators;

use PHPUnit\Framework\TestCase;
use Stratadox\Parser\Helpers\Item;
use Stratadox\Parser\Parsers\Sequence;

final class Using_only_the_nth_entry extends TestCase
{
    /** @test */
    function using_the_first_of_a_sequence()
    {
        $parser = Item::number(0, Sequence::of('a', 'b', 'c'));

        $result = $parser->parse('abcdef');

        self::assertTrue($result->ok());
        self::assertEquals('a', $result->data());
        self::assertEquals('def', $result->unparsed());
    }

    /** @test */
    function using_the_second_of_a_sequence()
    {
        $parser = Item::number(1, Sequence::of('a', 'b', 'c'));

        $result = $parser->parse('abcdef');

        self::assertTrue($result->ok());
        self::assertEquals('b', $result->data());
        self::assertEquals('def', $result->unparsed());
    }

    /** @test */
    function using_the_second_of_a_sequence_through_method()
    {
        $parser = Sequence::of('a', 'b', 'c')->item(1);

        $result = $parser->parse('abcdef');

        self::assertTrue($result->ok());
        self::assertEquals('b', $result->data());
        self::assertEquals('def', $result->unparsed());
    }

    // @todo refuse non-array content
}
