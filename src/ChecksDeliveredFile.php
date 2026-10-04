<?php

declare(strict_types=1);

namespace Leoness\AgentSdk;

use Leoness\AgentSdk\Dto\DeliveredFile;
use Leoness\AgentSdk\Dto\FileVerdict;

/**
 * An agent that checks the file its own run produced before anybody is offered it.
 *
 * Implemented by the agent class, like {@see HandlesFinishedRun}, and for the same
 * reason: the platform then needs no registry of checks and no tag, only the agent it is
 * already running.
 *
 * **It is a gate, not a tool.** A check the model can call is a check the model can skip,
 * and the one thing a client cannot live with is an import file that looks finished and
 * does not match the order it was made from. So the platform asks here, every time, in
 * the moment the file is stored — and a refusal means the file is not the run's result.
 *
 * What it must not do is correct anything. It says what differs; writing the file again
 * is the agent's job, and a platform that quietly fixed the numbers would produce a
 * result nobody could explain.
 */
interface ChecksDeliveredFile
{
    public function checkDeliveredFile(DeliveredFile $delivered): FileVerdict;
}
