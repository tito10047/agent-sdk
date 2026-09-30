<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Platform;

use Leoness\AgentSdk\Platform\Entity\PlatformUserInterface;

/**
 * Whether a person may spend the platform's money on an agent.
 *
 * A package that offers a "generate this again" button has to answer that, and the answer
 * is not the package's to invent: which roles may run which agent is set once per agent in
 * the administration, and a second list kept by a package would drift from the first within
 * a month. So the package asks.
 *
 * **Default deny.** No user, an agent that does not exist, an agent with no published
 * version, an agent switched off — all of them are false, and none of them is an exception.
 * The caller is deciding whether to show a button, and a button is not the place to find
 * out that somebody deactivated an agent.
 *
 * Only *running* is asked here, because that is the permission a package can reach a
 * decision with. Seeing and managing an agent are the administration's own screens.
 */
interface AgentAuthorizationInterface
{
    /**
     * @param PlatformUserInterface|null $user the signed-in account the answer is about; the
     *                                         platform refuses outright if this is not the account
     *                                         the current request is authenticated as, because it
     *                                         cannot truthfully answer for anybody else
     */
    public function canRunAgent(?PlatformUserInterface $user, string $agentCode): bool;
}
