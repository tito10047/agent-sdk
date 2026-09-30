<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Platform\Dto;

use Leoness\AgentSdk\Dto\AgentInputField;

/**
 * The frozen snapshot a new run of an agent would use, as far as a package needs to know
 * it.
 *
 * What a package actually does with a version is build an input for it, and for that it
 * needs the declared fields and nothing else: the prompt, the schema, the model ladder and
 * the budget are the platform's business and an operator's. The version number is here so
 * that a package can say *which* snapshot it built against in its own log.
 *
 * Read rather than remembered. An operator publishes a new version between two batches and
 * the field list changes underneath a package that cached it.
 */
final readonly class RunnableAgentVersion
{
    /**
     * @param int                   $number      the published version's own number, as an operator sees it
     * @param list<AgentInputField> $inputFields exactly what this version accepts, in declared order
     */
    public function __construct(
        public string $agentCode,
        public int $number,
        public array $inputFields = [],
    ) {
    }

    /**
     * The declared field names, for the common "which of these can I fill" check.
     *
     * @return list<string>
     */
    public function fieldNames(): array
    {
        return array_map(static fn (AgentInputField $field): string => $field->name, $this->inputFields);
    }
}
