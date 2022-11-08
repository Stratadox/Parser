<?php declare(strict_types=1);

namespace Stratadox\Parser\Test\T4_Combinator_Shortcuts;

use PHPUnit\Framework\TestCase;
use Stratadox\Parser\Helpers\Split;
use Stratadox\Parser\Parsers\Either;
use Stratadox\Parser\Parsers\Pattern;

final class Optionally_splitting_content extends TestCase
{
    /** @test */
    function splitting_two_foo_by_comma()
    {
        $parser = Split::optional(',', 'foo');

        $result = $parser->parse('foo,foo');

        self::assertTrue($result->ok());
        self::assertEquals(['foo', 'foo'], $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function splitting_a_single_foo_by_comma()
    {
        $parser = Split::optional(',', 'foo');

        $result = $parser->parse('foo');

        self::assertTrue($result->ok());
        self::assertEquals(['foo'], $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function refusing_non_occurring_foo_by_comma()
    {
        $parser = Split::optional(',', 'foo');

        $result = $parser->parse('b');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected b', $result->data());
        self::assertEquals('b', $result->unparsed());
    }

    /** @test */
    function splitting_five_foo_or_bar_by_comma_or_semicolon()
    {
        $parser = Split::optional(Either::of(',',';'), Either::of('foo', 'bar'));

        $result = $parser->parse('foo,bar;foo;foo,bar');

        self::assertTrue($result->ok());
        self::assertEquals(['foo', 'bar', 'foo', 'foo', 'bar'], $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function splitting_digits_by_semicolon_using_method()
    {
        $parser = Pattern::match('\d')->split(';');

        $result = $parser->parse('4;3;8');

        self::assertTrue($result->ok());
        self::assertEquals(['4', '3', '8'], $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function splitting_digits_without_semicolon_using_method()
    {
        $parser = Pattern::match('\d')->split(';');

        $result = $parser->parse('4');

        self::assertTrue($result->ok());
        self::assertEquals(['4'], $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function splitting_results_with_the_first_item_skipped()
    {
        $parser = Pattern::match('\d')->maybe()->split(',');

        $result = $parser->parse(',4');

        self::assertTrue($result->ok());
        self::assertEquals(['4'], $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function splitting_results_with_the_second_item_skipped()
    {
        $parser = Pattern::match('\d')->maybe()->split(',');

        $result = $parser->parse('4,');

        self::assertTrue($result->ok());
        self::assertEquals(['4'], $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function splitting_results_with_the_third_item_skipped()
    {
        $parser = Pattern::match('\d')->maybe()->split(',');

        $result = $parser->parse('4,3,');

        self::assertTrue($result->ok());
        self::assertEquals(['4', '3'], $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function splitting_fully_skipped_results()
    {
        $parser = Pattern::match('\d')->maybe()->split(',');

        $result = $parser->parse(',');

        self::assertTrue($result->ok());
        self::assertEquals([], $result->data());
        self::assertEquals('', $result->unparsed());
    }
}
