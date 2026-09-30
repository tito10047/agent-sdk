<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Exception;

/**
 * A stored file whose content cannot be produced — purged by retention, missing from
 * the storage, or corrupt. The message is presentable: it says what is wrong in a
 * sentence the agent may pass on.
 */
class FileUnreadableException extends \RuntimeException
{
}
