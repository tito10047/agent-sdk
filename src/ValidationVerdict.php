<?php

declare(strict_types=1);

namespace Leoness\AgentSdk;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * What the platform does with an answer it has just checked.
 *
 * The three cases are ordered by severity, and that order is the whole point: a run
 * collects objections from several validators and has to end up with exactly one
 * decision, which is the worst of them ({@see self::worseOf()}).
 *
 * The line between *Escalate* and *Fail* is "would a better model fix this". A missing
 * property or an invented technical parameter might well come out right on the next
 * rung of the ladder. A validation rule that does not even compile will not — paying a
 * stronger model to fail identically is how a cost dashboard starts telling stories.
 */
enum ValidationVerdict: string implements TranslatableInterface
{
    /** The answer is usable. */
    case Pass = 'pass';

    /** The answer is unusable, but another model may do better. */
    case Escalate = 'escalate';

    /** The answer is unusable and retrying cannot help. */
    case Fail = 'fail';

    /**
     * The more severe of the two verdicts.
     */
    public function worseOf(self $other): self
    {
        return $other->severity() > $this->severity() ? $other : $this;
    }

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return match ($this) {
            self::Pass => 'Prijaté',
            self::Escalate => 'Na eskaláciu',
            self::Fail => 'Zamietnuté',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Pass => 'success',
            self::Escalate => 'warning',
            self::Fail => 'danger',
        };
    }

    private function severity(): int
    {
        return match ($this) {
            self::Pass => 0,
            self::Escalate => 1,
            self::Fail => 2,
        };
    }
}
