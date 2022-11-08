<?php declare(strict_types=1);

namespace Stratadox\Parser\Helpers;

use Stratadox\Parser\Parser;
use Stratadox\Parser\Parsers\FullyMap;
use Stratadox\Parser\Result;
use Stratadox\Parser\Results\Error;

/**
 * NonEmpty
 *
 * Refuses `empty` results.
 */
final class NonEmpty
{
    public static function result(Parser $parser): Parser
    {
        return FullyMap::the($parser, fn(Result $result) => empty($result->data()) ?
            Error::in($result->unparsed()) :
            $result
        );
    }
}
