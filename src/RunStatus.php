<?php

declare(strict_types=1);

namespace Leoness\AgentSdk;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Where one agent run stands.
 *
 * A run is created as {@see self::Pending} and only then dispatched, which is what
 * lets the queue handler be idempotent: a message whose run is no longer pending has
 * already been dealt with and may be dropped without producing a second result.
 */
enum RunStatus: string implements TranslatableInterface
{
    case Pending = 'pending';
    case Running = 'running';
    case Succeeded = 'succeeded';
    case Failed = 'failed';

    /**
     * Never started and never will: its batch was called off. Kept apart from
     * {@see self::Failed} because nothing was attempted and nothing was paid for — a
     * cancelled batch must not read as five hundred errors.
     */
    case Cancelled = 'cancelled';

    /**
     * Terminal states never change again; the run log for them is complete.
     */
    public function isFinished(): bool
    {
        return match ($this) {
            self::Succeeded, self::Failed, self::Cancelled => true,
            self::Pending, self::Running => false,
        };
    }

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return match ($this) {
            self::Pending => 'Čaká',
            self::Running => 'Prebieha',
            self::Succeeded => 'Hotovo',
            self::Failed => 'Chyba',
            self::Cancelled => 'Zrušený',
        };
    }

    /**
     * Bootstrap contextual colour used by the run browser badges.
     */
    public function badge(): string
    {
        return match ($this) {
            self::Pending => 'secondary',
            self::Running => 'info',
            self::Succeeded => 'success',
            self::Failed => 'danger',
            self::Cancelled => 'secondary',
        };
    }
}
