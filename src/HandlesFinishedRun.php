<?php

declare(strict_types=1);

namespace Leoness\AgentSdk;

/**
 * A code agent that has something to do once its own run has concluded.
 *
 * This is where the logic of an agent goes: write the answer somewhere, raise an alert,
 * start a follow-up run of another agent. It is deliberately *after* the run rather than
 * inside it — the run is the audit record, it is complete and paid for by the time this is
 * called, and nothing here may change what it says.
 *
 * Optional on purpose. An agent whose whole job is to answer a prompt has nothing to add,
 * and forcing it to carry an empty method would make the interface lie about what agents do.
 *
 * **Nothing implemented here may throw for a domain reason.** The run is already recorded, so
 * an exception would not undo it — it would travel up through the queue handler into a
 * retry, which then finds the run in a terminal state and drops the message. The platform
 * catches and logs what escapes, but an agent that treats its own foreseeable failures as
 * exceptions is an agent whose failures nobody will see.
 */
interface HandlesFinishedRun
{
    /**
     * @param FinishedRunInterface $run always a run of this agent, in a terminal state —
     *                                  succeeded, failed or cancelled. Which one is
     *                                  {@see FinishedRunInterface::status()}, and an
     *                                  implementation is expected to look. So is
     *                                  {@see FinishedRunInterface::trigger()}: a rehearsal
     *                                  from the builder is a real recorded run, and an agent
     *                                  that writes its answer somewhere has to decide whether
     *                                  a rehearsal may do that too.
     */
    public function handleFinishedRun(FinishedRunInterface $run): void;
}
