<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Settings\Dto;

use Leoness\AgentSdk\Settings\Exception\UnknownSettingException;

/**
 * The values of one package's settings in one scope, already resolved: a value the
 * administrator left empty is the declared default, so a package never handles "not set".
 *
 * Typed getters, because the declaration fixes the type and a package should not cast.
 */
final readonly class SettingValues
{
    /**
     * @param array<string, string|int|float|bool|list<string>> $values every declared key
     */
    public function __construct(
        private string $package,
        private array $values,
    ) {
    }

    public function string(string $key): string
    {
        $value = $this->value($key);

        return \is_string($value) ? $value : throw UnknownSettingException::of($this->package, $key);
    }

    public function int(string $key): int
    {
        $value = $this->value($key);

        return \is_int($value) ? $value : throw UnknownSettingException::of($this->package, $key);
    }

    public function float(string $key): float
    {
        $value = $this->value($key);

        return \is_int($value) || \is_float($value) ? (float) $value : throw UnknownSettingException::of($this->package, $key);
    }

    public function bool(string $key): bool
    {
        $value = $this->value($key);

        return \is_bool($value) ? $value : throw UnknownSettingException::of($this->package, $key);
    }

    /**
     * @return list<string>
     */
    public function list(string $key): array
    {
        $value = $this->value($key);

        return \is_array($value) ? $value : throw UnknownSettingException::of($this->package, $key);
    }

    /**
     * @return string|int|float|bool|list<string>
     */
    private function value(string $key): string|int|float|bool|array
    {
        return \array_key_exists($key, $this->values)
            ? $this->values[$key]
            : throw UnknownSettingException::of($this->package, $key);
    }
}
