<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Mail\Dto;

/**
 * The result of one sender check (SPF, DKIM or DMARC) as our receiving server recorded it
 * in `Authentication-Results`.
 *
 * `None` and `Unknown` are not the same: `None` is the server saying the sender publishes
 * no policy, `Unknown` is the platform saying it found no verdict it can trust — a header
 * written by somebody else's server is not evidence.
 */
enum AuthVerdict: string
{
    case Pass = 'pass';
    case Fail = 'fail';
    case SoftFail = 'softfail';
    case Neutral = 'neutral';
    case None = 'none';
    case TempError = 'temperror';
    case PermError = 'permerror';
    case Unknown = 'unknown';

    public function failed(): bool
    {
        return self::Fail === $this || self::PermError === $this;
    }
}
