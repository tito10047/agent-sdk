<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Dto;

use Leoness\AgentSdk\OutputFieldKind;
use Leoness\AgentSdk\Support\CanonicalJson;

/**
 * An output schema as a list of fields — the shape the builder edits, instead of the JSON
 * an operator would otherwise have to type by hand.
 *
 * Everything this emits satisfies strict structured output by construction: an object
 * root, `additionalProperties: false`, every property described and listed in `required`.
 * The constraint that checks those rules therefore has nothing left to catch on a schema
 * that came from here — which is the point of editing fields rather than text.
 *
 * **{@see self::fromSchema()} only accepts a schema it can reproduce exactly.** It parses,
 * re-emits and compares; anything that differs — a `maxLength`, a nested object, a type
 * union the builder has no widget for — is reported as "not editable here" rather than
 * loaded into the editor and silently flattened on the next save. An agent's contract with
 * a model is not something a screen may quietly rewrite.
 */
final readonly class OutputSchemaPlan
{
    /**
     * @param list<OutputField> $fields
     */
    public function __construct(
        public array $fields = [],
    ) {
    }

    /**
     * @param array<string, mixed>|null $schema null meaning the agent answers free text
     *
     * @return self|null null when the schema is not one this editor can represent
     */
    public static function fromSchema(?array $schema): ?self
    {
        if (null === $schema) {
            return new self();
        }

        $properties = $schema['properties'] ?? null;

        if ('object' !== ($schema['type'] ?? null) || !\is_array($properties)) {
            return null;
        }

        $fields = [];

        foreach ($properties as $name => $property) {
            if (!\is_string($name) || !\is_array($property)) {
                return null;
            }

            $kind = OutputFieldKind::of($property);
            $description = $property['description'] ?? null;

            if (null === $kind || (null !== $description && !\is_string($description))) {
                return null;
            }

            try {
                $fields[] = new OutputField($name, $kind, $description);
            } catch (\InvalidArgumentException) {
                // A property name this editor could not offer back — e.g. camel case. The
                // schema stays as it is rather than being renamed behind the agent's back.
                return null;
            }
        }

        $plan = new self($fields);

        return self::comparable($plan->toSchema() ?? []) === self::comparable($schema) ? $plan : null;
    }

    /**
     * @return array<string, mixed>|null null when nothing is declared, which is how an
     *                                   agent says it answers free text
     */
    public function toSchema(): ?array
    {
        if ([] === $this->fields) {
            return null;
        }

        $properties = [];

        foreach ($this->fields as $field) {
            $properties[$field->name] = $field->schema();
        }

        return [
            'type' => 'object',
            'additionalProperties' => false,
            'required' => array_keys($properties),
            'properties' => $properties,
        ];
    }

    public function isEmpty(): bool
    {
        return [] === $this->fields;
    }

    /**
     * Object key order is normalised because MySQL reorders it on the way out of a JSON
     * column, and `required` because it is a set written as a list — two schemas that
     * differ only in the order they list the same property names are the same schema.
     * No other list is touched: the order of an `enum` is part of its meaning.
     *
     * @param array<array-key, mixed> $schema
     *
     * @return array<array-key, mixed>
     */
    private static function comparable(array $schema): array
    {
        return CanonicalJson::of(self::withSortedRequirements($schema));
    }

    /**
     * @param array<array-key, mixed> $schema
     *
     * @return array<array-key, mixed>
     */
    private static function withSortedRequirements(array $schema): array
    {
        foreach ($schema as $key => $value) {
            if (!\is_array($value)) {
                continue;
            }

            // The list test matters: a schema may legitimately describe a property called
            // "required", and that one is an object to recurse into, not a set to sort.
            if ('required' === $key && array_is_list($value)) {
                sort($value);
                $schema[$key] = $value;

                continue;
            }

            $schema[$key] = self::withSortedRequirements($value);
        }

        return $schema;
    }
}
