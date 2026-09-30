<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Exception;

/**
 * A batch was asked for with nothing in it.
 *
 * Refused rather than created, because an empty batch would immediately report itself as
 * finished and successful — a green row in the monitor for work that never existed, which
 * is exactly the kind of thing an import bug hides behind. A package that legitimately has
 * nothing to send checks before it asks.
 *
 * The platform's own exception extends this one.
 *
 * @phpstan-consistent-constructor Subclasses inherit the exception constructor, which is what
 *   lets the factories here build the platform's own subclass.
 */
class EmptyBatchException extends \InvalidArgumentException
{
    public static function forAgent(string $agentCode): static
    {
        return new static(\sprintf('Dávka pre agenta "%s" neobsahuje ani jednu položku.', $agentCode));
    }
}
