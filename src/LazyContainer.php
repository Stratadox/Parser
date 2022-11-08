<?php declare(strict_types=1);

namespace Stratadox\Parser;

use Closure;

/**
 * Lazy Parser Container
 *
 * Special container to manage lazy loading.
 */
interface LazyContainer extends Container
{
    public function factory(string $name): Closure;
}
