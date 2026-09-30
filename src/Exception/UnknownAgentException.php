<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Exception;

/**
 * No agent of that code exists, or it has been switched off.
 *
 * A package normally meets this the boring way: an operator deactivated the agent the
 * package was written around, and the next attempt to use it has to say so rather than
 * behave as though nothing were configured.
 *
 * The platform's own exception extends this one, so a `catch` written against the
 * contract catches the real thing.
 *
 * @phpstan-consistent-constructor Subclasses inherit the exception constructor, which is what
 *   lets the factories here build the platform's own subclass.
 */
class UnknownAgentException extends \RuntimeException
{
    public static function forCode(string $code): static
    {
        return new static(\sprintf('Agent "%s" neexistuje alebo nie je aktívny.', $code));
    }
}
