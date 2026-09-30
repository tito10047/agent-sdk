<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Platform;

use Leoness\AgentSdk\Exception\NoPublishedVersionException;
use Leoness\AgentSdk\Exception\UnknownAgentException;
use Leoness\AgentSdk\Platform\Dto\RunnableAgentVersion;

/**
 * What a package may ask about an agent before sending it work.
 *
 * One question, because there is only one a package legitimately has: *what does the
 * version that would run right now accept?* Everything else it might want to know —
 * whether the agent is switched on, which model it will pick, what it has cost this month —
 * is either implied by the answer or none of its business.
 *
 * The listing of agents is deliberately absent. A package is written around a specific
 * agent whose code it declares itself; enumerating the platform's other agents would let
 * it discover work it was never given.
 */
interface AgentDirectoryInterface
{
    /**
     * @throws UnknownAgentException       when no active agent has that code
     * @throws NoPublishedVersionException when the agent only exists as a draft
     */
    public function runnableVersion(string $agentCode): RunnableAgentVersion;
}
