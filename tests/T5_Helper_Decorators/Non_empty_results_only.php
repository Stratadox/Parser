<?php declare(strict_types=1);

namespace Stratadox\Parser\Test\T5_Helper_Decorators;

use PHPUnit\Framework\TestCase;
use Stratadox\Parser\Helpers\NonEmpty;
use Stratadox\Parser\Parsers\Repeatable;
use Stratadox\Parser\Parsers\Text;

final class Non_empty_results_only extends TestCase
{
    /** @test */
    function transforming_an_empty_text_into_an_error_response()
    {
        $parser = NonEmpty::result(Text::is(''));

        $result = $parser->parse('x');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected x', $result->data());
        self::assertEquals('x', $result->unparsed());
    }

    /** @test */
    function not_transforming_a_non_empty_text_result()
    {
        $parser = NonEmpty::result(Text::is('a'));

        $result = $parser->parse('a');

        self::assertTrue($result->ok());
        self::assertEquals('a', $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function transforming_an_empty_list_into_an_error_response()
    {
        $parser = NonEmpty::result(Repeatable::parser('a'));

        $result = $parser->parse('x');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected x', $result->data());
        self::assertEquals('x', $result->unparsed());
    }

    /** @test */
    function not_transforming_a_non_empty_list_result()
    {
        $parser = NonEmpty::result(Repeatable::parser('a'));

        $result = $parser->parse('a');

        self::assertTrue($result->ok());
        self::assertEquals(['a'], $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function transforming_an_empty_text_into_an_error_response_through_method()
    {
        $parser = Text::is('')->nonEmpty();

        $result = $parser->parse('x');

        self::assertFalse($result->ok());
        self::assertEquals('unexpected x', $result->data());
        self::assertEquals('x', $result->unparsed());
    }

    /** @test */
    function not_transforming_a_non_empty_text_result_through_method()
    {
        $parser = Text::is('a')->nonEmpty();

        $result = $parser->parse('a');

        self::assertTrue($result->ok());
        self::assertEquals('a', $result->data());
        self::assertEquals('', $result->unparsed());
    }
}
