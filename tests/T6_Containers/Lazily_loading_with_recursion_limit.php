<?php declare(strict_types=1);

namespace Stratadox\Parser\Test\T6_Containers;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Stratadox\Parser\Containers\Lazy;
use Stratadox\Parser\Containers\Limited;
use Stratadox\Parser\Parsers\Text;

final class Lazily_loading_with_recursion_limit extends TestCase
{
    /** @test */
    function parsing_despite_left_recursion()
    {
        $lazy = Limited::recursion(Lazy::container());
        $lazy['expression'] = $lazy['expression']->andThen('a')->or('z');
        $parser = $lazy['expression'];

        $result = $parser->parse('z');

        self::assertTrue($result->ok());
        self::assertEquals('z', $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function parsing_a_sequence_despite_left_recursion()
    {
        $lazy = Limited::recursion(Lazy::container());
        $lazy['expression'] = $lazy['expression']->andThen('a')->or('z');
        $parser = $lazy['expression']->split(',');

        $result = $parser->parse('z,z,z');

        self::assertTrue($result->ok());
        self::assertEquals(['z', 'z', 'z'], $result->data());
        self::assertEquals('', $result->unparsed());
    }

    /** @test */
    function throwing_an_exception_when_using_an_undefined_lazy_parser()
    {
        $lazy = Limited::recursion(Lazy::container());
        $parser = $lazy['foo-bar'];

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('foo-bar');

        $parser->parse('(z)');
    }

    /** @test */
    function throwing_an_exception_when_using_an_unset_lazy_parser()
    {
        $lazy = Limited::recursion(Lazy::container());
        $lazy['foo-bar'] = Text::is('foo-bar');
        $parser = $lazy['foo-bar'];
        unset($lazy['foo-bar']);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('foo-bar');

        $parser->parse('(z)');
    }
}
