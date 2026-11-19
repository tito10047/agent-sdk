<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Confirmation\Dto;

/**
 * A proposal waiting for a person: its id, for asking about it later (`stav_potvrdenia`),
 * and the signed link the person opens to decide, with the moment it stops working.
 *
 * The expiry travels with the URL because the model has to say it: "odkaz platí do 14:35"
 * is the sentence that keeps a person from opening it tomorrow and blaming the platform.
 */
final readonly class IssuedConfirmation
{
    public function __construct(
        public int $id,
        public string $url,
        public \DateTimeImmutable $expiresAt,
    ) {
    }
}
