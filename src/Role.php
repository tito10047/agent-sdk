<?php

declare(strict_types=1);

namespace Leoness\AgentSdk;

/**
 * The role vocabulary a tool package may check against.
 *
 * The string values are the platform's security role names, verbatim — the platform maps
 * this enum onto its own role catalogue by value, so renaming a case here is a breaking
 * change of the SDK, not a refactoring.
 *
 * Deliberately not the platform enum itself: a package never sees the core, and the core's
 * catalogue may grow cases (operational, internal ones) a package has no business naming.
 */
enum Role: string
{
    /** Can call the tools they are granted and review the proposals those produce. */
    case User = 'ROLE_USER';

    /** Can additionally manage packages, tool permissions and users. */
    case Admin = 'ROLE_ADMIN';
}
