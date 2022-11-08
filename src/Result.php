<?php declare(strict_types=1);

namespace Stratadox\Parser;
/**
 * Result
 *
 * Each parser returns a Result when parsing.
 */
interface Result
{
    public function data(): mixed;

    public function unparsed(): string;

    public function ok(): bool;

    public function use(): bool;
}
