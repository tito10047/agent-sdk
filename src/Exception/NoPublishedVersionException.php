<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Exception;

/**
 * The agent exists and is active, but nobody has published a version of it yet.
 *
 * Drafts are never runnable: a run has to point at a frozen snapshot, otherwise its
 * result could not be explained afterwards. Distinct from
 * {@see UnknownAgentException} because the two need different answers — one is
 * "somebody has to finish setting this up", the other "this is not the agent you think".
 *
 * The platform's own exception extends this one.
 *
 * @phpstan-consistent-constructor Subclasses inherit the exception constructor, which is what
 *   lets the factories here build the platform's own subclass.
 */
class NoPublishedVersionException extends \RuntimeException
{
    public static function forCode(string $code): static
    {
        return new static(\sprintf('Agent "%s" nemá publikovanú verziu, takže sa nedá spustiť.', $code));
    }
}
