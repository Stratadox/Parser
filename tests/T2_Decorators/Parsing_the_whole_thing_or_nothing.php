<?php declare(strict_types=1);

namespace Stratadox\Parser\Test\T2_Decorators;

use PHPUnit\Framework\TestCase;
use Stratadox\Parser\Parsers\AllOrNothing;

final class Parsing_the_whole_thing_or_nothing extends TestCase
{
    /** @test */
    function restoring_the_unparsed_bit_upon_error()
    {
        $parser = AllOrNothing::in('aaa');

        $result = $parser->parse('aab');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected a', $result->data());
        self::assertEquals('aab', $result->unparsed());
    }

    /** @test */
    function not_altering_a_successful_parse()
    {
        $parser = AllOrNothing::in('aaa');

        $result = $parser->parse('aaab');

        self::assertTrue($result->ok());
        self::assertEquals('aaa', $result->data());
        self::assertEquals('b', $result->unparsed());
    }
}
