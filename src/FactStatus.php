<?php

declare(strict_types=1);

namespace Leoness\AgentSdk;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * How much a single piece of data is worth believing — the three states section 11 of
 * the brief demands the system tell apart.
 *
 * The values are the literal strings a model writes into its answer, so they are part
 * of the public contract of an agent output and cannot be renamed without a new agent
 * version. They are Slovak because the prompt that asks for them is Slovak: a model
 * asked in Slovak for "stav" answers "nepotvrdeny" far more reliably than "unconfirmed".
 *
 * @see Dto\FactClaim for the shape these appear in
 */
enum FactStatus: string implements TranslatableInterface
{
    /** Backed by a source: supplier data, a data sheet, the input of the run. */
    case Confirmed = 'potvrdeny';

    /** Written by the model and plausible, but nobody has verified it. */
    case Suggested = 'navrh_ai';

    /** Not known. The only honest answer when there is no source — never a guess. */
    case Unknown = 'nepotvrdeny';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $status): string => $status->value, self::cases());
    }

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return match ($this) {
            self::Confirmed => 'Potvrdený údaj',
            self::Suggested => 'Návrh AI',
            self::Unknown => 'Nepotvrdený údaj',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Confirmed => 'success',
            self::Suggested => 'warning',
            self::Unknown => 'secondary',
        };
    }
}
