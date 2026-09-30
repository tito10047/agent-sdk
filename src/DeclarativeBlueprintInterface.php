<?php

declare(strict_types=1);

namespace Leoness\AgentSdk;

use Leoness\AgentSdk\Dto\AgentManifest;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * An agent that *could* have been clicked together in the administration, shipped as code
 * so that a fresh deployment has it working.
 *
 * The difference from {@see AgentInterface} is who owns the definition afterwards. A code
 * agent's class owns it forever and the administration shows it read-only. A blueprint only
 * **seeds** it: the moment it is published it is an ordinary declarative agent like any
 * other, editable in the builder, and an operator may copy it, change the prompt and
 * publish their own. That is the whole point of agents being data.
 *
 * What the platform does with it is exactly what it does with a code agent's manifest, and
 * for the same reasons: a changed manifest publishes a *new* version rather than editing
 * one (runs point at versions), an identical manifest publishes nothing, and an agent with
 * somebody's draft open is left alone and reported. `app:agent:sync` applies all three, on
 * every deployment and inside every package provision.
 *
 * **A blueprint has no runtime.** Nobody calls it while a run is happening — there is no
 * `handles()`, no output reader, no listener. It is read at synchronisation time and has to
 * be a pure description: no queries, no clock, nothing that could make two deployments of
 * the same code produce two different versions. A blueprint that also wants to react to its
 * own finished runs registers a {@see Platform\RunObserverInterface} beside it.
 */
#[AutoconfigureTag(DeclarativeBlueprintInterface::TAG)]
interface DeclarativeBlueprintInterface
{
    /** The container tag the platform collects blueprints by. */
    public const string TAG = 'app.declarative_blueprint';

    /**
     * The agent's code, as it appears in the registry and in a run log.
     *
     * Separate from the manifest so that the platform can name a blueprint in an error
     * message without building its whole manifest first, and so that two blueprints
     * claiming the same code are caught at startup rather than by whichever ran last.
     * Must equal {@see AgentManifest::$code}.
     */
    public function code(): string;

    /**
     * Everything about the agent that would otherwise be typed into the builder.
     */
    public function manifest(): AgentManifest;
}
