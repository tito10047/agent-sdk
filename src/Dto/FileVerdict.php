<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Dto;

/**
 * What an agent says about the file its run just produced, before anybody is offered it.
 *
 * Three outcomes, not two, and the third is the one that keeps the other two honest:
 *
 * - **accepted** — checked and correct; the file is the run's result.
 * - **rejected** — checked and wrong; the file is kept as evidence and *not* handed over,
 *   and the differences go back to the model so it can write it again.
 * - **unverified** — the check could not be made at all (an order that arrived as a scan,
 *   say). The file is handed over, because refusing it would help nobody, but nothing
 *   claims it was verified — a silent "fine" would be worse than no check.
 */
final readonly class FileVerdict
{
    /**
     * @param list<string> $differences concrete, one per line, for the model to act on
     */
    private function __construct(
        public bool $accepted,
        public array $differences = [],
        public ?string $unverifiedReason = null,
    ) {
    }

    public static function accepted(): self
    {
        return new self(true);
    }

    /**
     * @param list<string> $differences
     */
    public static function rejected(array $differences): self
    {
        return new self(false, $differences);
    }

    public static function unverified(string $reason): self
    {
        return new self(true, [], $reason);
    }

    public function isVerified(): bool
    {
        return $this->accepted && null === $this->unverifiedReason;
    }
}
