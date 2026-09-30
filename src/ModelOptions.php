<?php

declare(strict_types=1);

namespace Leoness\AgentSdk;

/**
 * The keys a manifest may put into {@see Dto\AgentManifest::$modelOptions}.
 *
 * Constants rather than bare strings because the platform reads the same keys back:
 * an agent package and the core runner must agree on the spelling, and a constant is
 * the only spelling the type checker can vouch for across two repositories.
 */
final class ModelOptions
{
    /**
     * How many tool calls one run may make before the platform stops it. A ceiling
     * against a confused model looping on its own output, not a target.
     */
    public const string MAX_TOOL_CALLS = 'max_tool_calls';
}
