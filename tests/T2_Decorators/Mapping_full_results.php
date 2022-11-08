<?php declare(strict_types=1);

namespace Stratadox\Parser\Test\T2_Decorators;

use PHPUnit\Framework\TestCase;
use Stratadox\Parser\Parsers\FullyMap;
use Stratadox\Parser\Parsers\Text;
use Stratadox\Parser\Result;
use Stratadox\Parser\Results\Error;
use Stratadox\Parser\Results\Ok;

final class Mapping_full_results extends TestCase
{
    /** @test */
    function replacing_an_error_result_with_an_ok()
    {
        $parser = FullyMap::the(Text::is('foo'), fn(Result $r) => Ok::with('bar', $r->unparsed()));

        $result = $parser->parse('baz');

        self::assertTrue($result->ok());
        self::assertSame('bar', $result->data());
        self::assertSame('baz', $result->unparsed());
    }

    /** @test */
    function replacing_the_error_with_a_custom_one()
    {
        $parser = FullyMap::the('foo', fn(Result $r) => $r->ok() ? $r : Error::in('baz'));

        $result = $parser->parse('bar');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected b', $result->data());
        self::assertEquals('baz', $result->unparsed());
    }

    /** @test */
    function replacing_an_error_result_with_an_ok_through_method()
    {
        $parser = Text::is('foo')->fullMap(fn(Result $r) => Ok::with('bar', $r->unparsed()));

        $result = $parser->parse('baz');

        self::assertTrue($result->ok());
        self::assertSame('bar', $result->data());
        self::assertSame('baz', $result->unparsed());
    }

    /** @test */
    function replacing_the_error_with_a_custom_one_through_method()
    {
        $parser = Text::is('foo')->fullMap(fn(Result $r) => $r->ok() ? $r : Error::in('woo'));

        $result = $parser->parse('bar');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected w', $result->data());
        self::assertEquals('woo', $result->unparsed());
    }
}
