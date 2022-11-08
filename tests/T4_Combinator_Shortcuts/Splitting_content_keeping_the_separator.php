<?php declare(strict_types=1);

namespace Stratadox\Parser\Test\T4_Combinator_Shortcuts;

use PHPUnit\Framework\TestCase;
use Stratadox\Parser\Helpers\Split;
use Stratadox\Parser\Parsers\Either;
use Stratadox\Parser\Parsers\Pattern;

final class Splitting_content_keeping_the_separator extends TestCase
{
    /** @test */
    function splitting_one_times_two()
    {
        $parser = Split::keep('*', Pattern::match('\d+'));

        $result = $parser->parse('1*2');

        self::assertTrue($result->ok());
        self::assertEquals(['*' => [1, 2]], $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function splitting_one_times_two_times_three()
    {
        $parser = Split::keep('*', Pattern::match('\d+'));

        $result = $parser->parse('1*2*3');

        self::assertTrue($result->ok());
        self::assertEquals(['*' => [['*' => [1, 2]], 3]], $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function splitting_one_times_two_divided_by_three()
    {
        $parser = Split::keep(Either::of('*', '/'), Pattern::match('\d+'));

        $result = $parser->parse('1*2/3');

        self::assertTrue($result->ok());
        self::assertEquals(['/' => [['*' => ['1', '2']], '3']], $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function splitting_one_times_two_divided_by_three_using_shorthand()
    {
        $parser = Split::keep(['*', '/'], Pattern::match('\d+'));

        $result = $parser->parse('1*2/3');

        self::assertTrue($result->ok());
        self::assertEquals(['/' => [['*' => ['1', '2']], '3']], $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function refusing_missing_separators()
    {
        $parser = Split::keep(Either::of('*', '/'), Pattern::match('\d+'));

        $result = $parser->parse('1');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected end', $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function splitting_one_times_two_divided_by_three_using_mapping()
    {
        $parser = Split::keep(['*', '/'], Pattern::match('\d+'), fn($op, $l, $r) => [
            'op' => $op,
            'arg' => [$l, $r],
        ]);

        $result = $parser->parse('1*2/3');

        self::assertTrue($result->ok());
        self::assertEquals([
            'op' => '/',
            'arg' => [
                [
                    'op' => '*',
                    'arg' => ['1', '2'],
                ],
                '3',
            ],
        ], $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function splitting_9_divided_by_3_plus_2_using_mapping()
    {
        $md = Pattern::match('\d+')->keepSplit(['*', '/'], fn($op, $l, $r) => [
            'op' => $op,
            'arg' => [$l, $r],
        ]);
        $parser = $md->or(Pattern::match('\d+'))->keepSplit(['+', '-'], fn($op, $l, $r) => [
            'op' => $op,
            'arg' => [$l, $r],
        ]);

        $result = $parser->parse('9*3+2');

        self::assertTrue($result->ok());
        self::assertEquals([
            'op' => '+',
            'arg' => [
                [
                    'op' => '*',
                    'arg' => ['9', '3'],
                ],
                '2',
            ],
        ], $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function splitting_one_times_two_divided_by_three_using_method()
    {
        $parser = Pattern::match('\d+')->keepSplit(['*', '/']);

        $result = $parser->parse('1*2/3');

        self::assertTrue($result->ok());
        self::assertEquals(['/' => [['*' => ['1', '2']], '3']], $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function splitting_one_times_two_divided_by_three_using_method_and_mapping()
    {
        $parser = Pattern::match('\d+')->keepSplit(['*', '/'], fn($op, $l, $r) => [
            'op' => $op,
            'arg' => [$l, $r],
        ]);

        $result = $parser->parse('1*2/3');

        self::assertTrue($result->ok());
        self::assertEquals([
            'op' => '/',
            'arg' => [
                [
                    'op' => '*',
                    'arg' => ['1', '2'],
                ],
                '3',
            ],
        ], $result->data());
        self::assertEquals('', $result->unparsed());
    }
}
