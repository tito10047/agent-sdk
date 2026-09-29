<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Support;

/**
 * Puts a decoded JSON structure into a form two of them can be compared in.
 *
 * It exists because **MySQL's native JSON type reorders the keys of an object**: a value
 * written into a `Types::JSON` column and read back differs from the original by key order
 * alone, so `===` between them does not hold. Every place that asks "is this the same JSON
 * as that" — is the installed agent still the blueprint, can the builder reproduce this
 * schema — needs the same answer, so the normalisation is written once.
 *
 * Lists are deliberately left alone: the order of an `enum` or of a tuple is part of the
 * value, and sorting it would make two different schemas compare equal.
 */
final readonly class CanonicalJson
{
    /**
     * @param array<array-key, mixed> $value
     *
     * @return array<array-key, mixed>
     */
    public static function of(array $value): array
    {
        foreach ($value as $key => $item) {
            if (\is_array($item)) {
                $value[$key] = self::of($item);
            }
        }

        if (!array_is_list($value)) {
            ksort($value);
        }

        return $value;
    }
}
