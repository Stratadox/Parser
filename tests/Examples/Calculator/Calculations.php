<?php declare(strict_types=1);

namespace Stratadox\Parser\Test\Examples\Calculator;

use Stratadox\Parser\Containers\Grammar;
use Stratadox\Parser\Containers\Lazy;
use Stratadox\Parser\Parser;
use function Stratadox\Parser\pattern;
use function Stratadox\Parser\text;

final class Calculations
{
    public static function parser(): Parser
    {
        $grammar = Grammar::with($lazy = Lazy::container());

        $sign = text('+')->or('-')->maybe();
        $digits = pattern('\d+');
        $map = fn($op, $l, $r) => [
            'op' => $op,
            'arg' => [$l, $r],
        ];

        $grammar['prio 0'] = $sign->andThen($digits, '.', $digits)->join()->map(fn($x) => (float) $x)
            ->or($sign->andThen($digits)->join()->map(fn($x) => (int) $x))
            ->between(text(' ')->or("\t", "\n", "\r")->repeatable()->optional());

        $lazy['prio 1'] = $grammar['prio 0']->andThen('^', $grammar['prio 0'])->map(fn($a) => [
            'op' => '^',
            'arg' => [$a[0], $a[2]],
        ])->or($grammar['prio 0']);

        $grammar['prio 2'] = $grammar['prio 1']->keepSplit(['*', '/'], $map)->or($grammar['prio 1']);

        $grammar['prio 3'] = $grammar['prio 2']->keepSplit(['+', '-'], $map)->or($grammar['prio 2']);

        return $grammar['prio 3']->end();
    }
}
