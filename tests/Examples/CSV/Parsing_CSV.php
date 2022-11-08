<?php declare(strict_types=1);

namespace Stratadox\Parser\Test\Examples\CSV;

use PHPUnit\Framework\TestCase;

final class Parsing_CSV extends TestCase
{
    /** @test */
    function parsing_comma_separated_values()
    {
        $parser = CSV::parser();

        $result = $parser->parse(<<<'CSV'
foo,bar,baz
1,2,3
4,5,6
CSV
);

        self::assertTrue($result->ok());
        self::assertEquals([
            ['foo' => '1', 'bar' => '2', 'baz' => '3'],
            ['foo' => '4', 'bar' => '5', 'baz' => '6'],
        ], $result->data());
    }

    /** @test */
    function parsing_comma_separated_values_with_trailing_newlines()
    {
        $parser = CSV::parser();

        $result = $parser->parse(<<<'CSV'
foo,bar,baz
1,2,3
4,5,6


CSV
);

        self::assertTrue($result->ok());
        self::assertEquals([
            ['foo' => '1', 'bar' => '2', 'baz' => '3'],
            ['foo' => '4', 'bar' => '5', 'baz' => '6'],
        ], $result->data());
    }

    /** @test */
    function parsing_comma_separated_values_with_quotes()
    {
        $parser = CSV::parser();

        $result = $parser->parse(<<<'CSV'
"foo","bar","baz"
"1","2","3"
"4","5","6"
CSV
);

        self::assertTrue($result->ok());
        self::assertEquals([
            ['foo' => '1', 'bar' => '2', 'baz' => '3'],
            ['foo' => '4', 'bar' => '5', 'baz' => '6'],
        ], $result->data());
    }

    /** @test */
    function parsing_comma_separated_values_with_some_quotes()
    {
        $parser = CSV::parser();

        $result = $parser->parse(<<<'CSV'
foo,bar,baz
"1","2","3"
"4",5,"6"
CSV
);

        self::assertTrue($result->ok());
        self::assertEquals([
            ['foo' => '1', 'bar' => '2', 'baz' => '3'],
            ['foo' => '4', 'bar' => '5', 'baz' => '6'],
        ], $result->data());
    }

    /** @test */
    function parsing_comma_separated_values_with_empty_values()
    {
        $parser = CSV::parser();

        $result = $parser->parse(<<<'CSV'
foo,bar,baz
"1","2","3"
"",,""
CSV
        );

        self::assertTrue($result->ok());
        self::assertEquals([
            ['foo' => '1', 'bar' => '2', 'baz' => '3'],
            ['foo' => '', 'bar' => '', 'baz' => ''],
        ], $result->data());
    }

    /** @test */
    function parsing_comma_separated_values_with_escaped_quotes()
    {
        $parser = CSV::parser();

        $result = $parser->parse(<<<'CSV'
"foo","bar","baz"
"1","2","3"
"4","5","6"""
CSV
        );

        self::assertTrue($result->ok());
        self::assertEquals([
            ['foo' => '1', 'bar' => '2', 'baz' => '3'],
            ['foo' => '4', 'bar' => '5', 'baz' => '6"'],
        ], $result->data());
    }

    /** @test */
    function refusing_comma_separated_values_with_invalid_quotes()
    {
        $parser = CSV::parser();

        $result = $parser->parse(<<<'CSV'
"foo","bar","baz"
"1","2","3"
"4","5","6""
CSV
        );

        self::assertFalse($result->ok());
        self::assertEquals('unexpected "', $result->data());
    }

    /** @test */
    function parsing_semicolon_separated_values()
    {
        $parser = CSV::parser(';');

        $result = $parser->parse(<<<'CSV'
foo;bar;baz
1;2;3
4;5;6
CSV
        );

        self::assertTrue($result->ok());
        self::assertEquals([
            ['foo' => '1', 'bar' => '2', 'baz' => '3'],
            ['foo' => '4', 'bar' => '5', 'baz' => '6'],
        ], $result->data());
    }

    /** @test */
    function parsing_comma_separated_values_with_backslash_escaped_quotes()
    {
        $parser = CSV::parser(',', '\\');

        $result = $parser->parse(<<<'CSV'
"foo","bar","baz"
"1","2","3"
"4","5","6\""
CSV
        );

        self::assertTrue($result->ok());
        self::assertEquals([
            ['foo' => '1', 'bar' => '2', 'baz' => '3'],
            ['foo' => '4', 'bar' => '5', 'baz' => '6"'],
        ], $result->data());
    }
}
