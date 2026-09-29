<?php

declare(strict_types=1);

namespace Leoness\AgentSdk;

use Leoness\AgentSdk\Dto\AgentManifest;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * An agent written in PHP rather than clicked together in the administration.
 *
 * Section 7 of the brief: a new agent has to be a module somebody adds to a repository,
 * reviews like any other code and deploys — with no change to the core. This interface is
 * the whole of that contract. A class that implements it and carries the tag *is* an agent
 * from the moment the platform synchronises it; nothing in the registry, the runner, the
 * router or the administration is told about it.
 *
 * **A code agent is not a second kind of agent.** Its definition lands in the same table as
 * every other, which is what gives it versions, budgets, permissions and an audit trail, and
 * it runs through the same runner. The only difference is who owns the definition: here it is
 * the class, and the administration shows it read-only.
 *
 * What a code agent may reach is what every agent may reach and no more — the tools and data
 * sources its manifest whitelists, resolved by the platform's default-deny registries. There
 * is no back door to the database: an agent that needs to read something needs a
 * {@see DataSource\DataSourceInterface} for it, reviewed like the rest.
 *
 * Implementations that also want to do something with their own finished runs implement
 * {@see HandlesFinishedRun}. Most do not, which is why it is a separate interface rather
 * than a method every agent has to leave empty.
 */
#[AutoconfigureTag(AgentInterface::TAG)]
interface AgentInterface
{
    /** The container tag the platform collects agents by. */
    public const string TAG = 'app.agent';

    /**
     * Everything about the agent that would otherwise be typed into the builder.
     *
     * Read on every synchronisation and compared with what is published, so it has to be a
     * pure description: no queries, no clock, nothing that could make two deployments of the
     * same code produce two different versions.
     */
    public function manifest(): AgentManifest;
}
