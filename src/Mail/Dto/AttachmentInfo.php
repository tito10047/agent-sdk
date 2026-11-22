<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Mail\Dto;

/**
 * An attachment as the message declares it. The content is never part of it: a package
 * judges the name and type, reading is the platform's tool and its allowlist.
 */
final readonly class AttachmentInfo
{
    public function __construct(
        public string $name,
        public string $mimeType,
        public int $size,
    ) {
    }
}
