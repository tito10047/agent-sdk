<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Platform;

/**
 * Lets an agent attach a warning to its own finished run.
 *
 * The one write an agent may make against the run record, and it is additive: a
 * warning changes what a person reads next to the answer, never what the run says
 * happened. Persisted immediately — by the time `handleFinishedRun()` fires the run
 * has already been flushed, and a warning nobody stored is a warning nobody reads.
 */
interface RunAnnotatorInterface
{
    public function warn(int $runId, string $warning): void;
}
