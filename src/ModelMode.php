<?php

declare(strict_types=1);

namespace Leoness\AgentSdk;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * How an agent version picks the model it runs on.
 *
 * The mode is stored on the version, but resolved by the router: an agent never
 * names a provider, only a catalogue entry or nothing at all.
 */
enum ModelMode: string implements TranslatableInterface
{
    /** The administrator pinned one catalogue entry. */
    case Fixed = 'fixed';

    /** The router picks the cheapest model that can do the job. */
    case Automatic = 'automatic';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return match ($this) {
            self::Fixed => 'Fixed — pevne zvolený model',
            self::Automatic => 'Automatic — vyberá Model Router',
        };
    }
}
