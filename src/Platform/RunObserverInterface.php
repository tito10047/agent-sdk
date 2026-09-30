<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Platform;

use Leoness\AgentSdk\Platform\Entity\AgentRunInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * Lets a package find out that a run of its agent has begun or ended.
 *
 * A batch item is executed by the platform's queue worker, where a package has no place
 * to stand: nobody calls it, and it cannot listen to the core's events because it is not
 * allowed to know their classes. This is the seam that closes that gap — the platform
 * dispatches its own events as it always did, and one bridge inside the core turns them
 * into calls on every observer a package has registered.
 *
 * **Nothing here should throw for a domain reason.** The run is already written by the
 * time either method is called, so an exception cannot undo it; it would only travel up
 * into a Messenger retry that then finds the run in a terminal state and drops the
 * message — the reaction lost and nobody told. The bridge therefore catches and logs
 * whatever escapes, so one package's bad afternoon is not another's. That makes the log
 * the platform's last word on the matter and not an answer a user will see: an observer
 * that cannot do its job is expected to record the failure where *its own* users look.
 *
 * Both methods fire for **every run on the platform**, not only for the observer's own
 * agents, and the first thing an implementation does is decide whether the run is any of
 * its business ({@see AgentRunInterface::agentCode()}). Keep that decision to a comparison
 * and no query.
 */
#[AutoconfigureTag(RunObserverInterface::TAG)]
interface RunObserverInterface
{
    /** The container tag the platform collects run observers by. */
    public const string TAG = 'app.run_observer';

    /**
     * A worker has claimed the run and is about to spend money on it.
     *
     * Called exactly once per execution, after the run has been moved to *running* and
     * before anything is paid for, so the status may be relied on as final.
     */
    public function onRunStarted(AgentRunInterface $run): void;

    /**
     * The run has reached a terminal state — succeeded, failed, or cancelled before it
     * began.
     *
     * Not called when infrastructure gave way mid-run: the run is then still *running*
     * and nothing has concluded that an observer could truthfully react to.
     */
    public function onRunFinished(AgentRunInterface $run): void;
}
