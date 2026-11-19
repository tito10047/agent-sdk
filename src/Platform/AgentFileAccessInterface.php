<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Platform;

/**
 * How a tool package reaches a stored file — scoped to what the caller is already allowed
 * to see (zadanie §14: only explicitly permitted data).
 *
 * There is deliberately no "find any file by id". A tool acts for the person who called it
 * and sees what that person may read; nothing a package holds lets it enumerate the
 * platform's files. The id a tool is given is the one the person got from `priprav_nahratie`
 * or from another tool's answer — files never travel in arguments themselves.
 */
interface AgentFileAccessInterface
{
    /**
     * The file as the current security context may read it. Null when the file does not
     * exist *or* the caller may not read it; the two are indistinguishable on purpose, so a
     * tool cannot be used to probe which ids exist.
     */
    public function findForCurrentUser(int $fileId): ?StoredFileHandleInterface;
}
