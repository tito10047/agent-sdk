<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Platform\Dto;

/**
 * A batch the platform has accepted: its id, what it is called, and how big it is.
 *
 * Three values rather than the batch entity, because everything a package does with a
 * batch afterwards it does through the platform — link to the monitor screen, report
 * "398 queued" — and none of that needs the rows. Pausing, cancelling and retrying are
 * operator actions on the platform's own screens and are deliberately not offered here.
 */
final readonly class DispatchedBatch
{
    public function __construct(
        public int $id,
        public string $label,
        public int $size,
    ) {
    }
}
