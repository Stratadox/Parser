<?php declare(strict_types=1);

namespace Stratadox\Parser\Parsers;

use Stratadox\Parser\Parser;
use Stratadox\Parser\Result;
use Stratadox\Parser\Results\Error;

/**
 * Lazy-Limit
 *
 * Safety harness to prevent left-recursion from eating infinite resources.
 */
final class Limit extends Parser
{
    private array $locked = [];

    public function __construct(private Parser $parser) {}

    public function parse(string $input): Result
    {
        if (isset($this->locked[$input])) {
            return Error::in($input);
        }

        $this->locked[$input] = true;
        $result = $this->parser->parse($input);
        unset($this->locked[$input]);

        return $result;
    }
}
