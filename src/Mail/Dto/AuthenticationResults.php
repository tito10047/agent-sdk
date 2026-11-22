<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Mail\Dto;

/**
 * SPF, DKIM and DMARC of one message, read only from the header our own receiving server
 * added — the one a sender cannot forge by writing a header of the same name.
 */
final readonly class AuthenticationResults
{
    public function __construct(
        public AuthVerdict $spf = AuthVerdict::Unknown,
        public AuthVerdict $dkim = AuthVerdict::Unknown,
        public AuthVerdict $dmarc = AuthVerdict::Unknown,
    ) {
    }

    public function anyFailed(): bool
    {
        return $this->spf->failed() || $this->dkim->failed() || $this->dmarc->failed();
    }
}
