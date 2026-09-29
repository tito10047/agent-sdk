<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Validation;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;

/**
 * The functions an agent's validation rules may call, on top of the operators
 * ExpressionLanguage already provides (`matches`, `contains`, `starts with`, `in`, …).
 *
 * The list is short on purpose. Rules are written by administrators in a text field and
 * run on every item of a batch, so everything here has to be total: no exceptions, no
 * side effects, no reaching outside the answer. `length()` accepting strings, lists and
 * null instead of three separate functions is the same idea — an operator writing
 * `length(output['seo_title']) <= 60` should not have to know the PHP type behind it.
 *
 * Deliberately not an {@see \Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface}:
 * that interface is autoconfigured into the routing and security expression languages,
 * and these functions have no business there.
 */
final class RuleFunctions
{
    /**
     * @return list<ExpressionFunction>
     */
    public function all(): array
    {
        return [
            $this->expose('length', self::length(...)),
            $this->expose('trim', self::trim(...)),
            $this->expose('lower', self::lower(...)),
            $this->expose('upper', self::upper(...)),
        ];
    }

    /**
     * Number of characters of a text, number of items of a list, 0 for a missing value.
     */
    public static function length(mixed $value): int
    {
        return match (true) {
            \is_string($value) => mb_strlen($value),
            \is_array($value), $value instanceof \Countable => \count($value),
            null === $value => 0,
            default => mb_strlen(self::text($value)),
        };
    }

    public static function trim(mixed $value): string
    {
        return trim(self::text($value));
    }

    public static function lower(mixed $value): string
    {
        return mb_strtolower(self::text($value));
    }

    public static function upper(mixed $value): string
    {
        return mb_strtoupper(self::text($value));
    }

    /**
     * Every function here takes exactly one value and returns one — deliberately, so
     * that a rule stays something an administrator can read back. A function that needs
     * two arguments would be a new helper, not a looser signature here.
     *
     * @param callable(mixed): (int|string) $implementation
     */
    private function expose(string $name, callable $implementation): ExpressionFunction
    {
        return new ExpressionFunction(
            $name,
            static fn (string $argument): string => \sprintf('\%s::%s(%s)', self::class, $name, $argument),
            static fn (array $values, mixed $value): int|string => $implementation($value),
        );
    }

    private static function text(mixed $value): string
    {
        return match (true) {
            \is_string($value) => $value,
            \is_scalar($value) => (string) $value,
            null === $value => '',
            // Arrays and objects have no sensible text form; an empty one keeps a rule
            // from dying on a field the model answered with the wrong type.
            default => '',
        };
    }
}
