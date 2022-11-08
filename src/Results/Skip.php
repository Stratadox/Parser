<?php declare(strict_types=1);

namespace Stratadox\Parser\Results;

use Stratadox\Parser\Result;

/**
 * Skip Result
 *
 * Signifies the result should not be used.
 */
final class Skip implements Result
{
    public function __construct(private string $unparsed) {}

    public static function upTo(string $unparsed): Result
    {
        return new self($unparsed);
    }

    public function data(): mixed
    {
        return null;
    }

    public function unparsed(): string
    {
        return $this->unparsed;
    }

    public function ok(): bool
    {
        return true;
    }

    public function use(): bool
    {
        return false;
    }
}
