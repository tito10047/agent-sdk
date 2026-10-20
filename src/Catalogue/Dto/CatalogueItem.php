<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Catalogue\Dto;

/**
 * What the platform knows about a product, looked up by SKU.
 *
 * **Everything here is something a model cannot invent**, which is the whole reason the
 * lookup exists: the e-shop URL on the blue button, the brand, the category used as a
 * card's eyebrow, and the approved parameters. A model asked for a product URL will
 * produce a plausible one, and a plausible URL in an offer sent to a customer is a 404
 * with the company's name on it.
 *
 * **There is no price**, and that is a decision rather than an omission. The product
 * catalogue has no price field; a price record with a validity period is its own piece of
 * work. An offer's prices come from the supplier's price list, and a source naming the
 * catalogue is refused with that explanation.
 */
final readonly class CatalogueItem
{
    /**
     * @param list<CatalogueParameter> $parameters approved parameters only
     * @param string|null              $photo      a `data:` URI, or null
     */
    public function __construct(
        public string $sku,
        public string $name,
        public string $brand = '',
        public string $category = '',
        public ?string $url = null,
        public array $parameters = [],
        public ?string $photo = null,
    ) {
    }
}
