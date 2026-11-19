<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Platform;

use Leoness\AgentSdk\Exception\FileUnreadableException;

/**
 * One stored file a tool was allowed to reach, content included.
 *
 * How a handle is obtained (and whether the caller was allowed to) is
 * {@see AgentFileAccessInterface}'s problem; by the time one exists, access has been
 * decided.
 */
interface StoredFileHandleInterface
{
    public function id(): int;

    /** The name the person knows the file by. */
    public function originalName(): string;

    /** Lowercase, without the dot — `xlsx`, `pdf`. */
    public function extension(): string;

    /**
     * Runs the callback over a local copy of the content and returns its result. The
     * copy lives only for the duration of the call — nothing is handed out to keep,
     * and whatever the reader raises travels out of here unchanged.
     *
     * @template TResult
     *
     * @param callable(\SplFileInfo): TResult $reader
     *
     * @return TResult
     *
     * @throws FileUnreadableException when the content cannot be produced
     *
     * @param-immediately-invoked-callable $reader
     */
    public function withLocalCopy(callable $reader): mixed;
}
