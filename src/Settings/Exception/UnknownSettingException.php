<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Settings\Exception;

/**
 * A package asked for a setting its `leoness-agent.json` does not declare — a programming
 * error, caught by the package's own tests, never a state a user can put it in.
 */
final class UnknownSettingException extends \LogicException
{
    public static function of(string $package, string $key): self
    {
        return new self(\sprintf('Balík %s nemá deklarované nastavenie „%s“.', $package, $key));
    }
}
