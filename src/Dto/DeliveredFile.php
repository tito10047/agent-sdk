<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Dto;

use Leoness\AgentSdk\Platform\StoredFileHandleInterface;
use Leoness\AgentSdk\RunFileView;

/**
 * A file a run has just produced, with everything needed to judge it against its source.
 *
 * The source is the point. Checking a spreadsheet against the rules of a format says
 * whether it *can* be imported; checking it against the order it was made from says
 * whether it *should* be. The second question needs the input of the run, so it travels
 * with the file.
 */
final readonly class DeliveredFile
{
    /**
     * @param array<string, mixed> $inputSnapshot what the run was started with, by field name
     * @param list<RunFileView>    $inputFiles    the attachments the question carried
     */
    public function __construct(
        public int $runId,
        public string $agentCode,
        public StoredFileHandleInterface $file,
        public array $inputSnapshot,
        public array $inputFiles,
    ) {
    }
}
