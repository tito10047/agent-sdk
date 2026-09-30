<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Platform\Dto;

use Leoness\AgentSdk\RunTrigger;

/**
 * A package asking the platform to run an agent.
 *
 * **There is no `requestedBy`, and that is the point.** Who is asking is not something a
 * caller gets to state — the platform reads it off the current security token when it
 * creates the run, so a package can neither forget to attribute a run nor attribute one to
 * somebody else. The audit trail of §17 holds because it is not the package's to fill in.
 *
 * The agent is named by code and never by a version: which version runs is the registry's
 * answer, and the answer is "the published one".
 */
final readonly class RunRequestSpec
{
    /**
     * @param array<string, mixed> $input           raw values, checked against the agent's declared input
     *                                              fields before the run is created
     * @param string|null          $modelIdentifier a model this one run must use, overriding routing; null
     *                                              leaves the choice to the Model Router, which is what a
     *                                              package normally wants
     */
    public function __construct(
        public string $agentCode,
        public array $input = [],
        public RunTrigger $trigger = RunTrigger::Manual,
        public ?string $modelIdentifier = null,
    ) {
    }
}
