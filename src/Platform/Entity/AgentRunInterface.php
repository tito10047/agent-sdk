<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Platform\Entity;

use Leoness\AgentSdk\RunStatus;
use Leoness\AgentSdk\RunTrigger;

/**
 * One recorded execution of an agent, as a package may read it.
 *
 * The run record is the platform's audit trail — agent, version, user, input, model,
 * tokens, cost, result — and none of that is a package's to write. What a package needs
 * is the handful of answers it takes to turn a finished run into its own data: was this
 * ours, did it succeed, what went in, what came out. That is this interface, and it is
 * read-only on purpose. The one write an agent may make is a warning, and it goes through
 * {@see \Leoness\AgentSdk\Platform\RunAnnotatorInterface}.
 *
 * It doubles as the Doctrine target for a package entity that wants a real foreign key to
 * `agent_run` — declared as `targetEntity: AgentRunInterface::class` and resolved by the
 * platform through `doctrine.orm.resolve_target_entities`. The key and its delete rule are
 * unchanged; only the class name is indirect, which is what keeps the core entity out of
 * the package's sight.
 */
interface AgentRunInterface
{
    /** Null only before the run has been persisted, which a package never sees. */
    public function getId(): ?int;

    /**
     * The code of the agent this run belongs to.
     *
     * A code and not a definition: the definition is a core entity with versions,
     * budgets and permissions hanging off it, and a package deciding "is this run mine"
     * is comparing a string it declared itself.
     */
    public function agentCode(): string;

    /**
     * Why the run happened. Worth reading before acting on an answer: a
     * {@see RunTrigger::Test} run is a rehearsal of an unpublished draft, and treating
     * its output as real would write the opinion of a version that does not run.
     */
    public function getTrigger(): RunTrigger;

    public function getStatus(): RunStatus;

    /**
     * The input the run was created with, frozen at that moment.
     *
     * @return array<string, mixed>
     */
    public function getInputSnapshot(): array;

    /**
     * The structured answer, or null when the run has none — it failed, was cancelled,
     * or the agent answers in plain text.
     *
     * @return array<string, mixed>|null
     */
    public function getOutput(): ?array;

    /** Who asked for it; null for a run nobody started by hand. */
    public function getRequestedBy(): ?PlatformUserInterface;
}
