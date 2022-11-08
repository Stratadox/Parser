<?php declare(strict_types=1);

namespace Stratadox\Parser\Test\T6_Containers;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Stratadox\Parser\Containers\Eager;
use Stratadox\Parser\Parsers\Text;

final class Registering_eager_parsers extends TestCase
{
    /** @test */
    function storing_and_fetching_a_parser()
    {
        $eager = Eager::container();

        $eager['foo'] = Text::is('foo');

        self::assertInstanceOf(Text::class, $eager['foo']);
    }

    /** @test */
    function not_fetching_a_parser_before_storing_it()
    {
        $eager = Eager::container();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('foo-bar');

        $eager['foo-bar'];
    }

    /** @test */
    function not_fetching_a_parser_after_unsetting_it()
    {
        $eager = Eager::container();
        $eager['foo'] = Text::is('foo');
        unset($eager['foo']);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('foo-bar');

        $eager['foo-bar'];
    }
}
