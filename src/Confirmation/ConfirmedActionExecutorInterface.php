<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Confirmation;

use Leoness\AgentSdk\Confirmation\Dto\ActionOutcome;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * Carries out one kind of action once a person has confirmed it.
 *
 * One class per kind, found by {@see self::type()} — the platform knows no tool and no
 * package, and a package adds a confirmable action by adding an executor, never by editing
 * a list.
 *
 * **Runs as the person who proposed the action**, with their permissions: the platform puts
 * them in the security token for the duration. An executor therefore checks access the way
 * a tool does, and a permission lost between proposing and confirming means the action is
 * refused, not carried out on the strength of yesterday's rights.
 */
#[AutoconfigureTag(self::TAG)]
interface ConfirmedActionExecutorInterface
{
    public const string TAG = 'app.confirmed_action_executor';

    /**
     * The {@see Dto\ProposedAction::$type} this executor answers for.
     */
    public static function type(): string;

    /**
     * @param array<string, mixed> $payload exactly what the tool stored when it proposed the action
     */
    public function execute(array $payload): ActionOutcome;
}
