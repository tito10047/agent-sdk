<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Catalogue\Dto;

/**
 * One technical parameter of a catalogued product: a name and a value.
 *
 * Deliberately not a {@see \Leoness\AgentSdk\Dto\FactClaim}. A claim carries a status and a
 * source because it is something a *model* proposed; what comes back from a catalogue was
 * approved by a named person in the product module, which is a stronger backing than any
 * source a model could name — so there is no status for it to be in.
 */
final readonly class CatalogueParameter
{
    public function __construct(
        public string $name,
        public string $value,
    ) {
    }
}
