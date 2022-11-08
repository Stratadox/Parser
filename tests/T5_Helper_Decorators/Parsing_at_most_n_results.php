<?php declare(strict_types=1);

namespace Stratadox\Parser\Test\T5_Helper_Decorators;

use PHPUnit\Framework\TestCase;
use Stratadox\Parser\Helpers\AtMost;
use Stratadox\Parser\Parsers\Repeatable;

final class Parsing_at_most_n_results extends TestCase
{
    /** @test */
    function successfully_parsing_2_matches()
    {
        $parser = AtMost::results(2, Repeatable::parser('x'));

        $result = $parser->parse('xxy');

        self::assertTrue($result->ok());
        self::assertEquals(['x', 'x'], $result->data());
        self::assertEquals('y', $result->unparsed());
    }

    /** @test */
    function successfully_parsing_0_matches()
    {
        $parser = AtMost::results(2, Repeatable::parser('x'));

        $result = $parser->parse('y');

        self::assertTrue($result->ok());
        self::assertEquals([], $result->data());
        self::assertEquals('y', $result->unparsed());
    }

    /** @test */
    function refusing_3_matches_when_at_most_2_are_allowed()
    {
        $parser = AtMost::results(2, Repeatable::parser('x'));

        $result = $parser->parse('xxxyz');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected x', $result->data());
        self::assertEquals('xyz', $result->unparsed());
    }

    /** @test */
    function refusing_4_matches_when_at_most_3_are_allowed()
    {
        $parser = AtMost::results(3, Repeatable::parser('x'));

        $result = $parser->parse('xxxxz');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected x', $result->data());
        self::assertEquals('xz', $result->unparsed());
    }

    /** @test */
    function successfully_parsing_2_matches_through_method()
    {
        $parser = Repeatable::parser('x')->atMost(2);

        $result = $parser->parse('xxy');

        self::assertTrue($result->ok());
        self::assertEquals(['x', 'x'], $result->data());
        self::assertEquals('y', $result->unparsed());
    }

    /** @test */
    function refusing_4_matches_when_at_most_3_are_allowed_through_method()
    {
        $parser = Repeatable::parser('x')->atMost(3);

        $result = $parser->parse('xxxxz');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected x', $result->data());
        self::assertEquals('xz', $result->unparsed());
    }

    // @todo refuse non-array content
}
