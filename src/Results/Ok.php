<?php declare(strict_types=1);

namespace Stratadox\Parser\Results;

use Stratadox\Parser\Result;

/**
 * Ok Result
 *
 * Successful parsing result.
 */
final class Ok implements Result
{
    public function __construct(
        private mixed $data,
        private string $unparsed,
    ) {}

    public static function with(mixed $data, string $unparsed): Result
    {
        return new self($data, $unparsed);
    }

    public function data(): mixed
    {
        return $this->data;
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
        return true;
    }
}
