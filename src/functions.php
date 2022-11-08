<?php declare(strict_types=1);

namespace Stratadox\Parser;

use Stratadox\Parser\Parsers\Any;
use Stratadox\Parser\Parsers\Pattern;
use Stratadox\Parser\Parsers\Text;

function any(): Parser
{
    return Any::symbol();
}

function pattern(string $search, string $modifier = ''): Parser
{
    return Pattern::match($search, $modifier);
}

function text(string $search): Parser
{
    return Text::is($search);
}
