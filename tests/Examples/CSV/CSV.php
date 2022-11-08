<?php declare(strict_types=1);

namespace Stratadox\Parser\Test\Examples\CSV;

use Stratadox\Parser\Helpers\Between;
use Stratadox\Parser\Parser;
use function array_combine;
use function array_filter;
use function array_map;
use function array_slice;
use function Stratadox\Parser\any;
use function Stratadox\Parser\pattern;

final class CSV
{
    public static function parser(
        Parser|string $separator = ',',
        Parser|string $escape = '"',
    ): Parser {
        $newline = pattern('\r\n|\r|\n');
        return Between::escaped('"', '"', $escape)
            ->or(any()->except($newline->or($separator, $escape))->repeatableString())
            ->mustSplit($separator)->maybe()
            ->split($newline)
            ->end()->map(fn(array $file) => array_map(
                fn(array $line) => array_combine($file[0], $line),
                array_filter(array_slice($file, 1), fn($x) => $x),
            ));
    }
}
