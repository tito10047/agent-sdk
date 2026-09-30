<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Platform\Entity;

/**
 * A platform account, as much of one as a package is allowed to see.
 *
 * Two jobs, and both of them are about *not* handing over the core's user entity. The
 * first is a name to write next to a decision: a package that records "who approved this
 * field" needs something to store and something to show, and it needs neither the roles
 * nor the password hash to do it. The second is Doctrine: a package entity that wants a
 * real foreign key to the user table declares `targetEntity: PlatformUserInterface::class`
 * and the platform resolves it through `doctrine.orm.resolve_target_entities`. The column,
 * the key and the `ON DELETE SET NULL` are all still there — only the class name is
 * indirect.
 *
 * Deliberately read-only and deliberately small. Permissions are not asked here but of
 * {@see \Leoness\AgentSdk\Platform\AgentAuthorizationInterface}, because "may this person
 * do this" is a decision the platform makes and not a property of an account.
 */
interface PlatformUserInterface
{
    /** Null only before the account has been persisted, which a package never sees. */
    public function getId(): ?int;

    /** The person's name, for showing next to what they did. */
    public function getName(): string;

    public function getEmail(): string;
}
