<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Confirmation\Dto;

/**
 * An action a tool wants to take and may not take without a person: what it is, in words,
 * and everything its executor needs to carry it out later.
 *
 * The tool only describes. Carrying the action out is the business of the executor named by
 * {@see self::$type} ({@see \Leoness\AgentSdk\Confirmation\ConfirmedActionExecutorInterface}),
 * and it happens only after the person has read {@see self::$summary} on the confirmation
 * page — so a model cannot get from "proposed" to "done" on its own.
 *
 * **The permission travels with the action.** Proposing it was checked by the tool layer;
 * carrying it out is checked again, as the proposer, the moment they click — a role taken
 * away in between means the action is refused, whatever the executor would allow.
 */
final readonly class ProposedAction
{
    /**
     * @param string               $type       the executor that will carry it out
     * @param string               $permission what the person must still hold when they confirm —
     *                                         the `TOOL_*` permission of the tool that proposed it
     * @param string               $summary    one sentence the person agrees to, numbers already filled in
     * @param array<string, mixed> $payload    what the executor needs — JSON-serialisable, stored as is
     * @param list<string>         $details    further lines shown under the summary
     */
    public function __construct(
        public string $type,
        public string $permission,
        public string $summary,
        public array $payload,
        public array $details = [],
    ) {
    }
}
