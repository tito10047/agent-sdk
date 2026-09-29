<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Validation;

/**
 * The rules of strict structured output: an object root, described properties, no extra
 * keys allowed, and everything listed as required.
 *
 * Written once, here, because two parties enforce them and must never disagree: the
 * platform refuses to save a schema that breaks them, and the deployability test of an
 * agent repository refuses to pass one. A schema the test accepted and the platform
 * rejected would fail at installation time, in front of the wrong person.
 *
 * Only the first problem is reported. A schema that breaks one of these rules is
 * usually half-written, and a list of twenty violations helps nobody.
 */
final readonly class OutputSchemaRules
{
    /**
     * @param array<mixed>|null $schema null meaning the agent answers free text, which
     *                                  is a valid choice and never a problem
     */
    public function firstProblem(?array $schema): ?ManifestProblem
    {
        if (null === $schema) {
            return null;
        }

        return $this->walk($schema, 'koreň schémy');
    }

    /**
     * @param array<mixed> $schema
     */
    private function walk(array $schema, string $path): ?ManifestProblem
    {
        $type = $schema['type'] ?? null;

        if ('array' === $type) {
            $items = $schema['items'] ?? null;

            if (!\is_array($items)) {
                return new ManifestProblem($path, 'pole musí mať popísaný obsah v "items"');
            }

            return $this->walk($items, $path.'[]');
        }

        if ('object' !== $type) {
            // The root has to be an object; deeper down, scalars are the normal case.
            return 'koreň schémy' === $path
                ? new ManifestProblem($path, 'musí byť typu "object"')
                : null;
        }

        $properties = $schema['properties'] ?? null;

        if (!\is_array($properties) || [] === $properties) {
            return new ManifestProblem($path, 'objekt musí mať aspoň jednu vlastnosť v "properties"');
        }

        if (false !== ($schema['additionalProperties'] ?? null)) {
            return new ManifestProblem($path, 'objekt musí mať "additionalProperties": false');
        }

        $required = $schema['required'] ?? [];

        if (!\is_array($required)) {
            return new ManifestProblem($path, '"required" musí byť zoznam názvov vlastností');
        }

        $names = array_map(strval(...), array_keys($properties));
        $missing = array_diff($names, array_map(strval(...), array_values($required)));

        if ([] !== $missing) {
            return new ManifestProblem($path, \sprintf('v "required" chýbajú vlastnosti: %s', implode(', ', $missing)));
        }

        foreach ($properties as $name => $property) {
            if (!\is_array($property)) {
                return new ManifestProblem($path.'.'.$name, 'vlastnosť musí byť popísaná objektom schémy');
            }

            $problem = $this->walk($property, $path.'.'.$name);

            if (null !== $problem) {
                return $problem;
            }
        }

        return null;
    }
}
