<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Dto;

use Leoness\AgentSdk\OutputFieldKind;

/**
 * One property of an agent's answer, as the builder edits it.
 *
 * The name is restricted to the same identifier shape as an input field, for the same
 * kind of reason: it is what a validation rule addresses (`output['seo_title']`) and what
 * a domain module maps onto its own fields, and a property called "SEO title" would make
 * both awkward in a way nobody would notice until a rule silently stopped matching.
 */
final readonly class OutputField
{
    public const string NAME_PATTERN = AgentInputField::NAME_PATTERN;

    public function __construct(
        public string $name,
        public OutputFieldKind $kind = OutputFieldKind::Text,
        public ?string $description = null,
    ) {
        if (1 !== preg_match(self::NAME_PATTERN, $this->name)) {
            throw new \InvalidArgumentException(\sprintf('Názov výstupného poľa "%s" nie je použiteľný; povolené sú malé písmená, číslice a podčiarkovník, začínať musí písmenom.', $this->name));
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function schema(): array
    {
        return $this->kind->schema($this->description);
    }
}
