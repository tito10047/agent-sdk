<?php

declare(strict_types=1);

namespace Leoness\AgentSdk;

/**
 * One file a finished run handed over, as an agent is allowed to see it.
 *
 * Metadata only, on purpose: what an agent may do with a file after its run is decide
 * whether to say something about it. Reading contents back goes through a tool during
 * the run, where it is whitelisted and recorded.
 */
final readonly class RunFileView
{
    public function __construct(
        public int $id,
        public string $name,
        public string $mimeType,
        public int $size,
    ) {
    }
}
