<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Catalogue;

use Leoness\AgentSdk\Catalogue\Dto\CatalogueItem;

/**
 * Where the platform looks up what it knows about a product.
 *
 * **A port in the SDK with its adapter in a package** — in the SDK rather than in the
 * platform for a reason the platform's own rules enforce: a package may not name an
 * `App\*` class at all (`App\Packaging\PackageVet`), so a port declared platform-side could
 * never be implemented by the package that has the data.
 *
 * **Without an implementation everything still works**, deliberately: the item in the
 * payload then has to be complete, and the tool's answer says which fields nobody filled in
 * rather than producing a card with no button and no eyebrow. Half a catalogue is worse than
 * none, because it looks like a decision.
 *
 * Looked up in one call rather than one per item: a catalogue offer has thirty-three
 * products, and thirty-three round trips inside a tool call is how a tool becomes the slow
 * part of a run.
 */
interface CatalogueItemsInterface
{
    /**
     * @param list<string> $skus
     *
     * @return array<string, CatalogueItem> keyed by SKU; a SKU nobody knows is simply absent
     */
    public function bySku(array $skus): array;
}
