<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Platform;

use Leoness\AgentSdk\Platform\Dto\DeliveredFile;

/**
 * How a tool hands over a file it produced: stored by the platform, owned by the person who
 * called, and reachable only through a signed download link that expires.
 *
 * Files never travel in a tool's answer — not as content, not as a path. What the model gets
 * is the file's ID (for other tools) and a link it gives to the person.
 */
interface FileDeliveryInterface
{
    /**
     * Stores a copy of the file; the original stays the caller's to delete.
     *
     * @param string $originalName the name the person downloads it as, extension included
     *
     * @throws \LogicException when nobody is signed in — a tool reached through MCP never sees that
     */
    public function deliver(\SplFileInfo $file, string $originalName): DeliveredFile;
}
