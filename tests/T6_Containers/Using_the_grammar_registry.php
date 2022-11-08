<?php declare(strict_types=1);

namespace Stratadox\Parser\Test\T6_Containers;

use PHPUnit\Framework\TestCase;
use Stratadox\Parser\Containers\Grammar;
use Stratadox\Parser\Containers\Lazy;
use Stratadox\Parser\Parsers\Text;

final class Using_the_grammar_registry extends TestCase
{
    /** @test */
    function storing_and_fetching_an_eager_parser()
    {
        $grammar = Grammar::container();

        $grammar['a'] = Text::is('a');

        $result = $grammar['a']->parse('a');

        self::assertTrue($result->ok());
        self::assertEquals('a', $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function storing_and_fetching_a_lazy_parser()
    {
        $grammar = Grammar::container();

        $grammar->lazy('a', $grammar['a']->between('(', ')')->or('a'));

        $result = $grammar['a']->parse('((a))');

        self::assertTrue($result->ok());
        self::assertEquals('a', $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function storing_and_fetching_a_lazy_parser_using_array_syntax()
    {
        $grammar = Grammar::with($lazy = Lazy::container());

        $lazy['a'] = $grammar['a']->between('(', ')')->or('a');

        $result = $grammar['a']->parse('((a))');

        self::assertTrue($result->ok());
        self::assertEquals('a', $result->data());
        self::assertEquals('', $result->unparsed());
    }
}
