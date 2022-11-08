<?php declare(strict_types=1);

namespace Stratadox\Parser\Test\T5_Helper_Decorators;

use PHPUnit\Framework\TestCase;
use Stratadox\Parser\Helpers\Join;
use Stratadox\Parser\Parsers\Sequence;

final class Joining_the_parsed_result extends TestCase
{
    /** @test */
    function joining_the_results_of_a_sequence()
    {
        $parser = Join::the(Sequence::of('a', 'b', 'c'));

        $result = $parser->parse('abcdef');

        self::assertTrue($result->ok());
        self::assertEquals('abc', $result->data());
        self::assertEquals('def', $result->unparsed());
    }

    /** @test */
    function no_transformation_when_joining_a_text()
    {
        $parser = Join::the('abc');

        $result = $parser->parse('abcdef');

        self::assertTrue($result->ok());
        self::assertEquals('abc', $result->data());
        self::assertEquals('def', $result->unparsed());
    }

    /** @test */
    function joining_the_results_of_a_sequence_with_glue()
    {
        $parser = Join::with(', ', Sequence::of('a', 'b', 'c'));

        $result = $parser->parse('abcdef');

        self::assertTrue($result->ok());
        self::assertEquals('a, b, c', $result->data());
        self::assertEquals('def', $result->unparsed());
    }

    /** @test */
    function no_transformation_when_joining_a_text_with_glue()
    {
        $parser = Join::with(', ', 'abc');

        $result = $parser->parse('abcdef');

        self::assertTrue($result->ok());
        self::assertEquals('abc', $result->data());
        self::assertEquals('def', $result->unparsed());
    }

    /** @test */
    function joining_the_results_of_a_sequence_through_method()
    {
        $parser = Sequence::of('a', 'b', 'c')->join();

        $result = $parser->parse('abcdef');

        self::assertTrue($result->ok());
        self::assertEquals('abc', $result->data());
        self::assertEquals('def', $result->unparsed());
    }

    /** @test */
    function joining_the_results_of_a_sequence_with_glue_through_method()
    {
        $parser = Sequence::of('a', 'b', 'c')->join(', ');

        $result = $parser->parse('abcdef');

        self::assertTrue($result->ok());
        self::assertEquals('a, b, c', $result->data());
        self::assertEquals('def', $result->unparsed());
    }
}
