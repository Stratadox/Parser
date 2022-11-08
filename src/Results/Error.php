<?php declare(strict_types=1);

namespace Stratadox\Parser\Results;

use Stratadox\Parser\Result;
use function mb_substr;

/**
 * Error Result
 *
 * Produced when a parser refuses the input.
 */
final class Error implements Result
{
    public function __construct(private string $unparsed) {}

    public static function in(string $input): Result
    {
        return new self($input);
    }

    public function data(): string
    {
        return 'unexpected ' . (empty($this->unparsed) ? 'end' : mb_substr($this->unparsed, 0, 1));
    }

    public function unparsed(): string
    {
        return $this->unparsed;
    }

    public function ok(): bool
    {
        return false;
    }

    public function use(): bool
    {
        return false;
    }
}
