<?php

declare(strict_types=1);

namespace Leoness\AgentSdk;

/**
 * The role vocabulary an agent manifest may speak.
 *
 * The string values are the platform's security role names, verbatim — the platform maps
 * this enum onto its own role catalogue by value, so renaming a case here is a breaking
 * change of the SDK, not a refactoring.
 *
 * Deliberately not the platform enum itself: an agent package never sees the core, and
 * the core's catalogue may grow cases (operational, internal ones) that a manifest has no
 * business granting.
 */
enum Role: string
{
    /** Can run agents they are allowed to and review the proposals they produce. */
    case User = 'ROLE_USER';

    /** Can additionally manage agents, models, routing rules, budgets and users. */
    case Admin = 'ROLE_ADMIN';
}
