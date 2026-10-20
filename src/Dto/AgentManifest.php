<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Dto;

use Leoness\AgentSdk\ModelMode;
use Leoness\AgentSdk\Role;

/**
 * An agent declared in PHP: everything the administration would otherwise be clicked
 * through, written down in one place and kept in version control.
 *
 * It mirrors the split the platform is built on. **Identity** — name, description, purpose,
 * who may use it — belongs to the agent's definition and may change without invalidating a
 * single past run. **Content** — instructions, prompt, inputs, output schema, rules,
 * whitelists, model mode — belongs to a version and is frozen the moment it is published,
 * because every run points at it and has to stay explainable.
 *
 * So a manifest is not applied to a running agent: the platform compares it with what is
 * published, and a difference becomes a new version. That comparison — and everything else
 * that touches platform records — lives on the platform side; this class is deliberately
 * nothing but the description.
 */
final readonly class AgentManifest
{
    /**
     * @param list<AgentInputField>     $inputFields
     * @param array<string, mixed>|null $outputSchema               null means the agent answers free text
     * @param list<ValidationRule>      $validationRules
     * @param list<string>              $allowedTools               default deny: an empty list is no tools at all
     * @param list<string>              $allowedDataSources         default deny, as above
     * @param list<Role>                $allowedRoles               on top of administrators; empty means administrators only
     * @param array<string, mixed>      $modelOptions
     * @param list<string>              $escalationModelIdentifiers
     * @param string|null               $requiresTemplate           the kind of output template the
     *                                                              deliverable needs, by key, or null
     */
    public function __construct(
        public string $code,
        public string $name,
        public string $promptTemplate,
        public array $inputFields,
        public ?array $outputSchema = null,
        public string $systemInstructions = '',
        public ?string $description = null,
        public ?string $purpose = null,
        public array $validationRules = [],
        public array $allowedTools = [],
        public array $allowedDataSources = [],
        public array $allowedRoles = [],
        public ModelMode $modelMode = ModelMode::Automatic,
        public ?string $fixedModelIdentifier = null,
        public array $modelOptions = [],
        public array $escalationModelIdentifiers = [],
        public bool $requiresManualApproval = true,
        /**
         * Which kind of output template this agent's deliverable needs.
         *
         * **A key and not a boolean.** The boolean this replaced could only ever mean
         * "an uploaded XLSX file", because that was the only kind the platform had — so a
         * package whose deliverable is a priced offer had no way to say that what it needs
         * is a record describing a document. The key names a kind the *platform* knows;
         * the SDK deliberately does not enumerate them, because which kinds exist is a
         * property of the installation, not of the SDK, and a package pinned to an older
         * SDK must still be able to name a newer kind.
         *
         * {@see \Leoness\AgentSdk\Validation\ManifestLinter} therefore only checks that
         * it is a non-empty string; whether the key exists is the platform's answer, with
         * the list of real ones in the message.
         */
        public ?string $requiresTemplate = null,
        public bool $usesMemory = false,
    ) {
    }
}
