<?php declare(strict_types=1);

namespace Stratadox\Parser\Test\T5_Helper_Decorators;

use PHPUnit\Framework\TestCase;
use Stratadox\Parser\Helpers\AtLeast;
use Stratadox\Parser\Parsers\Repeatable;

final class Parsing_at_least_n_results extends TestCase
{
    /** @test */
    function successfully_parsing_2_matches()
    {
        $parser = AtLeast::results(2, Repeatable::parser('x'));

        $result = $parser->parse('xxy');

        self::assertTrue($result->ok());
        self::assertEquals(['x', 'x'], $result->data());
        self::assertEquals('y', $result->unparsed());
    }

    /** @test */
    function successfully_parsing_5_matches()
    {
        $parser = AtLeast::results(2, Repeatable::parser('x'));

        $result = $parser->parse('xxxxxy');

        self::assertTrue($result->ok());
        self::assertEquals(['x', 'x', 'x', 'x', 'x'], $result->data());
        self::assertEquals('y', $result->unparsed());
    }

    /** @test */
    function refusing_1_match_when_at_least_2_are_needed()
    {
        $parser = AtLeast::results(2, Repeatable::parser('x'));

        $result = $parser->parse('xyz');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected y', $result->data());
        self::assertEquals('yz', $result->unparsed());
    }

    /** @test */
    function refusing_2_matches_when_at_least_3_are_needed()
    {
        $parser = AtLeast::results(3, Repeatable::parser('x'));

        $result = $parser->parse('xxz');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected z', $result->data());
        self::assertEquals('z', $result->unparsed());
    }

    /** @test */
    function refusing_2_matches_when_at_least_3_are_needed_through_method()
    {
        $parser = Repeatable::parser('x')->atLeast(3);

        $result = $parser->parse('xxz');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected z', $result->data());
        self::assertEquals('z', $result->unparsed());
    }

    /** @test */
    function successfully_parsing_5_matches_through_method()
    {
        $parser = Repeatable::parser('x')->atLeast(3);

        $result = $parser->parse('xxxxxy');

        self::assertTrue($result->ok());
        self::assertEquals(['x', 'x', 'x', 'x', 'x'], $result->data());
        self::assertEquals('y', $result->unparsed());
    }

    // @todo refuse non-array content
}
