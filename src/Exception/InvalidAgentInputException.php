<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Exception;

/**
 * The data handed to an agent does not match the inputs its version declares.
 *
 * "Default deny" applied to data: an agent sees exactly the fields its published version
 * asks for — no more, no fewer — so nothing can smuggle an extra variable into a prompt
 * that was never reviewed with it in mind. A package that hits this has built a row the
 * agent cannot accept, and the message names the field.
 *
 * The platform's own exception extends this one. An `\InvalidArgumentException` rather
 * than a runtime one, because the caller could have known.
 */
class InvalidAgentInputException extends \InvalidArgumentException
{
}
