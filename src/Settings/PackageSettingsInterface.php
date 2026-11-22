<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Settings;

use Leoness\AgentSdk\Settings\Dto\SettingValues;

/**
 * Values an administrator (or a user, for their own scope) set for a package in the
 * administration, without a new release of the package and without a screen of its own.
 *
 * The package declares its settings in `leoness-agent.json` under `settings` (key, type,
 * label, description, default, scope `installation|user`, `secret`); the platform renders
 * the form from that declaration and stores the values, secrets encrypted. This is the
 * layer for values that differ per installation — rules that need review belong in the
 * package's files, long texts in the Library, company rules in Memory.
 *
 * `$package` is the package's composer name, e.g. `leoness/agent-emailovy`.
 */
interface PackageSettingsInterface
{
    public function installation(string $package): SettingValues;

    /**
     * The signed-in user's values, falling back to the installation's for keys declared
     * with scope `user` that the user left empty.
     */
    public function currentUser(string $package): SettingValues;
}
