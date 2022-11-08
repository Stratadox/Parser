<?php declare(strict_types=1);

namespace Stratadox\Parser\Test\Examples\JSON;

use PHPUnit\Framework\TestCase;
use Stratadox\Parser\Parser;
use Stratadox\Parser\Result;

final class Parsing_JSON extends TestCase
{
    private Parser $json;

    protected function setUp(): void
    {
        $this->json = JSON::parser();
    }

    /** @test */
    function parsing_json_null()
    {
        self::assertSameData(
            null,
            $this->json->parse('null'),
        );
    }

    /** @test */
    function parsing_json_boolean_true()
    {
        self::assertSameData(
            true,
            $this->json->parse('true'),
        );
    }

    /** @test */
    function parsing_json_boolean_false()
    {
        self::assertSameData(
            false,
            $this->json->parse('false'),
        );
    }

    /** @test */
    function parsing_json_number()
    {
        self::assertSameData(
            3,
            $this->json->parse('3'),
        );
    }

    /** @test */
    function parsing_json_number_with_spaces()
    {
        self::assertSameData(
            3,
            $this->json->parse('  3  '),
        );
    }

    /** @test */
    function parsing_json_number_with_tab_and_newline()
    {
        self::assertSameData(
            3,
            $this->json->parse("\t 3\n"),
        );
    }

    /** @test */
    function parsing_json_float()
    {
        self::assertSameData(
            3.5,
            $this->json->parse('3.5'),
        );
    }

    /** @test */
    function parsing_json_math_float()
    {
        self::assertSameData(
            350000.0,
            $this->json->parse('3.5e5'),
        );
    }

    /** @test */
    function parsing_json_string()
    {
        self::assertSameData(
            'Hello, world',
            $this->json->parse('"Hello, world"'),
        );
    }

    /** @test */
    function parsing_escaped_json_string()
    {
        self::assertSameData(
            'Hello, "world"',
            $this->json->parse('"Hello, \"world\""'),
        );
    }

    /** @test */
    function parsing_json_object()
    {
        self::assertSameData(
            ['foo' => 'bar'],
            $this->json->parse('{"foo": "bar"}'),
        );
    }

    /** @test */
    function parsing_empty_json_object()
    {
        self::assertSameData(
            [],
            $this->json->parse('{ }'),
        );
    }

    /** @test */
    function parsing_nested_json_object()
    {
        self::assertSameData(
            ['foo' => ['bar' => 'baz', 'x' => 123]],
            $this->json->parse('{"foo": {"bar": "baz", "x": 123}}'),
        );
    }

    /** @test */
    function parsing_json_array()
    {
        self::assertSameData(
            ['foo', 'bar'],
            $this->json->parse('["foo", "bar"]'),
        );
    }

    /** @test */
    function parsing_empty_json_array()
    {
        self::assertSameData(
            [],
            $this->json->parse('[ ]'),
        );
    }

    /** @test */
    function parsing_nested_json_array()
    {
        self::assertSameData(
            ['foo', ['bar', 'baz']],
            $this->json->parse('["foo", ["bar", "baz"]]'),
        );
    }

    /** @test */
    function parsing_json_array_of_objects()
    {
        self::assertSameData(
            [['name' => 'foo'], ['name' => 'bar']],
            $this->json->parse('[{"name":"foo"},{"name":"bar"}]'),
        );
    }

    /** @test */
    function parsing_json_array_of_nulls()
    {
        self::assertSameData(
            [null, null, null],
            $this->json->parse('[null, null, null]'),
        );
    }

    private static function assertSameData(mixed $expectedData, Result $actual): void
    {
        self::assertEmpty($actual->unparsed(), $actual->unparsed());
        self::assertSame($expectedData, $actual->data());
    }
}
