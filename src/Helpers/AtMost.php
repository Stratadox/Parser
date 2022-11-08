<?php declare(strict_types=1);

namespace Stratadox\Parser\Helpers;

use Stratadox\Parser\Parser;
use Stratadox\Parser\Parsers\FullyMap;
use Stratadox\Parser\Result;
use Stratadox\Parser\Results\Error;
use function array_slice;
use function count;
use function implode;

/**
 * AtMost
 *
 * Refuses array results with more than x entries.
 */
final class AtMost
{
    public static function results(int $n, Parser $parser): Parser
    {
        return FullyMap::the($parser, fn(Result $result) => count($result->data()) > $n ?
            Error::in(implode(array_slice($result->data(), $n)) . $result->unparsed()) :
            $result
        );
    }
}
