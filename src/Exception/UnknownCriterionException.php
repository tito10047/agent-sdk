<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Exception;

/**
 * A data source was asked to filter by something it does not know.
 *
 * Thrown rather than ignored, because ignoring a criterion widens the result: an agent
 * that asked for one supplier's products and silently got the whole catalogue is the
 * exact failure the whitelists exist to prevent, and it looks like success.
 *
 * It lives in the SDK because it is the *implementer's* to raise — a data source is
 * written by whoever owns the data, and the platform only ever passes the message on.
 */
class UnknownCriterionException extends \InvalidArgumentException
{
    /**
     * @param list<string> $supported
     */
    public static function forSource(string $source, string $criterion, array $supported): self
    {
        return new self(\sprintf(
            'Dátový zdroj "%s" nepozná kritérium "%s". Podporuje: %s.',
            $source,
            $criterion,
            [] === $supported ? 'žiadne' : implode(', ', $supported),
        ));
    }
}
