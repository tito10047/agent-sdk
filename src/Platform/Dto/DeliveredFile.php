<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Platform\Dto;

/**
 * A file the platform stored for a tool: its ID, the name it downloads as, and the signed
 * link with the moment it stops working.
 */
final readonly class DeliveredFile
{
    public function __construct(
        public int $id,
        public string $name,
        public string $url,
        public \DateTimeImmutable $expiresAt,
    ) {
    }
}
