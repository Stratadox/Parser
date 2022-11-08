<?php declare(strict_types=1);

namespace Stratadox\Parser\Helpers;

use Stratadox\Parser\Parser;
use Stratadox\Parser\Parsers\FullyMap;
use Stratadox\Parser\Result;
use Stratadox\Parser\Results\Error;
use function count;

/**
 * AtLeast
 *
 * Refuses array results with fewer than x entries.
 */
final class AtLeast
{
    public static function results(int $n, Parser $parser): Parser
    {
        return FullyMap::the($parser, fn(Result $result) => count($result->data()) < $n ?
            Error::in($result->unparsed()) :
            $result
        );
    }
}
