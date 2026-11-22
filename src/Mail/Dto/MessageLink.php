<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Mail\Dto;

/**
 * One link of an HTML body: what the reader sees and where it actually leads. The two
 * differing is the oldest trick in phishing, so both are kept.
 */
final readonly class MessageLink
{
    public function __construct(
        public string $text,
        public string $href,
    ) {
    }
}
