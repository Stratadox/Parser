<?php declare(strict_types=1);

namespace Stratadox\Parser\Test\Examples\JSON;

use Stratadox\Parser\Containers\Grammar;
use Stratadox\Parser\Containers\Lazy;
use Stratadox\Parser\Helpers\Between;
use Stratadox\Parser\Parser;
use Stratadox\Parser\Result;
use Stratadox\Parser\Results\Ok;
use function array_column;
use function array_combine;
use function Stratadox\Parser\text;
use function Stratadox\Parser\pattern;

final class JSON
{
    public static function parser(): Parser
    {
        $grammar = Grammar::with($lazy = Lazy::container());

        $grammar['null'] = text('null')->map(fn() => null);

        $grammar['bool'] = text('true')->or('false')->map(fn($x) => $x === 'true');

        $sign = text('+')->or('-')->maybe();
        $digits = pattern('\d+');
        $integer = $sign->andThen($digits)->join();
        $float = $sign->andThen($digits, '.', $digits)->join();
        $mathFloat = $float->or($integer)->andThen(text('e')->or('E'))->andThen($float->or($integer))->join();
        $grammar['number'] = $mathFloat->or($float)->map(fn($x) => (float) $x)
            ->or($integer->map(fn($x) => (int) $x));

        $grammar['string'] = Between::escaped('"', '"', '\\');

        $padding = text(' ')->or("\t", "\n", "\r")->repeatable()->optional();
        $empty = $padding->fullMap(fn(Result $r) => Ok::with([], $r->unparsed()));
        $keyValue = $padding->andThen($grammar['string'], $padding, ':', $grammar['value'])
            ->map(fn($x) => ['k' => $x[0], 'v' => $x[2]]);

        $grammar['object'] = $keyValue->split(',')
            ->map(fn($x) => array_combine(array_column($x, 'k'), array_column($x, 'v')))
            ->or($empty)->between('{', '}');

        $grammar['array'] = $grammar['value']->split(',')->or($empty)->between('[', ']');

        $lazy['value'] = $padding->andThen($grammar['null']->or(
            $grammar['bool'],
            $grammar['number'],
            $grammar['string'],
            $grammar['object'],
            $grammar['array'],
        ))->andThen($padding)->map(fn($x) => $x[0] ?? null);

        return $grammar['value'];
    }
}
