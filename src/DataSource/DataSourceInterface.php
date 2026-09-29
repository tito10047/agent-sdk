<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\DataSource;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * A body of data an agent may read: products today, ONIX or supplier feeds later.
 *
 * An adapter, not a repository. What it returns is what an agent is allowed to see of
 * the underlying table — no entities, no associations to walk, no way to reach the
 * neighbouring data by following a relation. Section 20 of the brief rules out
 * unrestricted database access for agents, and this interface is where that is decided
 * once instead of trusted everywhere.
 *
 * Registering a source is a tagged service; whether a given agent may read it is the
 * whitelist of its version, enforced by the platform.
 */
#[AutoconfigureTag(DataSourceInterface::TAG)]
interface DataSourceInterface
{
    /** The container tag the platform collects data sources by. */
    public const string TAG = 'app.data_source';

    public function manifest(): DataSourceManifest;

    /**
     * Reads the records matching the criteria. An unknown criterion is the caller's
     * mistake and an implementation says so rather than widening the result.
     *
     * @param array<string, scalar|null> $criteria
     *
     * @return list<array<string, mixed>>
     */
    public function fetch(array $criteria): array;
}
