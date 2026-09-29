<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Dto;

use Leoness\AgentSdk\FactStatus;

/**
 * The convention for every piece of data a model is not free to invent: instead of a
 * bare value, the answer carries the value, how well it is backed, and by what.
 *
 * ```json
 * "sirka_mm": { "hodnota": "600", "stav": "potvrdeny", "zdroj": "dodávateľský list, riadok 12" }
 * "material":  { "hodnota": null,  "stav": "nepotvrdeny", "zdroj": null }
 * ```
 *
 * Fixing this shape in the core — rather than leaving each agent to invent its own —
 * is what makes section 11 of the brief enforceable at all: the validator can find
 * claims in any answer of any agent by their shape, the product module can store the
 * triple "original / proposal / approved" without a per-agent mapping, and the agent
 * builder can offer the fragment instead of asking an operator to retype it.
 *
 * A property is treated as a claim when it declares exactly these three keys. That is
 * an opt-in: an agent whose output has plain strings is simply not making claims the
 * platform can police, which is a decision for the agent author and their schema.
 */
final readonly class FactClaim
{
    public const string VALUE = 'hodnota';
    public const string STATUS = 'stav';
    public const string SOURCE = 'zdroj';

    public function __construct(
        public ?string $value,
        public FactStatus $status,
        public ?string $source,
    ) {
    }

    /**
     * The canonical JSON schema fragment for one claim, ready to be dropped into the
     * "properties" of an agent output schema.
     *
     * The descriptions are part of it on purpose: they are the only instructions the
     * model gets at the exact point where it is tempted to make something up.
     *
     * @return array<string, mixed>
     */
    public static function schema(string $description): array
    {
        return [
            'type' => 'object',
            'description' => $description,
            'additionalProperties' => false,
            'required' => [self::VALUE, self::STATUS, self::SOURCE],
            'properties' => [
                self::VALUE => [
                    'type' => ['string', 'null'],
                    'description' => 'Hodnota údaja. Ak údaj nepoznáš, napíš null — nikdy si ho nevymýšľaj.',
                ],
                self::STATUS => [
                    'type' => 'string',
                    'enum' => FactStatus::values(),
                    'description' => \sprintf(
                        '"%s" = údaj je vo vstupných dátach alebo v uvedenom zdroji, '
                        .'"%s" = tvoj návrh, ktorý musí človek overiť, '
                        .'"%s" = údaj nie je známy (potom musí byť hodnota null).',
                        FactStatus::Confirmed->value,
                        FactStatus::Suggested->value,
                        FactStatus::Unknown->value,
                    ),
                ],
                self::SOURCE => [
                    'type' => ['string', 'null'],
                    'description' => 'Odkiaľ údaj pochádza (napr. "vstupný popis", "dodávateľský list"). Pri potvrdenom údaji je povinný.',
                ],
            ],
        ];
    }

    /**
     * Whether a sub-schema describes a claim, i.e. whether the agent author opted into
     * the convention for this property.
     *
     * @param array<string, mixed> $schema
     */
    public static function describedBy(array $schema): bool
    {
        $properties = $schema['properties'] ?? null;

        if ('object' !== ($schema['type'] ?? null) || !\is_array($properties)) {
            return false;
        }

        $declared = array_map(strval(...), array_keys($properties));
        sort($declared);

        $expected = [self::SOURCE, self::STATUS, self::VALUE];
        sort($expected);

        return $declared === $expected;
    }

    /**
     * Reads one claim out of a model answer.
     *
     * @return self|null null when the value does not follow the convention at all —
     *                   the caller reports that as a violation, it is never ignored
     */
    public static function fromOutput(mixed $value): ?self
    {
        if (!\is_array($value)) {
            return null;
        }

        $status = FactStatus::tryFrom(\is_string($value[self::STATUS] ?? null) ? $value[self::STATUS] : '');

        if (null === $status) {
            return null;
        }

        return new self(
            self::textOrNull($value[self::VALUE] ?? null),
            $status,
            self::textOrNull($value[self::SOURCE] ?? null),
        );
    }

    public function hasValue(): bool
    {
        return null !== $this->value && '' !== trim($this->value);
    }

    public function hasSource(): bool
    {
        return null !== $this->source && '' !== trim($this->source);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            self::VALUE => $this->value,
            self::STATUS => $this->status->value,
            self::SOURCE => $this->source,
        ];
    }

    /**
     * Numbers are accepted and stringified: a model asked for a width will sometimes
     * answer 600 rather than "600", and refusing that would be pedantry, not safety.
     */
    private static function textOrNull(mixed $value): ?string
    {
        return match (true) {
            \is_string($value) => $value,
            \is_int($value), \is_float($value) => (string) $value,
            default => null,
        };
    }
}
