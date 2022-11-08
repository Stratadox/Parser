<?php declare(strict_types=1);

namespace Stratadox\Parser\Test\T2_Decorators;

use PHPUnit\Framework\TestCase;
use Stratadox\Parser\Parsers\End;
use Stratadox\Parser\Parsers\Pattern;

final class Last_part_of_content extends TestCase
{
    /** @test */
    function parsing_a_full_match()
    {
        $parser = End::with(Pattern::match('\d'));

        $result = $parser->parse('4');

        self::assertTrue($result->ok());
        self::assertEquals('4', $result->data());
    }

    /** @test */
    function refusing_a_partial_match()
    {
        $parser = End::with(Pattern::match('\d'));

        $result = $parser->parse('41');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected 1', $result->data());
    }

    /** @test */
    function parsing_a_full_match_through_method()
    {
        $parser = Pattern::match('\d')->end();

        $result = $parser->parse('4');

        self::assertTrue($result->ok());
        self::assertEquals('4', $result->data());
    }

    /** @test */
    function refusing_a_partial_match_through_method()
    {
        $parser = Pattern::match('\d')->end();

        $result = $parser->parse('41');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected 1', $result->data());
    }
}
