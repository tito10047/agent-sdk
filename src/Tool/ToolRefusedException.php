<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Tool;

use Symfony\AI\Agent\Toolbox\Exception\ToolExecutionExceptionInterface;

/**
 * The tool said no — for a reason the person on the other side can act on.
 *
 * A refusal is not a failure: the permission is missing, the file belongs to somebody else,
 * the record does not exist. Its message is written for the client to pass on, and the MCP
 * server answers it as a tool result marked as an error instead of a protocol error, so
 * Claude or ChatGPT read it as "this did not work, and here is why" rather than as output.
 *
 * Implements the toolbox's execution contract so that the toolbox hands it on untouched
 * instead of wrapping it as an unexpected crash. In the SDK and not in the platform because
 * the platform's own tools and every package's tools refuse the same way — one class, one
 * place where the MCP server turns it into `isError: true`.
 */
final class ToolRefusedException extends \RuntimeException implements ToolExecutionExceptionInterface
{
    /**
     * One sentence for "no such file" and "not yours": an ID is not a way to find out which
     * files exist.
     */
    public static function unknownFile(int $id): self
    {
        return new self(\sprintf('Súbor #%d neexistuje alebo nie je pre používateľa prístupný.', $id));
    }

    public function getToolCallResult(): string
    {
        return $this->getMessage();
    }
}
