<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Dto;

use Leoness\AgentSdk\AgentInputType;

/**
 * One declared input of an agent version.
 *
 * {@see self::$name} is the variable the prompt template addresses, which is why it
 * is restricted to a Twig-safe identifier: an input called "Názov produktu" would
 * render as nothing and the mistake would only show up in the model's answer.
 */
final readonly class AgentInputField
{
    /** A Twig-addressable identifier — lower snake case, starting with a letter. */
    public const string NAME_PATTERN = '/^[a-z][a-z0-9_]{0,63}$/';

    public function __construct(
        public string $name,
        public string $label,
        public AgentInputType $type = AgentInputType::Text,
        public bool $required = true,
        public ?string $description = null,
    ) {
        if (1 !== preg_match(self::NAME_PATTERN, $this->name)) {
            throw new \InvalidArgumentException(\sprintf('Názov vstupného poľa "%s" nie je použiteľný v prompte; povolené sú malé písmená, číslice a podčiarkovník, začínať musí písmenom.', $this->name));
        }

        if ('' === trim($this->label)) {
            throw new \InvalidArgumentException(\sprintf('Vstupné pole "%s" nemá popisku.', $this->name));
        }
    }

    /**
     * @param array<string, mixed> $data as produced by {@see self::toArray()}
     */
    public static function fromArray(array $data): self
    {
        $name = $data['name'] ?? null;
        $label = $data['label'] ?? null;
        $type = $data['type'] ?? AgentInputType::Text->value;
        $required = $data['required'] ?? true;
        $description = $data['description'] ?? null;

        if (!\is_string($name) || !\is_string($label) || !\is_string($type) || !\is_bool($required)) {
            throw new \InvalidArgumentException('Definícia vstupného poľa agenta je poškodená.');
        }

        return new self(
            $name,
            $label,
            AgentInputType::from($type),
            $required,
            \is_string($description) && '' !== $description ? $description : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label,
            'type' => $this->type->value,
            'required' => $this->required,
            'description' => $this->description,
        ];
    }
}
