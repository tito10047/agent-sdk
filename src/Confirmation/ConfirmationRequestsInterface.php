<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Confirmation;

use Leoness\AgentSdk\Confirmation\Dto\IssuedConfirmation;
use Leoness\AgentSdk\Confirmation\Dto\ProposedAction;

/**
 * Hands an action over for a person to confirm, instead of carrying it out.
 *
 * The tool gets back a link to give the person and an id to ask about; nothing has happened
 * yet. Whatever the model says next cannot change that — only the person, on the page. The
 * proposal is made in the name of whoever called the tool; a package never names the person,
 * so it cannot propose on somebody else's behalf.
 */
interface ConfirmationRequestsInterface
{
    /**
     * @throws \LogicException when nobody is signed in — a tool reached through MCP never
     *                         sees that, the MCP handler refuses such a call first
     */
    public function request(ProposedAction $action): IssuedConfirmation;
}
