<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Platform;

use Leoness\AgentSdk\Exception\EmptyBatchException;
use Leoness\AgentSdk\Exception\InvalidAgentInputException;
use Leoness\AgentSdk\Exception\NoPublishedVersionException;
use Leoness\AgentSdk\Exception\UnknownAgentException;
use Leoness\AgentSdk\Platform\Dto\DispatchedBatch;
use Leoness\AgentSdk\Platform\Dto\RunRequestSpec;
use Leoness\AgentSdk\Platform\Entity\AgentRunInterface;

/**
 * How a package gets work into the platform's queue.
 *
 * Two doors, one code path behind them: a whole batch, or a single item somebody started
 * from a screen. Both of them queue — §15 of the brief says processing must not depend on
 * a browser staying open, and a single item run inline would be a second code path that
 * nobody tested at four hundred.
 *
 * Both doors also **pre-create the run rows and then dispatch one message per item
 * carrying nothing but an id**, which is what makes a killed worker, a redelivered message
 * and a paused batch all behave. A package calling either of these inside its own
 * transaction extends that guarantee to its own rows: mark a product *queued* without a
 * message and it waits forever; send a message for a product not yet marked and the worker
 * gets there first.
 *
 * Who asked is not a parameter — see {@see RunRequestSpec}.
 */
interface RunDispatchInterface
{
    /**
     * Queues a single run — "run it, but not while I wait".
     *
     * @throws UnknownAgentException
     * @throws NoPublishedVersionException
     * @throws InvalidAgentInputException  when the input does not match the declared fields
     */
    public function dispatchRun(RunRequestSpec $spec): AgentRunInterface;

    /**
     * Creates a batch, one pending run per item, and queues them all.
     *
     * Every input is validated against the agent's declared fields *before* anything is
     * written: half a batch is worse than none, and a package that built one bad row
     * should hear about it now rather than from four hundred identical failures.
     *
     * @param list<array<string, mixed>> $inputs raw values, one entry per item; must not be empty
     *
     * @throws UnknownAgentException
     * @throws NoPublishedVersionException
     * @throws InvalidAgentInputException  when any item does not match the declared fields
     * @throws EmptyBatchException         when there is nothing to do
     */
    public function dispatchBatch(string $agentCode, array $inputs, string $label): DispatchedBatch;
}
