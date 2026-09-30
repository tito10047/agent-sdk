<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Platform;

/**
 * The two ways an agent package may reach a stored file, each scoped to what the
 * caller was already allowed to see (zadanie §14: only explicitly permitted data).
 *
 * There is deliberately no "find any file by id". A tool acts for the person whose
 * run is executing and sees what that person may read; a `handleFinishedRun()`
 * implementation acts on its own concluded run and sees what that run handed over.
 * Nothing an agent package holds lets it enumerate the platform's files.
 */
interface AgentFileAccessInterface
{
    /**
     * The file as the current security context may read it — what a tool calls during
     * a run. Null when the file does not exist *or* the caller may not read it; the
     * two are indistinguishable on purpose, so a tool cannot be used to probe which
     * ids exist.
     */
    public function findForCurrentUser(int $fileId): ?StoredFileHandleInterface;

    /**
     * A file the given run handed over as its result — what `handleFinishedRun()`
     * calls, with the id taken from {@see \Leoness\AgentSdk\RunFileView::$id}. Null
     * when the file is not among that run's output.
     */
    public function findRunOutput(int $runId, int $fileId): ?StoredFileHandleInterface;
}
