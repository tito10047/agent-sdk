<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Platform;

/**
 * The one question an agent may ask about the people who operate the platform:
 * where do alerts for administrators go.
 *
 * A port this narrow on purpose. An agent that notifies somebody must not browse user
 * accounts to do it — the platform decides who counts as an active administrator, the
 * agent only addresses the envelope.
 */
interface RecipientDirectoryInterface
{
    /**
     * E-mail addresses of the active administrators, possibly empty.
     *
     * An empty list is an answer, not an error: the agent decides whether to log the
     * undeliverable alert or drop it.
     *
     * @return list<string>
     */
    public function activeAdministratorEmails(): array;
}
