<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\DataSource;

/**
 * What a data source is called and what it holds.
 *
 * The name is what an administrator whitelists on an agent version, so it is stable
 * vocabulary — renaming one silently disables every agent that named it, which is why
 * the version validator checks the whitelists against the registry.
 */
final readonly class DataSourceManifest
{
    private const string NAME_PATTERN = '/^[a-z][a-z0-9_]{1,63}$/';

    public function __construct(
        public string $name,
        public string $description,
    ) {
        if (1 !== preg_match(self::NAME_PATTERN, $this->name)) {
            throw new \InvalidArgumentException(\sprintf('Názov dátového zdroja "%s" nie je použiteľný: povolené sú malé písmená, číslice a podčiarkovník, 2–64 znakov.', $this->name));
        }

        if ('' === trim($this->description)) {
            throw new \InvalidArgumentException(\sprintf('Dátový zdroj "%s" nemá popis; bez neho nikto nevie, čo agentovi povoľuje.', $this->name));
        }
    }
}
