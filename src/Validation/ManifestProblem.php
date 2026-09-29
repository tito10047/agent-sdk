<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Validation;

/**
 * One reason a manifest cannot be installed as it stands.
 *
 * The path names what is wrong in the author's vocabulary — a schema path, a rule index,
 * a prompt variable — and the reason is a full sentence. Both are for a human: a CI log
 * of a failing agent repository is where these end up.
 */
final readonly class ManifestProblem
{
    public function __construct(
        public string $path,
        public string $reason,
    ) {
    }

    public function __toString(): string
    {
        return \sprintf('%s: %s', $this->path, $this->reason);
    }
}
