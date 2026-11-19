<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Platform;

/**
 * How a moment is written for the model: ISO 8601 with the offset of the zone the people
 * using the platform live in.
 *
 * One spelling for every tool, the platform's and the packages', so a model never compares
 * a time in UTC with one in local time and gets the hour wrong.
 */
interface ModelTimeInterface
{
    public function forModel(\DateTimeInterface $at): string;
}
